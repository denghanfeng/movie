<?php
namespace App\Services;

use App\Model\Order;
use Hyperf\Di\Annotation\Inject;
use RuntimeException;

class PayService extends BaseService
{
    const WX_MP_PAY_API = '/api/weixin/mp-pay'; //微信公众号
    const WX_MINI_PAY_API = '/api/weixin/mp-pay'; //微信小程序支付

    const ACTION = 'moive';
    private $post_data = [];
    private $order;
    private $post_url;

    /**
     * @Inject()
     * @var AuthService
     */
    protected $authService;

    /**
     * @Inject()
     * @var OrderService
     */
    protected $orderService;

    /**
     * 调起支付
     * @param Order $order
     * @return mixed
     * @author: DHF 2021/4/20 18:25
     */
    public function index(Order $order)
    {
        $this->order = $order;
        switch ($this->order->payType){
            case 1:
                $this->wxMp();
                break;
            case 2:
                $this->wxMini();
                break;
        }
        $info = curlPost($this->post_url,$this->post_data);
        if(!isset($info['code']) || $info['code'] !== 200){
            throw new RuntimeException($info['message'],$info['code']);
        }
        return $info['data'];
    }

    private function baseData()
    {


    }

    /**
     * 微信公众号支付
     * @author: DHF 2021/4/21 10:00
     */
    public function wxMp()
    {
        $this->post_data['wx_id'] = $this->authService->getUser('wx_id');
        $this->post_data['openid'] = $this->authService->getUser('openid');
        $this->post_data['action_order_id'] = $this->order->thirdOrderId;
        $this->post_data['body'] = $this->order->cinema->cinemaName.'--'.$this->order->filme->name;
        $this->post_data['action'] = self::ACTION;
        $this->post_data['total_fee'] = 101;//$this->order->initPrice; 沙箱测试固定值

        $this->post_url = env('YZ_DOMAIN').self::WX_MP_PAY_API;
    }

    /**
     * 微信小程序支付
     * @author: DHF 2021/4/21 10:31
     */
    public function wxMini()
    {
        $this->post_data['wx_id'] = $this->authService->getUser('wx_id');
        $this->post_data['openid'] = $this->authService->getUser('openid');
        $this->post_data['action_order_id'] = $this->order->thirdOrderId;
        $this->post_data['body'] = $this->order->cinema->cinemaName.'--'.$this->order->filme->name;
        $this->post_data['action'] = self::ACTION;
        $this->post_data['total_fee'] = 101;//$this->order->initPrice; 沙箱测试固定值

        $this->post_url = env('YZ_DOMAIN').self::WX_MP_PAY_API;
    }

    /**
     * 回调
     * @param $param
     * @return array
     * @author: DHF 2021/4/21 15:22
     */
    public function notify($param)
    {
        $this->orderService->pay();
        return [];
    }
}