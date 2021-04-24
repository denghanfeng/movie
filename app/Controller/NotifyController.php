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

use App\Services\CinemaService;
use App\Services\OrderService;
use App\Services\PayService;
use App\Task\CrontabTask;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class NotifyController extends AbstractController
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
     * 下单回调
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/21 15:34
     */
    public function order()
    {
        $param = $this->request->all();
        $this->logger->alert(json_encode($param));
        return $this->response->json([
            'code' => 200,
            'message' => '请求成功',
            'success' => $this->orderService->notify($param)
        ]);
    }

    /**
     * 支付回调
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/23 17:16
     */
    public function pay()
    {
        $param = $this->request->all();
        $this->logger->alert(json_encode($param));
        return $this->success($this->payService->notify($param));
    }

    /**
     * 全部同步
     * @author: DHF 2021/4/23 18:36
     */
    public function all()
    {
        (new CrontabTask)->updateAll();
        return $this->success();
    }
}
