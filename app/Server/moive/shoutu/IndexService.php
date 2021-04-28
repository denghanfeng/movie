<?php
namespace App\Server\moive\shoutu;

use App\Model\Cinema;
use App\Server\moive\BasicService;
use App\Server\moive\IndexTemplate;
use Hyperf\Di\Annotation\Inject;
use OutOfBoundsException;
use App\Model\Config as ConfigModel;
/**
 * 电影接口
 * @author: DHF
 * Class MoiveService
 * @package app\service\menuLink
 */
class IndexService extends BasicService implements IndexTemplate
{
    /**
     * @Inject
     * @var Config
     */
    protected $config;

    /**
     * 获取城市列表
     * @param array $param
     * @return mixed
     * @author: DHF
     */
    public function getCityList(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_CITY_LIST,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['list'];
    }

    /**
     * 获取城市下级区域
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getCityAreaList(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_CITY_AREA,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['list'];
    }

    /**
     * 获取影院
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getCinemaList(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_CINEMA_LIST,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['list'];
    }

    /**
     * 热映电影
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getHotList(array $param = [])
    {
        $data = $this->$this->curlPost($this->config->cur_url.Config::GET_HOT_LIST,$this->config->encrypt($param));
        if(!isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['list'];
    }

    /**
     * 即将上映电影
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getSoonList(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_SOON_LIST,$this->config->encrypt($param));
        if(!isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['list'];
    }

    /**
     * 排期
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getScheduleList(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_SCHEDULE_LIST,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            return false;
            //throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data'];
    }

    /**
     * 某场次座位
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getSeat(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_SEAT,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['seatData'];
    }

    /**
     * 包含某电影的影院
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getShowList(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_SHOW_LIST,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['list'];
    }

    /**
     * 包含某电影的日期
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getShowDate(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::GET_SHOW_DATE,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data']['dateList'];
    }

    /**
     * 下单
     * @param array $param
     * @author: DHF 2021/3/11 14:19
     */
    public function createOrder(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::API_ORDER_CREATE,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return true;
    }

    /**
     * 主动触发事件，测试服
     * @param array $param
     * @return false|mixed
     * @author: DHF 2021/3/11 15:55
     */
    public function orderHandle(array $param = [])
    {
        return $this->curlPost($this->config->cur_url.Config::API_AUTOMATION_ORDERHANDLE,$this->config->encrypt($param));
    }

    /**
     * 订单查询
     * @param array $param
     * @return bool|array
     * @author: DHF 2021/3/11 16:06
     */
    public function orderQuery(array $param = [])
    {
        $data = $this->curlPost($this->config->cur_url.Config::API_ORDER_QUERY,$this->config->encrypt($param));
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data'];
    }

    /**
     * 查看账户余额
     * @return false|mixed
     * @author: DHF 2021/3/11 16:10
     */
    public function userInfo()
    {
        $data = $this->curlPost($this->config->cur_url.Config::API_USER_INFO,$this->config->encrypt());
        if(!is_array($data) || !isset($data['code']) || $data['code'] !== 200){
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
        }
        return $data['data'];
    }

    /**
     * 验证sign
     * @param $param
     * @return bool
     * @author: DHF 2021/4/26 11:53
     */
    public function notify($param):bool
    {
        $sign = $param['sign'];
        unset($param['sign']);
        return $sign == $this->config->getSign($param);
    }

    /**
     * 秒出单
     * @return false|mixed
     * @author: DHF 2021/3/11 16:14
     */
    public function soonOrder($param)
    {
        $data = $this->curlPost($this->config->cur_url.Config::API_ORDER_SOON_ORDER,$this->config->encrypt($param));
//        if($data['code'] !== 200){
//            $data['message'] = $data['message'] ?? '访问失败';
            $data['code'] = $data['code'] ?? '404';
            throw new OutOfBoundsException($data['message']??'访问失败',$data['code']??'404');
//        }
//        return $data['data'];
    }

    /**
     * 支付金额
     * @param $netPrice
     * @param $count
     * @param $cinemaId
     * @author: DHF 2021/4/27 17:20
     */
    public function getCommission($netPrice,$cinemaId,$count)
    {
        $Cinema = Cinema::find($cinemaId);
        $bili = $netPrice > 3900 ? $Cinema->upDiscountRate : $Cinema->downDiscountRate;
        $bili = $bili ?: ConfigModel::where(['namespace'=>'shoutu','key'=>'payPili'])->first()->value;
        return $count * $netPrice * (float)$bili * 1.02;
    }

}