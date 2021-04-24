<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use App\Server\moive\MoiveService;
use App\Services\CinemaService;
use App\Services\OrderService;
use App\Services\PayService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class TestController extends AbstractController
{
    /**
     * @Inject()
     * @var OrderService
     */
    protected $orderService;
    /**
     * @Inject()
     * @var PayService
     */
    protected $payService;
    /**
     * @Inject()
     * @var CinemaService
     */
    protected $cinemaService;
    /**
     * @Inject()
     * @var MoiveService
     */
    protected $moiveService;

    public function cinema(){
        $cityId = $this->request->input('cityId', 70);
        return $this->success($this->moiveService->create()->getCinemaList(['cityId'=>$cityId]));
    }

    public function city(){
        return $this->success($this->moiveService->create()->getCityList());
    }

    public function cityarea(){
        $cityId = $this->request->input('cityId', 70);
        return $this->success($this->moiveService->create()->getCityAreaList(['cityId' => $cityId]));
    }

    public function seat(){
        $showId = $this->request->input('showId', 866073168);
        return $this->success($this->moiveService->create()->getSeat(['showId'=>$showId]));
    }

    public function orderHandle(){
        $data['thirdOrderId'] = $this->request->post('thirdOrderId', '');
        $data['eventName'] = $this->request->post('eventName', '');
        return $this->success($this->cinemaService->orderHandle($data));
    }

    public function orderQuery(){
        $data['thirdOrderId'] = $this->request->post('thirdOrderId', '');
        return $this->success($this->cinemaService->orderQuery($data));
    }

    public function userInfo()
    {
        return $this->success($this->cinemaService->userInfo());
    }
}
