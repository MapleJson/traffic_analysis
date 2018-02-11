<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

class Common
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
     * @param string $mark  日志的类型标示
     * @param string $log_content  日志的内容
     * @param string $keyp 日志重命名
     * @param string $size 每个日志的大小 以M做单位
     */
    public static function logger($mark, $log_content, $keyp = '', $size = '2')
    {
        $suffix = '.log';
        $max_size = $size * 1024 * 1024;
        $path = storage_path('logs') . '/' . date('Ymd');
        if (!file_exists($path)) {
            mkdir($path, 0755, true); //修改权限
        }
        if ($keyp == '') {
            $log_filename = $path . '/' . date('Y-m-d') . $suffix;
        } else {
            $log_filename = $path . '/' . date('Y-m-d') . $keyp . $suffix;
        }

        if (file_exists($log_filename) && (abs(filesize($log_filename)) > $max_size)) {
            rename($log_filename, dirname($log_filename) . '/' . date('Y-m-d His') . $keyp . $suffix);
        }
        $date_t = date('Ymd') . microtime(true);
        file_put_contents($log_filename, $date_t . '   key：' . $mark . '\r\n' . $log_content . '\r\n--------------------------------------------------\r\n', FILE_APPEND);
    }

    /**
     * 生成 GUID（UUID）
     * @param bool $trim
     * @return string
     */
    public static function _createGUID ($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true) {
                return trim(com_create_guid(), '{}');
            } else {
                return com_create_guid();
            }
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // '-'
        $lbrace = $trim ? '' : chr(123);    // '{'
        $rbrace = $trim ? '' : chr(125);    // '}'
        $guidv4 = $lbrace . substr($charid,  0,  8)
            . $hyphen . substr($charid,  8,  4)
            . $hyphen . substr($charid, 12,  4)
            . $hyphen . substr($charid, 16,  4)
            . $hyphen . substr($charid, 20, 12)
            . $rbrace;

        return $guidv4;
    }

    /**
     * 生成随机ip
     * @return string
     */
    public static function _createIp(){
        $arr_1 = [
            '218', '218', '66', '66', '218', '218',
            '60', '60', '202', '204', '66', '66',
            '66', '59', '61', '60', '222', '221',
            '66', '59', '60', '60', '66', '218',
            '218', '62', '63', '64', '66', '66',
            '122', '211'
        ];
        $randarr= mt_rand(0,count($arr_1)-1);
        $ip1id = $arr_1[$randarr];
        $ip2id=  round(rand(600000,  2550000)  /  10000);
        $ip3id=  round(rand(600000,  2550000)  /  10000);
        $ip4id=  round(rand(600000,  2550000)  /  10000);
        return  $ip1id . '.' . $ip2id . '.' . $ip3id . '.' . $ip4id;
    }

    /**
     * 创建随机验证吗
     * @param int $length 长度，默认为6位
     * @param bool $is_fill 是否填充，默认填充
     * @param int|string $fill_str 填充字符，默认使用0
     * @return string
     */
    public static function _buildNum($length = 6, $is_fill = true, $fill_str = 0)
    {
        $max_num = str_pad('9', $length, '9', STR_PAD_RIGHT);

        $rand = mt_rand(0, intval($max_num));
        if($is_fill){
            return str_pad($rand, $length, $fill_str, STR_PAD_LEFT);
        }

        return $rand;
    }

    /**
     * 获取请求的域名
     * @return string
     */
    public static function _getReferer(){

        $referer = '';

        if(isset($_SERVER['HTTP_ORIGIN']) && !empty($_SERVER['HTTP_ORIGIN'])){
            $referer = $_SERVER['HTTP_ORIGIN'];
        }

        if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
            $referer = $_SERVER['HTTP_REFERER'];
        }

        $prefix = 'http://';
        if($referer){
            strpos($referer, $prefix) === false ?: $referer = $prefix . $referer;
        }

        return $referer;
    }
}

