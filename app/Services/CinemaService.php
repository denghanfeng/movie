<?php
namespace App\Services;

use App\Server\moive\MoiveService;
use Hyperf\Di\Annotation\Inject;

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

    public function userInfo():array
    {
        return $this->moiveService->create()->userInfo();
    }

}