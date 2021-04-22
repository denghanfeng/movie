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

class CinemaService extends BaseService
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
     * @param $cinemaId
     * @param $filmId
     * @param $date
     * @return array
     * @author: DHF 2021/4/21 14:49
     */
    public function shows($cinemaId,$filmId,$date):array
    {
        if(!(new CrontabTask)->updateShow($cinemaId)){
            throw new RuntimeException('信息更新失败',2005);
        };
    }

    /**
     * 场次排期
     * @param $cinemaId
     * @return array
     * @author: DHF 2021/4/14 17:10
     */
    public function getSchedule($cinemaId,$filmId,$date):array
    {
        if(!(new CrontabTask)->updateShow($cinemaId)){
            throw new RuntimeException('信息更新失败',2005);
        };
        $Show = Show::where('cinemaId',$cinemaId);
        $filmId && $Show->where('filmId',$filmId);
        $date && $Show->whereBetween('showTime', [$date,date("Y-m-d", strtotime('+1 day',strtotime($date)))]);

        return $Show->get()->toArray();
    }

    /**
     * 获取电影信息
     * @param $filmId
     * @return array
     * @author: DHF 2021/4/21 14:47
     */
    public function getFilm($filmId):array
    {
        return Filme::find($filmId)->toArray();
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