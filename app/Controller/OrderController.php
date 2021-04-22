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

use App\Services\OrderService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Middleware\AuthMiddleware;

/**
 * @AutoController()
 * @Middlewares({
 *     @Middleware(AuthMiddleware::class)
 * })
 */
class OrderController extends AbstractController
{
    /**
     * @Inject()
     * @var OrderService
     */
    protected $orderService;

    /**
     * 生成订单
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/16 16:02
     */
    public function create()
    {
        $this->validated([
            'showId' => 'required',
            'seat' => 'required',
            'cinemaId' => 'required',
            'filmId' => 'required',
            'acceptChangeSeat' => 'integer',
            'reservedPhone' => 'required',
            'payType' => 'required|integer',
        ]);
        $param = $this->request->all();
        return $this->success($this->orderService->create($param));
    }

    /**
     * 获取单个订单信息
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/16 16:02
     */
    public function one()
    {
        $this->validated([
            'thirdOrderId' => 'required|integer',
        ]);
        $thirdOrderId = $this->request->input('thirdOrderId');
        return $this->success($this->orderService->one((int)$thirdOrderId));
    }


    public function list()
    {
        $this->validated([
            'orderStatus' => 'integer',
            'max_id' => 'integer',
        ]);
        $orderStatus = $this->request->input('orderStatus');
        $max_id = $this->request->input('max_id');
        return $this->success($this->orderService->list((int)$orderStatus,(int)$max_id));
    }

    /**
     * 下单回调
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/21 15:34
     */
    public function notify()
    {
        $this->validated([
            'sign' => 'required',
            'appKey' => 'required',
        ]);
        return $this->response->json([
            'code' => 200,
            'message' => '请求成功',
            'success' => $this->orderService->notify($this->request->all())
        ]);
    }


    public function orderHandle(){
        $data['thirdOrderId'] = $this->request->post('thirdOrderId', '');
        $data['eventName'] = $this->request->post('eventName', '');
        return $this->resultMsg(200,$this->IndexService->orderHandle($data));
    }

    public function orderQuery(){
        $data['thirdOrderId'] = $this->request->post('thirdOrderId', '');
        return $this->resultMsg(200,$this->IndexService->orderQuery($data));
    }

    public function userInfo(){
        return $this->resultMsg(200,$this->IndexService->userInfo());
    }

    public function soonOrder(){
        $data['showId'] = $this->request->post('showId', '');
        $data['seat'] = $this->request->post('seat', '');
        $data['reservedPhone'] = $this->request->post('reservedPhone', '');
        $data['acceptChangeSeat'] = $this->request->post('acceptChangeSeat', 0);
        $data['seatId'] = $this->request->post('seatId', '');
        $data['seatNo'] = $this->request->post('seatNo', '');
        $data['netPrice'] = $this->request->post('netPrice', '');
        $data['testType'] = $this->request->post('testType', '');

        return $this->resultMsg(200,$this->IndexService->soonOrder($data));
    }
}
