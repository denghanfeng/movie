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
namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use OutOfBoundsException;
use Psr\Container\ContainerInterface;
use App\Middleware\AuthMiddleware;

/**
 *
 * @author: DHF 2021/4/13 15:04
 * Class AbstractController
 * @package App\Controller
 * @Middlewares({
 *     @Middleware(AuthMiddleware::class)
 * })
 */
abstract class AbstractController
{
    /**
     * @Inject
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
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * 成功回调
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface
     * @author: DHF 2021/4/14 11:54
     */
    public function success($data = []){
        return $this->response->json([
            'code' => 200,
            'msg' => 'success',
            'data' => $data
        ]);
    }

    /**
     * 参数验证
     * @param $rules
     * @author: DHF 2021/4/14 11:54
     */
    public function validated($rules)
    {
        $validator = $this->validationFactory->make(
            $this->request->all(),$rules
        );

        if ($validator->fails()){
            throw new OutOfBoundsException($validator->errors()->first(),2002);
        }
    }
}
