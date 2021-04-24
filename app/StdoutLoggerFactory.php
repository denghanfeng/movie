<?php
declare(strict_types=1);

namespace App;

use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

class StdoutLoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get();

    }
}