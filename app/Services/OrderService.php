<?php
namespace App\Services;

use App\Model\Order;
use App\Server\moive\MoiveService;
use App\Task\CrontabTask;
use App\Task\OrderTask;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use RuntimeException;

class OrderService extends BaseService
{
    /**
     * @Inject()
     * @var AuthService
     */
    protected $authService;

    /**
     * @Inject()
     * @var PayService
     */
    protected $payService;

    /**
     * 获取记录的手机号
     * @return array|mixed|string
     * @author: DHF 2021/4/21 15:16
     */
    public function getPhone()
    {
        return $this->authService->getUser('phone');
    }

    /**
     * 生成订单
     * @param $param
     * @return array|false
     * @author: DHF 2021/4/16 13:49
     */
    public function create($param)
    {
        if(!$show = (new CrontabTask)->updateShow($param['cinemaId'],$param['showId'])){
            throw new RuntimeException('订单场次查询失败',2005);
        };
        $param['uid'] = $this->authService->getUser('uid');
        $param['orderStatus'] = Order::STATUS_START;
        $param['orderNum'] = count(explode(",",$param['seat']));
        $param['initPrice'] = $show->netPrice;
        if(!$Order = Order::create($param)){
            throw new RuntimeException('订单保存失败',2004);
        };
        return $this->payService->index($Order);
    }



    /**
     * 查询单个订单信息
     * @param integer $thirdOrderId
     * @return array
     * @author: DHF 2021/4/16 15:02
     */
    public function one(int $thirdOrderId)
    {
        $order = Order::with('cinema')->find($thirdOrderId);
        $order->cinema->setVisible(['cinemaName','address','latitude','longitude','phone','regionName']);
        return $order->setVisible(['cinema','seat','reservedPhone','acceptChangeSeat','orderStatus','created_at','thirdOrderId'])->toArray();
    }

    /**
     * 查询订单列表
     * @param integer  $orderStatus
     * @param integer $max_id
     * @return array
     * @author: DHF 2021/4/16 16:35
     */
    public function list(int $orderStatus,int $max_id)
    {
        $order = Order::with(['cinema'=>function($query){
            return $query->select(['cinemaId','cinemaName','address','latitude','longitude','phone','regionName']);
        }]);
        $orderStatus && $order->where('orderStatus',$orderStatus);
        $max_id && $order->where('thirdOrderId','<=',$max_id);
        return $order
            ->orderBy('thirdOrderId','desc')
            ->get(['cinemaId','seat','reservedPhone','acceptChangeSeat','orderStatus','created_at','thirdOrderId'])
            ->toArray();
    }

    /**
     * 订单回调
     * @param $param
     * @author: DHF 2021/4/21 15:36
     */
    public function notify($param)
    {
        if(!$Order = Order::find($param['thirdOrderId'])){
            throw new RuntimeException('订单查询失败',2007);
        };
        if(!$Order->update($param)){
            throw new RuntimeException('订单保存失败',2004);
        };
        return true;
    }

    /**
     * 订单支付回调
     * @param int $order_id
     * @author: DHF 2021/4/21 15:38
     */
    public function pay(int $order_id)
    {
        ApplicationContext::getContainer()->get(OrderTask::class)->create($order_id);
    }


}