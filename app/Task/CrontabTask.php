<?php
namespace App\Task;

use App\Model\Cinema;
use App\Model\City;
use App\Model\CityArea;
use App\Model\CityFilme;
use App\Model\Filme;
use App\Model\Show;
use App\Model\StakLog;
use App\Server\moive\MoiveService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use App\Task\ShowTask;

class CrontabTask
{
    /**
     * @Inject()
     * @var StdoutLoggerInterface
     */
    private $logger;

    /**
     * @Inject()
     * @var MoiveService
     */
    protected $moiveService;

    /**
     * @Crontab(rule="0 0 0 * * *", memo="updateAll")
     */
    public function updateAll()
    {

        $StakLog = StakLog::create(['action'=>'updateAll','start_time'=>date('Y-m-d H:i:s')]);
        $this->updateCitys();
        $this->updateCityArea();
        $this->delShow();
        $StakLog->update(['end_time'=>date('Y-m-d H:i:s')]);
    }

    /**
     * 同步城市
     * @return bool
     * @author: DHF 2021/4/14 17:33
     */
    public function updateCitys()
    {
        if(!$city_list = $this->moiveService->create()->getCityList()){
            return false;
        };
        $city_is_list = City::all()->pluck('cityId')->toArray();
        foreach ($city_list as $city){
            $where['cityId'] = $city['cityId'];
            in_array($city['cityId'],$city_is_list) || City::updateOrCreate($where, $city);
        }
        return true;
    }

    /**
     * 同步城市下级区域
     * @return bool
     * @author: DHF 2021/4/14 15:19
     */
    public function updateCityArea()
    {
        $city_list = City::all(['cityId']);
        foreach ($city_list as $city){
            if (!$city_area_list = $this->moiveService->create()->getCityAreaList(['cityId' => $city->cityId])) {
                return false;
            };
            $city_area_id_list = CityArea::where(['cityId' => $city->cityId])->pluck('areaId')->toArray();
            foreach ($city_area_list as $city_area) {
                $city_area['cityId'] = $city->cityId;
                in_array($city_area['areaId'], $city_area_id_list) || CityArea::updateOrCreate(['areaId' => $city_area['areaId']], $city_area);
            }
        }
        return true;
    }

    /**
     * 同步场次信息
     * @param string $cinemaId
     * @param string $showId
     * @return Show|bool|\Hyperf\Database\Model\Model
     * @author: DHF 2021/4/21 14:01
     */
    public function updateShow(string $cinemaId,$showId = '')
    {
        $show_id_list = Show::where(['cinemaId'=>$cinemaId])->pluck('showId')->toArray();
        if($showId && in_array($showId,$show_id_list)){
            return Show::find($showId);
        }
        if(!$schedule = $this->moiveService->create()->getScheduleList(['cinemaId'=>$cinemaId])){
            return false;
        }
        $schedule_list = $schedule['list'];

        $today = date("Y-m-d H:i:s");
        foreach ($schedule_list as $schedule){
            if(!in_array($schedule['showId'],$show_id_list) && $schedule['showTime'] >= $today){
                $schedule['cinemaId'] = $cinemaId;
                $schedule['payPrice'] = $this->moiveService->create()->getCommission($schedule['netPrice'],$cinemaId,1);
                $show = Show::updateOrCreate(['showId'=>$schedule['showId']], $schedule);
                //如果指定更新则更新后直接退出
                if($showId && $showId == $schedule['showId']) {
                    return $show;
                }
            }
        }
        return !$showId;
    }

    /**
     * 清除今日之前数据
     * @author: DHF 2021/4/24 11:17
     */
    public function delShow($limit = 1000)
    {
        $today = date("Y-m-d");
        $delect_ids = Show::where('showTime','<',$today)->limit($limit)->pluck('showId');
        Show::destroy($delect_ids);
        if(count($delect_ids) == $limit){
            $this->delShow();
        };
    }

