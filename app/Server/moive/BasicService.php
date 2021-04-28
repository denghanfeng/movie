<?php
namespace App\Server\moive;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use OutOfBoundsException;

class BasicService
{

    /**
     * @Inject()
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * 重复请求
     * @param $url
     * @param array $param
     * @param int $times
     * @return false|mixed
     * @author: DHF 2021/4/28 19:56
     */
    public function curlPost($url,$param = [],$times = 0)
    {
        $data = curlPost($url,$param);
        if(empty($data)){
            //电影接口获取容易失败
            return $times > 3 ? false : $this->curlPost($url,$param,$times++);
        }
        return $data;
    }


}