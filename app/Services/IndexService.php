<?php
namespace App\Services;

use App\Model\Cinema;
use App\Model\City;
use App\Model\CityArea;
use App\Model\Filme;
use App\Model\Order;
use App\Model\Show;
use App\Server\moive\MoiveService;
use App\Task\CrontabTask;
use Hyperf\Di\Annotation\Inject;
use RuntimeException;

class IndexService extends BaseService
{

    /**
     * @Inject()
     * @var MoiveService
     */
    protected $moiveService;

    /**
     * @Inject()
     * @var AuthService
     */
    protected $authService;


    /**
     * banner信息
     * @return array
     * @author: DHF 2021/4/13 18:59
     */
    public function getBanner(): array
    {
        return Filme::orderBy('grade','desc')->limit(4)->pluck('pic')->toArray();
    }

    /**
     * 获取订单信息
     * @return array
     * @author: DHF 2021/4/21 11:50
     */
    public function getTicket()
    {
        return Order::where(['orderStatus'=>Order::STATUS_STATEMENT])->value('thirdOrderId');
    }

    /**
     * 查看电影列表
     * @param $cityId
     * @param $keyword
     * @param $showType 1 热门电影 2 即将上映
     * @return array|false|mixed
     * @author: DHF 2021/4/23 11:49
     */
    public function getMovieList($cityId,$keyword,$showType)
    {
        switch ($showType){
            case 2:
                return $this->getSoonList($cityId,$keyword);
            default:
                return $this->getHotList($cityId,$keyword);
        }
    }

    /**
     * 城市信息
     * @return array
     * @author: DHF 2021/4/13 18:59
     */
    public function getCityList(): array
    {
        return City::all()->toArray();
    }

    /**
     * 下级城市信息
     * @param int $cityId
     * @return array
     * @author: DHF 2021/4/13 18:59
     */
    public function getCityAreaList(int $cityId): array
    {
        return CityArea::query()->where(['cityId'=>$cityId])->get(['areaId','areaName'])->toArray();
    }


    /**
     * 查找电影院
     * @param $cityId
     * @param $areaId
     * @param $max_id
     * @return array
     * @author: DHF 2021/4/14 15:48
     */
    public function getCinemaList($cityId,$areaId,$max_id):array
    {
        $Cinema = Cinema::where('cityId',$cityId);
        $areaId && $Cinema->where('areaId',$areaId);
        $max_id && $Cinema->where('cinemaId','<',$max_id);
        return $Cinema
            ->limit(10)
            ->orderBy('cinemaId','desc')
            ->get(['cinemaId', 'cityId', 'cinemaName', 'address', 'latitude', 'longitude', 'phone', 'regionName', 'isAcceptSoonOrder'])
            ->toArray();
    }

    /**
     * 排期页面
     * @param $cinemaId //影院
     * @param $filmId //电影
     * @param $date //时间
     * @return array
     * @author: DHF 2021/4/21 14:49
     */
    public function shows($cinemaId,$filmId,$date):array
    {
        if(!(new CrontabTask)->updateShow($cinemaId)){
            throw new RuntimeException('信息更新失败',2005);
        };
        //电影信息
        $film_id_list = Show::where('cinemaId',$cinemaId)->pluck('filmId')->toArray();
        $filme = Filme::findMany($film_id_list)->toArray();
        isset($filme[0]) && $filmId = $filmId ?: $filme[0]['filmId'];

        $now = date("Y-m-d H:i:s");
        //排期时间筛选
        $show_list = Show::where(['cinemaId'=>$cinemaId,'filmId'=>$filmId])->where('showTime','>',$now)->pluck('showTime')->toArray();
        $dates = [];

        foreach ($show_list as $show){
            $show_day = substr($show,0,10);
            in_array($show_day,$dates) || $dates[] = $show_day;
        }
        sort($dates);
        if(!in_array($date,$dates)){
            $date = $dates[0] ?? '';
        }

        $list = $date ? $this->getSchedule($cinemaId,$filmId,$date) : [];

        return [
            'cinema'=>Cinema::find($cinemaId)->toArray(),//影院信息
            'film'=> $filme,
            'dates'=> $dates,
            'list'=> $list,//当日排期
        ];
    }

    /**
     * 座位
     * @param $showId
     * @return array
     * @author: DHF 2021/4/14 18:04
     */
    public function getSeat($showId):array
    {
        $seat_list =  $this->moiveService->create()->getSeat(['showId'=>$showId]);
        if(isset($seat_list['seats'])){
            foreach ($seat_list['seats'] as &$seat){
                list($columnNo,$rowNo) = explode("排",$seat['seatNo']);
                $seat['columnNo'] = checkNatInt($columnNo);
                $seat['rowNo'] = checkNatInt($rowNo);
            }
        }
        return $seat_list;
    }

    /**
     * 包含某电影的影院
     * @param $param
     * @return array
     * @author: DHF 2021/4/14 18:09
     */
    public function getShowList($param):array
    {
        $param['limit'] = $param['limit'] ?? 10;
        if(!$cinema_list =  $this->moiveService->create()->getShowList($param)){
            return [];
        };
        foreach ($cinema_list as &$cinema){
            (new CrontabTask)->updateShow($cinema['cinemaId']);
            $cinema['scheduleList'] = $this->getSchedule($cinema['cinemaId'],$param['filmId'],$param['date']);
        }
        return $cinema_list;
    }

    /**
     * 包含某电影的日期
     * @param $param
     * @return array
     * @author: DHF 2021/4/14 18:13
     */
    public function getShowDate($param):array
    {
        return $this->moiveService->create()->getShowDate($param);
    }

    /**
     * 获取热门电影
     * @param $cityId
     * @param $keyword
     * @return array
     * @author: DHF 2021/4/14 14:47
     */
    public function getHotList($cityId,$keyword): array
    {
        $moive_list = $this->moiveService->create()->getHotList(['cityId'=>$cityId]);
        if($keyword){
            $moive_list = array_filter($moive_list,function($moive)use($keyword){
                return strpos($moive['name'],$keyword);
            });
        }
        return $moive_list;
    }

    /**
     * 即将上映电影
     * @param $cityId
     * @param $keyword
     * @author: DHF 2021/4/14 14:55
     */
    public function getSoonList($cityId,$keyword)
    {
        $moive_list = $this->moiveService->create()->getSoonList(['cityId'=>$cityId]);
        if($keyword){
            $moive_list = array_filter($moive_list,function($moive)use($keyword){
                return strpos($moive['name'],$keyword);
            });
        }
        return $moive_list;
    }

    /**
     * 场次排期
     * @param $cinemaId
     * @return array
     * @author: DHF 2021/4/14 17:10
     */
    public function getSchedule($cinemaId,$filmId,$date):array
    {
        $Show = Show::where('cinemaId',$cinemaId)
            ->where('showTime','>',date("Y-m-d H:i:s"));
        $filmId && $Show->where('filmId',$filmId);
        $date && $Show->whereBetween('showTime', [$date,date("Y-m-d", strtotime('+1 day',strtotime($date)))]);
        return $Show->get()->toArray();
    }
}