    /**
     * 同步电影信息
     * @Crontab(rule="0 30 0 * * *", memo="updateFilme")
     * @return bool
     * @author: DHF 2021/4/20 17:15
     */
    public function updateFilme()
    {
        $StakLog = StakLog::create(['action'=>'updateFilme','start_time'=>date('Y-m-d H:i:s')]);
        $city_list = City::pluck('cityId')->toArray();
        foreach ($city_list as $cityId){
            if(!$hot_list = $this->moiveService->create()->getHotList(['cityId'=>$cityId])){
                return true;
            }
            if(!$soon_list = $this->moiveService->create()->getSoonList(['cityId'=>$cityId])){
                return true;
            }
            $list = array_merge($hot_list,$soon_list);
            co(function () use ($list,$cityId) {
                $filme_list = Filme::where('cityId',$cityId)->pluck('filmId')->toArray();
                Db::table('city_filmes')->where('cityId', $cityId)->delete();
                foreach ($list as $filme){
                    $filme['like'] = (int)$filme['likeNum'];
                    $filme['grade'] = (int)$filme['grade'];
                    $filme['duration'] = (int)$filme['duration'];
                    $filme['showStatus'] = (int)$filme['showStatus'];
                    empty($filme['publishDate']) && $filme['publishDate'] = '0000-00-00 00:00:00';
                    if(!in_array($filme['filmId'],$filme_list)){
                        Filme::create($filme);
                        $filme_list[] = $filme['filmId'];
                    }
                    $city_file = ['filmId'=>$filme['filmId'],'cityId'=>$cityId];
                    CityFilme::create($city_file);
                }
            });
        }
        $StakLog->update(['end_time'=>date('Y-m-d H:i:s')]);
        return true;
    }


    /**
     * 同步电影院
     * @Crontab(rule="0 0 1 * * *", memo="updateCinema")
     * @return bool
     * @author: DHF 2021/4/14 15:44
     */
    public function updateCinema()
    {
        $StakLog = StakLog::create(['action'=>'updateCinema','start_time'=>date('Y-m-d H:i:s')]);
        $city_list = City::all(['cityId']);
        $city_area_list = CityArea::all();
        $city_area_array = [];
        foreach ($city_area_list as $city_area){
            isset($city_area_array[$city_area->cityId]) || $city_area_array[$city_area->cityId] = [];
            $city_area_array[$city_area->cityId][$city_area->areaName] = $city_area->areaId;
        }
        foreach ($city_list as $city){
            if(!$cinema_list = $this->moiveService->create()->getCinemaList(['cityId'=>$city->cityId])){
                continue;
            };
            co(function () use ($cinema_list,$city,$city_area_array) {
                $cinema_id_list = Cinema::where(['cityId' => $city->cityId])->pluck('cinemaId')->toArray();
                foreach ($cinema_list as $cinema) {
                    $cinema['cityId'] = $city->cityId;
                    $cinema['areaId'] = $city_area_array[$cinema['cityId']][$cinema['regionName']] ?? 0;
                    in_array($cinema['cinemaId'], $cinema_id_list) || Cinema::updateOrCreate(['cinemaId' => $cinema['cinemaId']], $cinema);
                }
            });
        }
        $StakLog->update(['end_time'=>date('Y-m-d H:i:s')]);
        return true;
    }

    /**
     * 同步所有场次
     * @Crontab(rule="0 0 2 * * *", memo="updateAllShow")
     * @param int $limit
     * @return array|bool
     * @author: DHF 2021/4/28 20:08
     */
    public function updateAllShow($limit = 100)
    {
        $StakLog = StakLog::create(['action'=>'updateAllShow','start_time'=>date('Y-m-d H:i:s')]);
        $page = 1;
        while($page)
        {
            $cinema_id_list = Cinema::limit($limit)->skip($limit*($page-1))->get(['cinemaId','cityId']);

            foreach ($cinema_id_list as $cinema){
                ApplicationContext::getContainer()->get(ShowTask::class)->create($cinema->cinemaId,$cinema->cityId);
            }
            if(count($cinema_id_list) == $limit){
                $page++;
            }else{
                $page = 0;
            }
        }
        $StakLog->update(['end_time'=>date('Y-m-d H:i:s')]);
    }
}
