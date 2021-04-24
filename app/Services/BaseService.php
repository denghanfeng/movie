<?php
namespace App\Services;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;

class BaseService
{
    /**
     * @Inject()
     * @var StdoutLoggerInterface
     */
    protected $logger;
}