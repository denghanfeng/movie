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

    /**
     * 获取订单列表
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/23 14:03
     */
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


    public function refund()
    {
        $this->validated([
            'thirdOrderId' => 'required|integer',
        ]);
        $thirdOrderId = $this->request->input('thirdOrderId');
        return $this->success($this->orderService->refund((int)$thirdOrderId));
    }

}
