<?php
namespace App\Services;

use App\Model\Banner;
use App\Model\Cinema;
use App\Model\City;
use App\Model\CityArea;
use App\Model\CityFilme;
use App\Model\Filme;
use App\Model\Order;
use App\Model\Show;
use App\Server\moive\MoiveService;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

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
        return Banner::where('is_show',1)->orderBy('sort','desc')->limit(4)->pluck('pic')->toArray();
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
     * 通过城市名称获取热门电影
     * @param $city
     * @return array
     * @author: DHF 2021/4/28 15:42
     */
    public function hotMovie($city):array
    {
        $dd = mb_strpos($city,'市');
        $city = $dd ? mb_substr($city,0,$dd) : $city;
        $cityId = City::where('regionName',$city)->value('cityId');
        $select = ['filmId','pic','name'];
        $list = $this->getMovieList($cityId,'',1,0,$select);
        return $list['list'] ?? [];
    }

    /**
     * 查看电影列表
     * @param $cityId
     * @param $keyword
     * @param $showType 1 热门电影 2 即将上映
     * @return array
     * @author: DHF 2021/4/23 11:49
     */
    public function getMovieList($cityId,$keyword = '',$showType = 1,$max_id = 0,$select = []):array
    {
        $filmIds = CityFilme::where(['cityId'=>$cityId])->pluck('filmId');
        $filme = Filme::where('showStatus',$showType)
            ->whereIn('filmId',$filmIds);
        $keyword && $filme->where('name', 'like', "%{$keyword}%");
        $count = $filme->count();
        $max_id && $filme->where('filmId','<',$max_id);
        empty($select) || $filme->select($select);
        $list = $filme
            ->limit(10)
            ->orderBy('filmId','desc')
            ->get()
            ->toArray();
        return [
            'count'=>$count,
            'list'=>$list,
        ];
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
    public function getCinemaList($cityId,$areaId,$max_id,$keyword = ''):array
    {
        $Cinema = Cinema::where('cityId',$cityId);
        $areaId && $Cinema->where('areaId',$areaId);
        $max_id && $Cinema->where('cinemaId','<',$max_id);
        $keyword && $Cinema->where('cinemaName','like','%'.$keyword.'%');
        $data['count'] = $Cinema->count();
        $data['list'] = $Cinema
            ->limit(10)
            ->orderBy('cinemaId','desc')
            ->get(['cinemaId', 'cityId', 'cinemaName', 'address', 'latitude', 'longitude', 'phone', 'regionName', 'isAcceptSoonOrder'])
            ->toArray();
        return $data;
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
        //电影信息
        $film_id_list = Show::where('cinemaId',$cinemaId)->pluck('filmId')->toArray();
        $filme = Filme::findMany($film_id_list)->toArray();
        isset($filme[0]) && $filmId = $filmId ?: $filme[0]['filmId'];

        $now = date("Y-m-d H:i:s");
        //排期时间筛选
        $show_list = Show::where(['cinemaId'=>$cinemaId,'filmId'=>$filmId])->where('stopSellTime','>',$now)->pluck('showTime')->toArray();
        $dates = [];
        $list = [];
        foreach ($show_list as $show){
            $show_day = substr($show,0,10);
            $list[$show_day][] = $show;
            in_array($show_day,$dates) || $dates[] = $show_day;
        }
        sort($dates);
        if(!in_array($date,$dates)){
            $date = $dates[0] ?? '';
        }
        return [
            'cinema'=>Cinema::find($cinemaId)->toArray(),//影院信息
            'film'=> $filme,
            'dates'=> $dates,
            'list'=> $list[$date]??[],  //当日排期
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
        //座位参数正常可以显示
//        if(isset($seat_list['seats'])){
//            foreach ($seat_list['seats'] as &$seat){
//                list($columnNo,$rowNo) = explode("排",$seat['seatNo']);
//                $seat['columnNo'] = checkNatInt($columnNo);
//                $seat['rowNo'] = checkNatInt($rowNo);
//            }
//        }
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
        $limit = $param['limit'] ?: 10;
        $count = 0;
        $date = $param['date'] ?? date("Y-m-d");
        $cinema_list = [];
        $page = $param['page'] ?: 1;

        $now = date("Y-m-d H:i:s");
        //所有有电影的电影院
        $cinemaIds = Show::where('filmId',$param['filmId'])
            ->where('cityId',$param['cityId'])
            ->where('stopSellTime','>',$now)
            ->whereBetween('stopSellTime',[$date,date("Y-m-d", strtotime('+1 day',strtotime($date)))])
            ->select( 'cinemaId')
            ->groupBy('cinemaId')
            ->pluck('cinemaId')
            ->toArray();

        if($cinemaIds){
            //电影院分页
            $Cinema = Cinema::whereIn('cinemaId',$cinemaIds);
            isset($param['areaId']) && $Cinema->where('areaId',$param['areaId']);
            $count = $Cinema->count();

            //电影院信息
            //有坐标需要运算 TODO::需要转移到 Elasticsearch 处理
            if(!empty($param['latitude']) && !empty($param['longitude'])){

                $cinema_list = $Cinema->orderBy('cinemaId','desc')->get()->toArray();
                foreach ($cinema_list as &$cinema){
                    $cinema['distance'] = Cinema::checkDistance($param['latitude'],$param['longitude'],$cinema['latitude'],$cinema['longitude']);
                }
                array_multisort(array_column($cinema_list,'distance'),$cinema_list);
                array_splice($cinema_list, 0,($page - 1) * $limit);
                array_splice($cinema_list, $limit);
            }else{
                //没有坐标直接查表
                $cinema_list = $Cinema->limit($limit)->skip(($page - 1) * $limit)->get()->toArray();
            }

            $page_cinema_ids = array_column($cinema_list,'cinemaId');
            $show_list = Show::whereIn('cinemaId',$page_cinema_ids)
                ->where('filmId',$param['filmId'])
                ->where('stopSellTime','>',$now)
                ->whereBetween('showTime', [$date,date("Y-m-d", strtotime('+1 day',strtotime($date)))])
                ->orderBy('showTime')
                ->get()
                ->toArray();
            $show_lists =[];
            foreach ($show_list as $show){
                $show_lists[$show['cinemaId']][] = $show;
            }
            foreach ($cinema_list as &$cinema){
                $cinema['scheduleList'] = $show_lists[$cinema['cinemaId']];
            }
        }

        return [
            'count'=>$count,
            'date'=>$date,
            'list'=>$cinema_list,
            'page'=>$page,
        ];
    }

    /**
     * 包含某电影的日期
     * @param $param
     * @return array
     * @author: DHF 2021/4/14 18:13
     */
    public function getShowDate($param):array
    {

        $now = date("Y-m-d H:i:s");
        $days = Db::table('shows')
            ->where(['cityId'=>$param['cityId'],'filmId'=>$param['filmId']])
            ->where('stopSellTime','>',$now)
            ->select( Db::raw("date_format(`showTime`,'%Y-%m-%d') as `day`"))
            ->groupBy('day')
            ->get()
            ->toArray();
        $days = array_column($days,'day');
        sort($days);
        return [
            'filme'=>Filme::find($param['filmId'])->toArray(),
            'days'=>$days
        ];
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
            ->where('stopSellTime','>',date("Y-m-d H:i:s"));
        $filmId && $Show->where('filmId',$filmId);
        $date && $Show->whereBetween('showTime', [$date,date("Y-m-d", strtotime('+1 day',strtotime($date)))]);
        return $Show->get()->toArray();
    }

    /**
     * 地址查询
     * @param $longitude
     * @param $latitude
     * @return array|false
     * @author: DHF 2021/4/28 10:47
     */
    public function getCoder($longitude,$latitude)
    {
        $url = "http://api.map.baidu.com/geocoder?location={$latitude},{$longitude}&output=json&key=RKAfLvwqst37aqV6WvEt12j2Oem4cndM";
        $ref =  curlGet($url);
        if(isset($ref['status']) && $ref['status'] == 'OK'){
            return $ref['result'];
        }
        return false;
    }
}