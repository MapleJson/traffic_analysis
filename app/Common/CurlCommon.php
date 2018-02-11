<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

class CurlCommon
{
    //保存例实例在此属性中
    private static $_instance;

    //单例方法
    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * json curl 发送消息
     * post提交数据,调用第三方接口
     * @param $url
     * @param array $data
     * @param bool $json
     * @return array|mixed 请求接口的数据集合
     */
    public static function curlPostJson($url, $data = null, $json = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        if (!empty($data)) {
            if ($json && is_array($data)) {
                $data = json_encode($data);
            }
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if ($json) { //发送JSON数据
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json; charset=utf-8',
                        'Content-Length:' . strlen($data))
                );
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        $errorno = curl_errno($curl);
        if ($errorno) {
            curl_close($curl);
            return array('errorno' => false, 'errmsg' => $errorno);
        }
        curl_close($curl);
        return json_decode($res, true);
    }

    /**
     * 多类型curl
     * @param string $url curl网址
     * @param array $data 参数
     * @param string $method GET|POST|PUT|DELETE
     * @param bool $json 是否返回json
     * @return mixed
     */
    public static function curlJson($url, $data = null, $method = 'POST', $json = true)
    {
        $data = json_encode($data);
        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($data)
        ];
        // 启动一个CURL会话
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($handle,CURLOPT_HEADER,false); // 是否显示返回的Header区域内容
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers); //设置请求头
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data); //设置请求体，提交数据包

        switch($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($handle); // 执行操作
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE); // 获取返回的状态码
        curl_close ($handle); // 关闭CURL会话
        if('200' != $code){
            return ['error_code' => $code];
        }else{
            return ($json ? json_decode($response, true) : $response);
        }
    }
}

