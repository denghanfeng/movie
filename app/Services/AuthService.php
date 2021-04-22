<?php
namespace App\Services;

use App\JsonRpc\YzApiInterface;
use App\Model\User;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use RuntimeException;

class AuthService extends BaseService
{
    public static $user;

    /**
     * @Inject()
     * @var \Hyperf\Contract\SessionInterface
     */
    protected $session;

    /**
     * 用户信息匹配
     * @param string $openid
     * @param int $wx_id
     * @return bool
     * @author: DHF 2021/4/13 15:29
     */
    public function login(string $openid,int $wx_id)
    {
        if($this->getUser('openid') == $openid){
            return true;
        }
        if(!$user = User::query()->where(['openid'=>$openid,'wx_id'=>$wx_id])->first()) {
            $yz_api = ApplicationContext::getContainer()->get(YzApiInterface::class);
            $user_info = $yz_api->getUser($openid,$wx_id);
            if(empty($user_info)){
                throw new RuntimeException('用户信息错误',2002);
            }
            $data['uid'] = $user_info['user_id'];
            $data['nickname'] = $user_info['nickname'];
            $data['headimgurl'] = $user_info['headimgurl'];
            $data['openid'] = $user_info['openid'];
            $data['unionid'] = $user_info['unionid'];
            $data['wx_id'] = $user_info['wx_id'];
            $data['accounts_id'] = $user_info['accounts_id'];
            if(!$user = User::create($data)){
                throw new RuntimeException('用户信息保存',2003);
            };
        }
        $this->session->set('user', $user->toArray());
    }

    /**
     * 获取用户信息
     * @param ...$keys
     * @return array|mixed|string
     * @author: DHF 2021/4/16 14:24
     */
    public function getUser(...$keys)
    {
        $user = $this->session->get('user');
        switch (count($keys)){
            case 0:
                return $user;
            case 1:
                return $user[$keys[0]]??'';
            default:
                return array_filter($user,function($value,$key)use($keys){
                    return in_array($key,$keys);
                });
        }
    }

}