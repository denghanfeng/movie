<?php
namespace App\Task;

use App\Model\Cinema;
use App\Model\City;
use App\Model\CityArea;
use App\Model\Filme;
use App\Model\Show;
use App\Server\moive\MoiveService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;


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
        $this->updateCitys();
        $this->updateCityArea();
        $this->updateCinema();
        $this->updateFilme();
        $this->delShow();
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
     * 同步电影院
     * @return bool
     * @author: DHF 2021/4/14 15:44
     */
    public function updateCinema()
    {
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
            co(function () use ($cinema_list,$city) {
                $cinema_id_list = Cinema::where(['cityId' => $city->cityId])->pluck('cinemaId')->toArray();
                foreach ($cinema_list as $cinema) {
                    $cinema['cityId'] = $city->cityId;
                    $cinema['areaId'] = $city_area_array[$cinema['cityId']][$cinema['regionName']] ?? 0;
                    $cinema['cityId'] == 8 && $this->logger->alert(json_encode($cinema));
                    in_array($cinema['cinemaId'], $cinema_id_list) || Cinema::updateOrCreate(['cinemaId' => $cinema['cinemaId']], $cinema);
                }
            });
        }
        return true;
    }

    /**
     * 同步电影信息
     * @return bool
     * @author: DHF 2021/4/20 17:15
     */
    public function updateFilme()
    {
        for ($id = 1;$id <= 10;$id++){

                if(!$hot_list = $this->moiveService->create()->getHotList(['cityId'=>$id])){
                    return true;
                }
                if(!$soon_list = $this->moiveService->create()->getSoonList(['cityId'=>$id])){
                    return true;
                }
                $list = array_merge($hot_list,$soon_list);
            co(function () use ($list) {
                foreach ($list as $filme){
                    $filme['like'] = $filme['likeNum'];
                    $filme['grade'] = (int)$filme['grade'];
                    $filme['duration'] = (int)$filme['duration'];
                    $filme['showStatus'] = (int)$filme['showStatus'];
                    Filme::updateOrCreate(['filmId'=>$filme['filmId']], $filme);
                }
            });
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
        if(!$schedule_list = $this->moiveService->create()->getScheduleList(['cinemaId'=>$cinemaId])){
            return false;
        }
        $today = date("Y-m-d");
        foreach ($schedule_list as $schedule){
            if(!in_array($schedule['showId'],$show_id_list) && $schedule['showTime'] >= $today){
                $schedule['cinemaId'] = $cinemaId;
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
}
