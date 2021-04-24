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
class IndexController extends AbstractController
{
    /**
     * @Inject()
     * @var IndexService
     */
    protected $indexService;

    /**
     * banner信息
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/21 11:46
     */
    public function banner()
    {
        return $this->success($this->indexService->getBanner());
    }

    /**
     * 等待取票信息
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/21 11:56
     */
    public function ticket()
    {
        return $this->success($this->indexService->getTicket());
    }

    /**
     * 获取热映电影
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/14 11:57
     */
    public function movie()
    {
        $this->validated([
            'cityId' => 'required|integer',
        ]);
        $cityId = $this->request->input('cityId', 0);
        $keyword = $this->request->input('keyword', '');
        $showType = $this->request->input('showType', 1);

        return $this->success($this->indexService->getMovieList($cityId,$keyword,$showType));
    }

    /**
     * 获取城市列表
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/13 17:58
     */
    public function cityList()
    {
        return $this->success($this->indexService->getCityList());
    }

    /**
     * 获取城市下级列表
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/14 17:36
     */
    public function cityArea()
    {
        $this->validated([
            'cityId' => 'required|integer',
        ]);
        $cityId = $this->request->input('cityId', 0);
        return $this->success($this->indexService->getCityAreaList((int)$cityId));
    }

    /**
     * 获取影院列表
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/14 17:35
     */
    public function cinema()
    {
        $this->validated([
            'cityId' => 'required|integer',
            'areaId' => 'integer',
            'max_id' => 'integer',
        ]);
        $cityId = $this->request->input('cityId', 0);
        $areaId = $this->request->input('areaId', 0);
        $max_id = $this->request->input('max_id', 0);
        return $this->success($this->indexService->getCinemaList($cityId,$areaId,$max_id));
    }

    /**
     * 场次排期
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/14 18:03
     */
    public function shows()
    {
        $this->validated([
            'cinemaId' => 'required|integer',
            'filmId' => 'integer',
        ]);
        $cinemaId = $this->request->input('cinemaId', 0);
        $filmId = $this->request->input('filmId', 0);
        $date = $this->request->input('date', 0);

        return $this->success($this->indexService->shows($cinemaId,$filmId,$date));
    }

    /**
     * 座位
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/14 18:04
     */
    public function seat()
    {
        $this->validated([
            'showId' => 'required'
        ]);
        $showId = $this->request->input('showId', '');
        return $this->success($this->indexService->getSeat($showId));
    }

    /**
     * 包含某电影的影院
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/14 18:09
     */
    public function showList()
    {
        $this->validated([
            'filmId' => 'required|integer',
            'cityId' => 'required|integer',
            'date' => 'required|string',
            'page' => 'integer',
            'limit' => 'integer',
            'latitude' => 'double',
            'longitude' => 'double',
        ]);
        $param = $this->request->all();
        return $this->success($this->indexService->getShowList($param));
    }

    /**
     * 包含某电影的日期
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/23 15:46
     */
    public function showDate()
    {
        $this->validated([
            'filmId' => 'required|integer',
            'cityId' => 'required|integer',
        ]);
        $param = $this->request->all();
        return $this->success($this->indexService->getShowDate($param));
    }
}
