<?php
namespace App\Services;

use App\Model\Order;
use App\Model\User;
use App\Task\CrontabTask;
use Hyperf\Di\Annotation\Inject;
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
    public function create($data)
    {
        if(!$show = (new CrontabTask)->updateShow($data['cinemaId'],$data['showId'])){
            throw new RuntimeException('订单场次查询失败',2005);
        }
        $param['showId'] = $data['showId'];
        $param['cinemaId'] = $data['cinemaId'];
        $param['seat'] = $data['seat'];
        isset($data['filmId']) && $param['filmId'] = $data['filmId'];
        isset($data['acceptChangeSeat']) && $param['acceptChangeSeat'] = $data['acceptChangeSeat'];
        isset($data['reservedPhone']) && $param['reservedPhone'] = $data['reservedPhone'];
        isset($data['payType']) && $param['payType'] = $data['payType'];
        isset($data['seatId']) && $param['seatId'] = $data['seatId'];
        isset($data['seatNo']) && $param['seatNo'] = $data['seatNo'];

        $param['orderStatus'] = Order::STATUS_START;
        $param['orderNum'] = count(explode(",",$param['seat']));
        $param['initPrice'] = $show->netPrice;
        $param['hallName'] = $show->hallName;
        $param['showTime'] = $show->showTime;
        $param['showVersionType'] = $show->showVersionType;
        $param['language'] = $show->language;
        $param['planType'] = $show->planType;

        if(!empty($param['reservedPhone'])){
            $this->authService->updateUser(['phone'=>$param['reservedPhone']]);
        }
        if(!$Order = Order::createById($param)){
            throw new RuntimeException('订单保存失败',2004);
        };
        return $this->payService->index($Order);
    }


    /**
     * 查询单个订单信息
     * @param int $thirdOrderId
     * @return Order|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     * @author: DHF 2021/4/24 18:14
     */
    public function one(int $thirdOrderId)
    {
        return Order::with(['cinema'=>function($query){
            return $query->select(['cinemaId','cinemaName','address','latitude','longitude','phone','regionName']);
        }])->select([
            'seat',
            'cinemaId',
            'reservedPhone',
            'acceptChangeSeat',
            'orderStatus',
            'created_at',
            'thirdOrderId',
            'hallName',
            'showVersionType',
            'language',
            'planType',
        ])->where(['thirdOrderId'=>$thirdOrderId])->first();
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
            ->get([
                'seat',
                'reservedPhone',
                'acceptChangeSeat',
                'orderStatus',
                'created_at',
                'thirdOrderId',
                'hallName',
                'showVersionType',
                'language',
                'planType',
                'cinemaId'
            ])
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

}