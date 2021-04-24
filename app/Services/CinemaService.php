<?php
namespace App\Services;

use App\Log;
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

    public function notifyPay()
    {
        return false;
    }


    public function orderHandle($param):array
    {
        $aa =  $this->moiveService->create()->orderHandle($param);
        Log::get()->debug(json_encode($aa));
        return $aa;
    }

    public function orderQuery($param):array
    {
        return $this->moiveService->create()->orderQuery($param);
    }

    public function userInfo():array
    {
        return $this->moiveService->create()->userInfo();
    }

}