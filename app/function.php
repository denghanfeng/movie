<?php

function pre($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}


/**
 * POST访问
 * @param $url
 * @param $data
 * @param array $headers
 * @return mixed
 * @author: DHF 2021/3/11 13:42
 */
function curlPost($url,$data,$headers=[])
{
    $ch=curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
    if( $headers ){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置请求头
    }
    if(substr($url, 0, 5) == 'https') {
        //禁止https协议验证域名，0就是禁止验证域名且兼容php5.6
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt ($ch, CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //设置请求时间为5秒
    $output = curl_exec($ch);
    $json_str=json_decode($output,true);
    /*return access_token*/
    return $json_str;
}

/**
 * @DESC curl get请求
 * @param $url
 * @return mixed
 */
function  curlGet ( $url ){
    $ch=curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
    $output = curl_exec($ch) ;
    $json_str=json_decode($output,true);
    /*return access_token*/
    return $json_str;
}

/**
 * 汉字转数字
 * @param $str
 * @return float|int|mixed
 * @author: DHF 2021/4/24 17:09
 */
function checkNatInt($str) {
    $map = array(
        '一' => '1','二' => '2','三' => '3','四' => '4','五' => '5','六' => '6','七' => '7','八' => '8','九' => '9',
        '壹' => '1','贰' => '2','叁' => '3','肆' => '4','伍' => '5','陆' => '6','柒' => '7','捌' => '8','玖' => '9',
        '零' => '0','两' => '2',
        '仟' => '千','佰' => '百','拾' => '十',
        '万万' => '亿',
    );

    $str = str_replace(array_keys($map), array_values($map), $str);
    $str = checkString($str, '/([\d亿万千百十]+)/u');

    $func_c2i = function ($str, $plus = false) use(&$func_c2i) {
        if(false === $plus) {
            $plus = array('亿' => 100000000,'万' => 10000,'千' => 1000,'百' => 100,'十' => 10,);
        }
        $i = 0;
        if($plus)
            foreach($plus as $k => $v) {
                $i++;
                if(strpos($str, $k) !== false) {
                    $ex = explode($k, $str, 2);
                    $new_plus = array_slice($plus, $i, null, true);
                    $l = $func_c2i($ex[0], $new_plus);
                    $r = $func_c2i($ex[1], $new_plus);
                    if($l == 0) $l = 1;
                    return $l * $v + $r;
                }
            }
        return (int)$str;
    };
    return $func_c2i($str);
 }

//来自uct php微信开发框架，其中的checkString函数如下
function checkString($var, $check = '', $default = '') {
    if (!is_string($var)) {
        if(is_numeric($var)) {
            $var = (string)$var;
        }
        else {
            return $default;
        }
    }
    if ($check) {
        return (preg_match($check, $var, $ret) ? $ret[1] : $default);
    }
    return $var;
}