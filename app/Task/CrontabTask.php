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
        foreach ($city_list as $city){
            $where['cityId'] = $city['cityId'];
            City::updateOrCreate($where, $city);
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
            if(!$city_area_list = $this->moiveService->create()->getCityAreaList(['cityId'=>$city->cityId])){
                return;
            };
            foreach ($city_area_list as $city_area){
                $city_area['cityId'] = $city->cityId;
                CityArea::updateOrCreate(['areaId'=>$city_area['areaId']], $city_area);
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
        $city_area_list = CityArea::all()->toArray();
        $city_area_list = array_combine(array_column($city_area_list,'areaName'),array_column($city_area_list,'areaId'));
        foreach ($city_list as $city){
            co(function () use ($city,$city_area_list) {
                if(!$cinema_list = $this->moiveService->create()->getCinemaList(['cityId'=>$city->cityId])){
                    return;
                };
                foreach ($cinema_list as $cinema){
                    $cinema['cityId'] = $city->cityId;
                    $cinema['areaId'] = $city_area_list[$cinema['regionName']] ?? 0;
                    Cinema::updateOrCreate(['cinemaId'=>$cinema['cinemaId']], $cinema);
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
            co(function () use ($id) {
                if(!$hot_list = $this->moiveService->create()->getHotList(['cityId'=>$id])){
                    return true;
                }
                if(!$soon_list = $this->moiveService->create()->getSoonList(['cityId'=>$id])){
                    return true;
                }
                $list = array_merge($hot_list,$soon_list);
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
        foreach ($schedule_list as $schedule){
            if(!in_array($schedule['showId'],$show_id_list)){
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
}
