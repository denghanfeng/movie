<?php

declare(strict_types=1);

namespace App\JsonRpc;

interface YzApiInterface
{
    /**
     * 获取用户信息
     * @param string $openid
     * @param int $wx_id
     * @return array
     * @author: DHF 2021/4/13 14:47
     */
    public function getUser(string $openid,int $wx_id): array;
}
