<?php
namespace App\Task;

use App\Log;
use App\Model\Order;
use App\Server\moive\MoiveService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Task\Annotation\Task;
use OverflowException;

class OrderTask extends BasickTask
{
    const NOTIFY_URL = '/notify/order?channel=toto';

    /**
     * @Inject()
     * @var MoiveService
     */
    protected $moiveService;

    /**
     * @Task
     * @param $thirdOrderId
     * @return array
     * @author: DHF 2021/4/1 16:59
     */
    public function create($thirdOrderId)
    {
        $this->logger->alert($thirdOrderId);
        if(!$Order = Order::where(['thirdOrderId'=>$thirdOrderId,'orderStatus'=>Order::STATUS_PAY])->first()){
            throw new OverflowException("没有找到信息 thirdOrderId={$thirdOrderId}",33061);
        };

        $order_data['notifyUrl'] = env('INDEX_DOMAIN').self::NOTIFY_URL; //回调地址
        $order_data['thirdOrderId'] = $Order->thirdOrderId;
        $order_data['showId'] = $Order->showId; //排期
        $order_data['seat'] = $Order->seat; //座位 用户所选的座位，例：1排1座,1排2座 以英文的逗号 “ , “隔开。 如果座位是情侣座，请传入 ： 1排1座(情侣座),1排2座(情侣座)
        $order_data['acceptChangeSeat'] = $Order->acceptChangeSeat; //调座
        $order_data['reservedPhone'] = $Order->reservedPhone; //预留手机号
        if(!$this->moiveService->create()->createOrder($order_data)){
            throw new OverflowException("下单失败请重新下单 thirdOrderId={$thirdOrderId}",4423);
        };

        $Order->update(['orderStatus'=>Order::STATUS_ACCEPT]);
    }
}