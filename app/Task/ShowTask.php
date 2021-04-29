<?php
namespace App\Task;

use App\Model\Cinema;
use App\Model\Show;
use App\Server\moive\MoiveService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Task\Annotation\Task;

class ShowTask extends BasickTask
{
    /**
     * @Inject()
     * @var MoiveService
     */
    protected $moiveService;

    /**
     * @Task
     * @param $cinemaId
     * @param $cityId
     * @return bool
     * @author: DHF 2021/4/1 16:59
     */
    public function create($cinemaId,$cityId)
    {
        co(function () use ($cinemaId,$cityId){
            if (!$schedule = $this->moiveService->create()->getScheduleList(['cinemaId' => $cinemaId])) {
                return false;
            }
            $show_id_list = Show::where(['cinemaId' => $cinemaId])->pluck('showId')->toArray();
            $show_list = $schedule['list'];
            $discountRule = $schedule['discountRule'] ?? [];
            if (!empty($discountRule)) {
                $cinema = Cinema::find($cinemaId);
                $cinema && $cinema->update($discountRule);
            }
            $today = date("Y-m-d H:i:s");
            foreach ($show_list as $show) {
                if (!in_array($show['showId'], $show_id_list) && $show['showTime'] >= $today) {
                    $show['cinemaId'] = $cinemaId;
                    $show['cityId'] = $cityId;
                    $show['payPrice'] = $this->moiveService->create()->getCommission($show['netPrice'], $cinemaId, 1);
                    Show::create($show);
                }
            }
        });
    }
}