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