<?php
namespace App\Services;

use App\Model\Order;
use App\Model\User;
use App\Server\moive\MoiveService;
use App\Task\CrontabTask;
use Hyperf\Di\Annotation\Inject;
use RuntimeException;
use function _HumbugBox61bfe547a037\RingCentral\Psr7\str;

class OrderService extends BaseService
{
    /**
     * @Inject()
     * @var AuthService
     */
    protected $authService;
    /**
     * @Inject()
     * @var MoiveService
     */
    protected $moiveService;
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
        isset($data['test']) && $param['appKey'] = Order::TEST_APP_KEY;
        isset($data['filmId']) && $param['filmId'] = $data['filmId'];
        isset($data['acceptChangeSeat']) && $param['acceptChangeSeat'] = $data['acceptChangeSeat'];
        isset($data['reservedPhone']) && $param['reservedPhone'] = $data['reservedPhone'];
        isset($data['payType']) && $param['payType'] = $data['payType'];
        isset($data['seatId']) && $param['seatId'] = $data['seatId'];
        isset($data['seatNo']) && $param['seatNo'] = $data['seatNo'];
        $param['showId'] = $data['showId'];
        $param['cinemaId'] = $data['cinemaId'];
        $param['seat'] = $data['seat'];
        $param['orderStatus'] = Order::STATUS_START;
        $param['orderNum'] = count(explode(",",$param['seat']));
        $param['initPrice'] = $show->netPrice;
        $param['payPrice'] = ceil($show->netPrice * Order::PAY_BILI);
        $param['hallName'] = $show->hallName;
        $param['showTime'] = $show->showTime;
        $param['showVersionType'] = $show->showVersionType;
        $param['language'] = $show->language;
        $param['planType'] = $show->planType;
        $param['uid'] = $this->authService->getUser('uid');
        $param['pid'] = $this->authService->getUser('pid');

        if(!empty($param['reservedPhone'])){
            $this->authService->updateUser(['phone'=>$param['reservedPhone']]);
        }
        if(!$Order = Order::createById($param)){
            throw new RuntimeException('订单保存失败',2004);
        };
        if(!$ref = $this->payService->index($Order)){
            return false;
        };
        $ref['thirdOrderId'] = (string)$Order->thirdOrderId;
        return $ref;
    }


    /**
     * 查询单个订单信息
     * @param int $thirdOrderId
     * @return Order|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     * @author: DHF 2021/4/24 18:14
     */
    public function one(int $thirdOrderId)
    {
        return Order::with([
            'cinema'=>function($query){
                return $query->select(['cinemaId','cinemaName','address','latitude','longitude','phone','regionName']);
            },
            'filme'=>function($query){
                return $query->select(['filmId','name','pic']);
            },
        ])->select([
            'seat',
            'filmId',
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
            'payPrice',
            'initPrice',
            'ticketCode',
            'ticketImage',
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
        $order = Order::with([
            'cinema'=>function($query){
                return $query->select(['cinemaId','cinemaName','address','latitude','longitude','phone','regionName']);
            },
            'filme'=>function($query){
                return $query->select(['filmId','name','pic']);
            },
        ]);
        $orderStatus = $orderStatus == 4 ? [4,5,10] : $orderStatus;
        $orderStatus && $order->where('orderStatus',$orderStatus);
        $max_id && $order->where('thirdOrderId','<=',$max_id);
        $count = $order->count();
        $list = $order
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
                'payPrice',
                'initPrice',
                'cinemaId',
                'payPrice',
                'initPrice',
                'ticketCode',
                'ticketImage',
            ])
            ->toArray();
        return [
            'count'=>$count,
            'list'=>$list,
        ];
    }

    /**
     * 接口获取订单信息
     * @param array $param
     * @return mixed
     * @author: DHF 2021/4/26 14:22
     */
    public function apiList($param = [])
    {
        $param['limit'] = isset($param['limit']) && $param['limit'] < 1000 && $param['limit'] > 0 ? $param['limit'] : 1000;
        $param['page'] = $param['page'] ?? 1;

        $order = Order::where('orderStatus','>',Order::STATUS_START);
        empty($param['orderStatus']) || $order->where('orderStatus',$param['orderStatus']);
        empty($param['uid']) || $order->where('uid',$param['uid']);
        empty($param['start_updated_at']) || $order->where('updated_at','>',$param['start_updated_at']);
        empty($param['end_updated_at']) || $order->where('updated_at','<',$param['end_updated_at']);
        $param['count'] = $order->count();
        $param['list'] = $order
            ->orderBy('thirdOrderId','desc')
            ->limit($param['limit'])
            ->offset(($param['page'] - 1)*$param['limit'])
            ->get()
            ->toArray();
        return $param;
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
        $channel = $param['channel'];
        unset($param['channel']);
        if(!$this->moiveService->create($channel)->notify($param)){
            throw new RuntimeException('验证不通过',2008);
        };
        if(!$Order->update($this->notifyData($param))){
            throw new RuntimeException('订单保存失败',2004);
        };
        return true;
    }

    /**
     * 生成订单数据
     * @param $param
     * @return array
     * @author: DHF 2021/4/26 11:56
     */
    public function notifyData($param)
    {
        $data = [];
        switch ($param['eventName'])
        {
            case 'TICKET_SUCCESS':
                $data['orderStatus'] = Order::STATUS_WAIT_STATEMENT;
                $data['ticketCode'] = $param['ticketCode'];
                $data['ticketImage'] = $param['ticketImage'];
                $data['ticketTime'] = date("Y-m-d H:i:s");
                empty($param['realSeat']) || $data['seat'] = $param['realSeat'];
                break;
        }

        return $data;
    }


    public function refund($thirdOrderId)
    {
        $order = Order::find($thirdOrderId);
        if($order->orderStatus != Order::STATUS_PAY){
            return [];
        }
        return [];
    }
}