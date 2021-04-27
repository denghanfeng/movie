<?php
namespace App\Services;

use App\Model\Cinema;
use App\Model\Filme;
use App\Model\Order;
use App\Server\moive\MoiveService;
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
        if(!$filme = Filme::find($data['filmId'])){
            throw new RuntimeException('电影信息查询失败',2006);
        }
        if(!$cinema = Cinema::find($data['cinemaId'])){
            throw new RuntimeException('电影信息查询失败',2006);
        }

        isset($data['test']) && $param['appKey'] = Order::TEST_APP_KEY;
        isset($data['acceptChangeSeat']) && $param['acceptChangeSeat'] = $data['acceptChangeSeat'];
        isset($data['reservedPhone']) && $param['reservedPhone'] = $data['reservedPhone'];
        isset($data['seatId']) && $param['seatId'] = $data['seatId'];
        isset($data['seatNo']) && $param['seatNo'] = $data['seatNo'];

        $param['seat'] = $data['seat'];
        $param['orderStatus'] = Order::STATUS_START;
        $param['orderNum'] = count(explode(",",$data['seat']));

        $param['showId'] = $data['showId'];
        $param['cinemaId'] = $data['cinemaId'];
        $param['filmId'] = $data['filmId'];

        $param['filmeName'] = $filme->name;
        $param['filmePic'] = $filme->pic;

        $param['cinemaName'] = $cinema->cinemaName;
        $param['cinemaAddress'] = $cinema->address;
        $param['latitude'] = $cinema->latitude;
        $param['longitude'] = $cinema->longitude;
        $param['cinemaPhone'] = $cinema->phone;
        $param['cinemaPhone'] = $cinema->phone;


        $param['initPrice'] = $show->netPrice * $param['orderNum'];
        $param['payPrice'] = $show->payPrice ? $show->payPrice * $param['orderNum'] : $this->moiveService->create()->getCommission($show->netPrice,$cinema->cinemaId,$param['orderNum']);
        $param['payType'] = $data['payType'];

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
     * @return array
     * @author: DHF 2021/4/26 21:36
     */
    public function one(int $thirdOrderId)
    {
        $order = Order::select([
                'seat',
                'cinemaName',
                'cinemaAddress',
                'latitude',
                'longitude',
                'cinemaPhone',
                'filmeName',
                'filmePic',
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
            ])
            ->where(['thirdOrderId'=>$thirdOrderId])
            ->where('uid',$this->authService->getUser('uid'))
            ->first();

        $order->thirdOrderId = (string)$order->thirdOrderId;
        return $order->toArray();
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
        $order = Order::where('uid',$this->authService->getUser('uid'));
        if($orderStatus == 4){
            $order->whereIn('orderStatus',['4','5','10']);
        }elseif($orderStatus){
            $order->where('orderStatus',$orderStatus);
        }
        $max_id && $order->where('thirdOrderId','<=',$max_id);
        $count = $order->count();
        $list = $order
            ->limit(10)
            ->orderBy('thirdOrderId','desc')
            ->get([
                'filmeName',
                'filmePic',
                'cinemaName',
                'cinemaAddress',
                'latitude',
                'longitude',
                'cinemaPhone',
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
        foreach ($list as &$order){
            $order['thirdOrderId'] = (string)$order['thirdOrderId'];
        }
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
        $Order->checkSettle();
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
            //出票
            case 'TICKET_SUCCESS':
                $data['orderStatus'] = Order::STATUS_WAIT_STATEMENT;
                $data['ticketCode'] = $param['ticketCode'];
                $data['ticketImage'] = $param['ticketImage'];
                $data['ticketTime'] = date("Y-m-d H:i:s");
                empty($param['realSeat']) || $data['seat'] = $param['realSeat'];
                break;
            //同步取票码
            case 'TICKET_SYNC':
                $data['ticketCode'] = $param['ticketCode'];
                $data['ticketImage'] = $param['ticketImage'];
                empty($param['realSeat']) || $data['seat'] = $param['realSeat'];
                break;
            //等待出票
            case 'WAIT_TICKET':
                $data['orderStatus'] = Order::STATUS_WAIT_DRAWERS;
                $data['orderPrice'] = $param['orderPrice'];
                $data['readyTicketTime'] = date("Y-m-d H:i:s");
                break;
            //完结
            case 'ORDER_FINISH':
                $data['orderStatus'] = Order::STATUS_END;
                $data['closeCause'] = $param['closeCause'];
                $data['closeTime'] = date("Y-m-d H:i:s");
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

    /**
     * 同步订单
     * @param $thirdOrderId
     * @return bool
     * @author: DHF 2021/4/27 15:16
     */
    public function copyOrder($thirdOrderId)
    {
        if(!$copy_order =  $this->moiveService->create()->orderQuery(['thirdOrderId'=>$thirdOrderId])){
            return false;
        };
        if(!Order::updateOrCreate(['thirdOrderId'=>$thirdOrderId],$copy_order)){
            throw new RuntimeException('订单保存失败',2004);
        }
        return true;
    }
}