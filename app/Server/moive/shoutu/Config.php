<?php

namespace App\Server\moive\shoutu;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;

/**
 * 电影接口配置文件
 * @author: DHF
 * Class Config
 * @package app\service\moive
 * 文档地址：https://www.showdoc.com.cn/1154868044931571?page_id=6166551054194295
 */
class Config
{
    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    CONST GET_SIGN = 'api/sign/getSign'; //获取签名(仅支持测试环境)
    CONST GET_CITY_LIST = 'movieapi/movie-info/get-city-list'; //获取城市列表
    const GET_CITY_AREA = 'movieapi/movie-info/get-city-area'; //城市下区域列表
    const GET_CINEMA_LIST = 'movieapi/movie-info/get-cinema-list'; //影院列表
    const GET_HOT_LIST = 'movieapi/movie-info/get-hot-list'; //正在热映电影
    const GET_SOON_LIST = 'movieapi/movie-info/get-soon-list'; //即将上映电影
    const GET_SCHEDULE_LIST = 'movieapi/movie-info/get-schedule-list'; //场次排期
    const GET_SEAT = 'movieapi/movie-info/get-seat'; //某场次的座位
    const GET_SHOW_LIST = 'movieapi/movie-info/get-show-list'; //包含某电影的影院
    const GET_SHOW_DATE = 'movieapi/movie-info/get-show-date'; //包含某电影的日期

    const API_ORDER_CREATE = 'api/order/create'; //下单API
    const API_AUTOMATION_ORDERHANDLE = 'api/automation/orderHandle'; //此接口用于接入方自行订单流程调试。
    const API_ORDER_QUERY = 'api/order/query'; //订单查询
    const API_USER_INFO = 'api/user/info'; //查看账户余额
    const API_ORDER_SOON_ORDER = 'api/order/create-soon-order'; //秒出单
    /**
     *
     * @author: DHF
     * @var string 测试环境地址
     */
    public $cur_url = '';

    /**
     *
     * @author: DHF
     * @var string 加解密的密钥
     */
    private $appkey = '';

    /**
     *
     * @author: DHF
     * @var string appSecret
     */
    private $appsecret = '';


    public function __construct()
    {
        $this->cur_url = getenv('SHOUTU_MOIVE_URL');
        $this->appkey = getenv('SHOUTU_MOIVE_APPKEY');
        $this->appsecret = getenv('SHOUTU_MOIVE_APPSECRET');
    }

    /**
     * 加密
     * @param array $param 要加密的数据
     * @return array
     * @author: DHF
     */
    public function encrypt(array $param = [])
    {
        $param['appKey'] = $this->appkey;
        empty($param['time']) && $param['time'] = time();
        $param['sign'] = $this->getSign($param);
        return $param;
    }

    /**
     * 获取加密sign
     * @param $param
     * @return string
     * @author: DHF 2021/4/26 11:52
     */
    public function getSign($param)
    {
        //按键名升序排序
        ksort($param);
        $arr = [];
        foreach ($param as $key => $value){
            $arr[]="{$key}={$value}";
        }
        return  md5(implode("&",$arr).'&appSecret='.$this->appsecret);
    }

}