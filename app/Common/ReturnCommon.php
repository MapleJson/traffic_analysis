<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

class ReturnCommon
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
     * 打印输出调试信息
     * @param string $data
     * @param bool|true $exit
     */
    public static function tbug($data = '', $exit = true)
    {
        header('Content-type: text/html; charset=utf-8');
        echo '<pre/>';
        print_r($data);
        !$exit || exit ();
    }

    /**
     * 接口返回值处理
     * @param $result
     * @return array|string
     */
    public static function returnValue($result)
    {
        if (empty($result['Status'])) {
            return self::retu('', '没有数据返回');
        }
        if ($result['Status'] != 200) {
            return self::retu('', $result['Message']);
        }
        if (empty($result['Data'])) {
            return self::retu([]);
        }
        return self::retu($result['Data']);
    }

    /**
     * 接口统一返回数据
     * @param string $data
     * @param string $error
     * @return array
     */
    public static function retu($data = '', $error = '')
    {
        if (empty($error)) {
            return ['status' => true, 'data' => $data, 'message' => '请求成功'];
        }
        return ['status' => false, 'data' => $data, 'message' => $error];
    }
}

