<?php
namespace App\Services;

use Hyperf\Di\Annotation\Inject;

class UserService extends BaseService
{

    /**
     * @Inject()
     * @var AuthService
     */
    protected $authService;

    /**
     * 获取手机号
     * @return array|mixed|string
     * @author: DHF 2021/4/24 17:28
     */
    public function phone()
    {
        return $this->authService->getUser('phone');
    }

}