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

use App\Services\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class UserController extends AbstractController
{
    /**
     * @Inject()
     * @var UserService
     */
    protected $userService;

    /**
     * 获取手机号
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/24 17:29
     */
    public function phone()
    {
        return $this->success($this->userService->phone());
    }
}
