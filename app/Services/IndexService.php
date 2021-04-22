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
     * @Inject()
     * @var CinemaService
     */
    protected $cinemaService;


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

    public function getMovieList($cityId,$keyword,$showType)
    {
        switch ($showType){
            case 2:
                return $this->cinemaService->getSoonList($cityId,$keyword);
            default:
                return $this->cinemaService->getHotList($cityId,$keyword);
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
     * 影院页面
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

        $film_id_list = Show::where('cinemaId',$cinemaId)->pluck('filmId')->toArray();
        $filme = Filme::findMany($film_id_list)->toArray();
        isset($filme[0]) && $filmId = $filmId ?: $filme[0]['filmId'];

        $show_list = Show::where(['cinemaId'=>$cinemaId,'filmId'=>$filmId])->pluck('showTime')->toArray();
        $dates = [];
        foreach ($show_list as $show){
            $show_day = substr($show,10);
            in_array($show_day,$dates) || $dates[] = $show_day;
        }
        isset($dates[0]) && $date = $dates[0];
        return [
            'cinema'=>Cinema::find($cinemaId)->toArray(),
            'film'=> $filme,
            'dates'=> $dates,
            'list'=> $this->cinemaService->getSchedule($cinemaId,$filmId,$date),
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
        return $this->moiveService->create()->getSeat(['showId'=>$showId]);
    }

    /**
     * 包含某电影的影院
     * @param $param
     * @return array
     * @author: DHF 2021/4/14 18:09
     */
    public function getShowList($param):array
    {
        return $this->moiveService->create()->getShowList($param);
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

}