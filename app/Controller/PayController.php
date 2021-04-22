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
use App\Services\PayService;
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
class PayController extends AbstractController
{
    /**
     * @Inject()
     * @var PayService
     */
    protected $payService;

    public function notify(){
        $param = $this->request->all();
        pre($param);
        return $this->success($this->payService->notify($param));
    }

}
