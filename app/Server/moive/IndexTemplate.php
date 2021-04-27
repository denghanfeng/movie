<?php
namespace App\Server\moive;

use App\Model\Cinema;

/**
 * 电影接口
 * @author: DHF
 * Class MoiveService
 * @package app\service\menuLink
 */
interface IndexTemplate
{

    /**
     * 获取城市列表
     * @param array $param
     * @return mixed
     * @author: DHF
     */
    public function getCityList(array $param = []);

    /**
     * 获取城市下级区域
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getCityAreaList(array $param = []);

    /**
     * 获取影院
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getCinemaList(array $param = []);

    /**
     * 热映电影
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getHotList(array $param = []);

    /**
     * 即将上映电影
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getSoonList(array $param = []);

    /**
     * 排期
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getScheduleList(array $param = []);

    /**
     * 某场次座位
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getSeat(array $param = []);

    /**
     * 包含某电影的影院
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getShowList(array $param = []);

    /**
     * 包含某电影的日期
     * @param array $param
     * @return false|mixed
     * @author: DHF
     */
    public function getShowDate(array $param = []);
    /**
     * 验证sign
     * @param $param
     * @return bool
     * @author: DHF 2021/4/26 11:53
     */
    public function notify($param):bool;

    /**
     * 价格计算
     * @param $netPrice
     * @param $count
     * @param $cinemaId
     * @return mixed
     * @author: DHF 2021/4/27 18:05
     */
    public function getCommission($netPrice,$cinemaId,$count);

}