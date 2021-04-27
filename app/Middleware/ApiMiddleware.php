<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use OutOfBoundsException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiMiddleware implements MiddlewareInterface
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
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;
    /**
     *
     * @author: DHF
     * @var string 加解密的密钥
     */
    const APPKEY = '231273817831';
    /**
     *
     * @author: DHF
     * @var string appSecret
     */
    const APPSECRET = 'aaasdasdasdasda';


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
                'sign' => 'required',
                'time' => 'required|integer',
                'appKey' => 'required',
            ]
        );
        if ($validator->fails()){
            throw new OutOfBoundsException($validator->errors()->first(),2001);
        }
//        if(!$this->checkEncrypt($params)){
//            throw new OutOfBoundsException('验证失败',2002);
//        }

        return $handler->handle($request);
    }

    /**
     * 加密验证
     * @param $params
     * @return bool
     * @author: DHF 2021/4/27 13:53
     */
    private function checkEncrypt($params)
    {
        $sign = $params['sign'];
        unset($params['sign']);
        return $sign == $this->encrypt($params);
    }

    /**
     * 加密
     * @param array $param
     * @return string
     * @author: DHF 2021/4/27 13:53
     */
    private function encrypt(array $param = [])
    {
        $param['appKey'] = self::APPKEY;
        empty($param['time']) && $param['time'] = time();
        //按键名升序排序
        ksort($param);
        return md5(http_build_query($param) . '&appSecret=' . self::APPSECRET);
    }
}