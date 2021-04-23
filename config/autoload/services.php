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
return [
    'consumers' => value(function () {
        // 这里示例自动创建代理消费者类的配置形式，顾存在 name 和 service 两个配置项，这里的做法不是唯一的，仅说明可以通过 PHP 代码来生成配置
        $services = [
            'YzApiService' => ['protocol' => 'jsonrpc', 'service' => App\JsonRpc\YzApiInterface::class],
        ];
        $consumers = [];
        foreach ($services as $name => $interface) {
            $consumers[] = [
                'name' => $name,
                'service' => $interface['service'],
                'protocol' => $interface['protocol'],
//                'registry' => [
//                    'protocol' => 'consul',
//                    'address' => 'http://127.0.0.1:8500',
//                ]
            //没有 consul 中心 则 配置 host
                'nodes' => [
                    ['host' => '127.0.0.1', 'port' => 9503],
                ],
            ];
        }
        return $consumers;
    }),

    // 'consumers' => [
    //     [
    //         // 服务名称，需与服务提供者的 name 属性相同
    //         'name' => 'CalculatorService',
    //         // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
    //         'service' => \App\JsonRpc\CalculatorServiceInterface::class,
    //         // 对应容器对象 ID，可选，默认值等于 service 配置的值，用来定义依赖注入的 key
    //         // 'id' => \App\JsonRpc\CalculatorServiceInterface::class,
    //         // 服务提供者的服务协议，可选，默认值为 jsonrpc-http，可选 jsonrpc-http jsonrpc jsonrpc-tcp-length-check
    //         'protocol' => 'jsonrpc',
    //         // 负载均衡算法，可选，默认值为 random
    //         // 'load_balancer' => 'random',
    //         // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
    //         'registry' => [
    //             'protocol' => 'consul',
    //             'address' => 'http://consul:8500',
    //         ],
    //         // If `registry` is missing, then you should provide the nodes configs.
    //         // 'nodes' => [
    //         //     // Provide the host and port of the service provider.
    //         //     // ['host' => 'The host of the service provider', 'port' => 9502]
    //         //     ['host' => 'consul', 'port' => 9603]
    //         // ],
    //         // 配置项，会影响到 Packer 和 Transporter
    //         // 'options' => [
    //         //     'connect_timeout' => 5.0,
    //         //     'recv_timeout' => 5.0,
    //         //     'settings' => [
    //         //         // 根据协议不同，区分配置
    //         //         'open_eof_split' => true,
    //         //         'package_eof' => "\r\n",
    //         //         // 'open_length_check' => true,
    //         //         // 'package_length_type' => 'N',
    //         //         // 'package_length_offset' => 0,
    //         //         // 'package_body_offset' => 4,
    //         //     ],
    //         //     // 当使用 JsonRpcPoolTransporter 时会用到以下配置
    //         //     'pool' => [
    //         //         'min_connections' => 1,
    //         //         'max_connections' => 32,
    //         //         'connect_timeout' => 10.0,
    //         //         'wait_timeout' => 3.0,
    //         //         'heartbeat' => -1,
    //         //         'max_idle_time' => 60.0,
    //         //     ],
    //         // ]
    //     ],
    // ],
];
