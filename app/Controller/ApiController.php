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

use App\Services\IndexService;
use App\Services\OrderService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class ApiController extends AbstractController
{
    /**
     * @Inject()
     * @var OrderService
     */
    protected $orderService;
    /**
     * @Inject()
     * @var IndexService
     */
    protected $indexService;

    /**
     * 订单信息
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/26 14:25
     */
    public function order()
    {
        $param = $this->request->all();
        $this->validated([
            'limit' => 'integer',
            'page' => 'integer',
            'orderStatus' => 'integer',
            'uid' => 'integer',
        ]);
        return $this->success($this->orderService->apiList($param));
    }

    /**
     * 获取热门电影信息
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/27 13:55
     */
    public function hotMovie()
    {
        $city = $this->request->input('city', '');
        $this->logger->alert(' hotMovie '.date("Y-m-d H:i:s").$city);
        return $this->success($this->indexService->hotMovie($city));
    }

}
