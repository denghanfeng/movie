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
    'shoutu' => [
        'host' => getenv('SHOUTU_MOIVE_URL'),
        'appkey' => getenv('SHOUTU_MOIVE_APPKEY'),
        'appsecret' => getenv('SHOUTU_MOIVE_APPSECRET'),
    ],
];
