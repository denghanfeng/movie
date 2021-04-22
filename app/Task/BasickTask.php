<?php
namespace App\Task;

use App\Model\Order;
use App\Server\moive\MoiveService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Task\Annotation\Task;
use Hyperf\Utils\ApplicationContext;
use OverflowException;

class BasickTask
{
    const TOP_TIMES = 3; //最大重复次数
    public $times = 1;

    /**
     * 获取失败重试
     * @param ...$data
     * @return false
     * @author: DHF 2021/4/20 17:24
     */
    protected function moreTime(...$data)
    {
        if($this->times >= self::TOP_TIMES){
            return false;
        }
        $task = ApplicationContext::getContainer()->get(self::class);
        $task->addTimes()->create(...$data);
    }

    /**
     * 次数加一
     * @return $this
     * @author: DHF 2021/4/20 17:25
     */
    protected function addTimes()
    {
        $this->times += 1;
        return $this;
    }
}