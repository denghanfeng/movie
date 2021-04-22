<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use RuntimeException;
use LogicException;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $stream['code'] = $throwable->getCode();
        $stream['msg'] = 'service is error';

        //系统错误调试环境打印
        if($throwable instanceof LogicException || env('APP_ENV') !== 'prod'){
            $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
            $this->logger->error($throwable->getTraceAsString());
        }
        //正常回调
        $throwable instanceof RuntimeException && $stream['msg'] = $throwable->getMessage();
        //其他错误
        return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(new SwooleStream(json_encode($stream)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
