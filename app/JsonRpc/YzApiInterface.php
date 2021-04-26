<?php

declare(strict_types=1);

namespace App\JsonRpc;

interface YzApiInterface
{
    /**
     * 获取用户信息
     * @param int $uid
     * @param int $wx_id
     * @return array
     * @author: DHF 2021/4/13 14:47
     */
    public function getUser(int $uid,int $wx_id): array;
    /**
     * 获取订单信息
     * @param string $out_trade_no
     * @return array
     * @author: DHF 2021/4/26 10:23
     */
    public function getOrder(string $out_trade_no): array;
}
