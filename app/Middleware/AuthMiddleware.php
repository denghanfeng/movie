<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use OutOfBoundsException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @Inject()
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject()
     * @var AuthService
     */
    protected $authService;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $query_params = $request->getQueryParams();
        $headers_params = $request->getHeaders();
        $body_params = $request->getParsedBody();
        $headers_params = array_map(function ($header){
            return $header[0] ?? '';
        },$headers_params);
        $params = array_merge($headers_params,$query_params,$body_params);
        $validator = $this->validationFactory->make(
            $params,
            [
                'openid' => 'required',
                'wx_id' => 'required',
            ]
        );

        if ($validator->fails()){
            throw new OutOfBoundsException($validator->errors()->first(),2001);
        }
        $this->authService->login((int)base64_decode($params['openid']),(int)$params['wx_id']);

        return $handler->handle($request);
    }
}