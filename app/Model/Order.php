<?php

declare (strict_types=1);
namespace App\Model;

use function _HumbugBox61bfe547a037\RingCentral\Psr7\str;
/**
 * @property int $thirdOrderId 接入方的订单号
 * @property int $uid 用户id
 * @property int $cinemaId 影院id
 * @property int $filmId 影片id
 * @property string $showId 场次标识
 * @property string $appKey 下单appKey
 * @property int $orderStatus 订单状态：2-受理中，3-待出票，4-已出票待结算，5-已结算，10-订单关闭
 * @property string $orderStatusStr 订单状态说明
 * @property int $initPrice 订单市场价：分
 * @property int $orderPrice 订单成本价：分，接入方可拿次字段作为下单成本价
 * @property string $seat 座位：英文逗号隔开
 * @property int $orderNum 座位数
 * @property string $reservedPhone 下单预留手机号码
 * @property string $readyTicketTime 待出票时间
 * @property string $ticketTime 出票时间
 * @property string $closeTime 关闭时间
 * @property string $closeCause 关闭原因
 * @property int $payType 支付方式
 * @property string $payOrder 支付订单号
 * @property string $ticketCode 取票码，type为1时，为字符串，type为2时，为取票码原始截图。 理论上一个取票码包含各字符串和原始截图， 原始截图可能不和字符串同步返回，有滞后性。
 * @property string $ticketImage 取票码原始截图
 * @property int $acceptChangeSeat 是否允许调座
 * @property string $hallName 影厅名
 * @property string $showTime 放映时间
 * @property string $showVersionType 场次类型
 * @property string $language 语言
 * @property string $planType 影厅类型 2D 3D
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property-read \App\Model\Cinema $cinema 
 * @property-read \App\Model\Filme $filme 
 */
class Order extends Model
{
    const TEST_APP_KEY = 100;
    //创建订单
    const STATUS_START = -1;
    //订单已支付
    const STATUS_PAY = 1;
    //受理中
    const STATUS_ACCEPT = 2;
    //待出票
    const STATUS_WAIT_DRAWERS = 3;
    //已出票待结算
    const STATUS_WAIT_STATEMENT = 4;
    //已结算
    const STATUS_STATEMENT = 5;
    //订单关闭
    const STATUS_END = 10;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';
    protected $primaryKey = 'thirdOrderId';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['thirdOrderId', 'uid', 'cinemaId', 'filmId', 'showId', 'appKey', 'orderStatus', 'orderStatusStr', 'initPrice', 'orderPrice', 'seat', 'orderNum', 'reservedPhone', 'readyTicketTime', 'ticketTime', 'closeTime', 'closeCause', 'payType', 'payOrder', 'ticketCode', 'ticketImage', 'acceptChangeSeat', 'hallName', 'showTime', 'showVersionType', 'language', 'planType', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['thirdOrderId' => 'integer', 'uid' => 'integer', 'cinemaId' => 'integer', 'filmId' => 'integer', 'orderStatus' => 'integer', 'initPrice' => 'integer', 'orderPrice' => 'integer', 'orderNum' => 'integer', 'payType' => 'integer', 'acceptChangeSeat' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 影院信息
     * @return \Hyperf\Database\Model\Relations\HasOne
     * @author: DHF 2021/4/19 15:24
     */
    public function cinema()
    {
        return $this->hasOne(Cinema::class, 'cinemaId', 'cinemaId');
    }
    /**
     * 电影信息
     * @return \Hyperf\Database\Model\Relations\HasOne
     * @author: DHF 2021/4/20 18:19
     */
    public function filme()
    {
        return $this->hasOne(Filme::class, 'filmId', 'filmId');
    }
    /**
     * 生成新的订单账号
     * @author: DHF 2021/4/24 11:41
     */
    public static function getNewId() : int
    {
        list($s1, $s2) = explode('.', (string) microtime(true));
        return (int) (date("YmdHis") . $s2);
    }
    /**
     * 主键去重复生成
     * @param $param
     * @param int $time
     * @return Order|false|\Hyperf\Database\Model\Model
     * @author: DHF 2021/4/24 11:45
     */
    public static function createById($param, $time = 0)
    {
        if ($time > 3) {
            return false;
        }
        $param['thirdOrderId'] = Order::getNewId();
        if (!($order = Order::create($param))) {
            $order = self::createById($param, $time + 1);
        }
        return $order;
    }
}