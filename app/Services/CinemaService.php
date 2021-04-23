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

    public function notifyPay()
    {
        return false;
    }


    public function orderHandle($param):array
    {
        return $this->moiveService->create()->orderHandle($param);
    }

    public function orderQuery($param):array
    {
        return $this->moiveService->create()->orderQuery($param);
    }

    public function userInfo($param):array
    {
        return $this->moiveService->create()->userInfo($param);
    }

}