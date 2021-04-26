<?php


namespace App\JsonRpc;

use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;

/**
 * 注意，如希望通过服务中心来管理服务，需在注解内增加 publishTo 属性
 * @RpcService(name="YzApiService", protocol="jsonrpc", server="jsonrpc")
 */
class YzApiService implements YzApiInterface
{

    /**
     * 获取用户信息
     * @param int $uid
     * @param int $wx_id
     * @return array
     * @author: DHF 2021/4/13 14:47
     */
    public function getUser(int $uid,int $wx_id): array
    {
        $wx_list =  Db::connection('yz')->select('
SELECT accounts_id 
FROM wx_auth_info
WHERE id = ?
;',[$wx_id]);

        $user_list =  Db::connection('yz')->select('
SELECT * 
FROM wx_user_info
WHERE user_id = ?
AND wx_id = ?
;',[$uid,$wx_id]);
        if(empty($user_list[0]) || empty($wx_list[0])){
            return [];
        }
        $user = $user_list[0];
        $user->accounts_id = $wx_list[0]->accounts_id;
        return (array)$user;
    }

    /**
     * 获取订单信息
     * @param string $out_trade_no
     * @return array
     * @author: DHF 2021/4/26 10:25
     */
    public function getOrder(string $out_trade_no):array
    {
        $order =  Db::connection('yz')->select('
SELECT * 
FROM api_pay_order
WHERE out_trade_no = ?
;',[$out_trade_no]);
        if(empty($order[0])){
            return [];
        }
        return (array)$order[0];
    }
}
