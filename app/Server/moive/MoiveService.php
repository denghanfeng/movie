<?php
namespace App\Server\moive;

/**
 * 电影接口
 * @author: DHF
 * Class MoiveService
 * @package app\service\moive
 */
class MoiveService
{
    const FACTORY = 'shoutu'; //默认仓库

    /**
     * 获取仓库模型
     * @param string $factory
     * @return IndexTemplate
     * @author: DHF 2021/4/13 15:12
     */
    public function create($factory = ''): IndexTemplate
    {
        $factory = $factory ? $factory : self::FACTORY;
        $class=__NAMESPACE__."\\{$factory}\\IndexService";
        if (!class_exists($class)) {
            $class=__NAMESPACE__."\\".self::FACTORY."\\IndexService";
        }
        return new $class;
    }



}