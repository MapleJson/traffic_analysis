<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

class MathCommon
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
     * 环比计算
     * @param float $now
     * @param float $last
     * @return float|int
     */
    public static function linkRelativeRatio($now = 0.00, $last = 0.00)
    {
        $res = ($last + 0) ? ($now - $last) / $last : 0;
        return $res;
    }

    /**
     * 百分比转化
     * @param $num
     * @return string
     */
    public static function rateGet($num)
    {
        return round($num * 100, 2) . '%';
    }

    /**
     * 二维数组求一个键的和
     * @param array $an_array 二维数组
     * @param string $key  二维数组子数组里一个键
     * @return array|float|int
     */
    public static function sumArrayKey($an_array, $key)
    {
        $ty = array_column($an_array, $key);
        $ty = array_sum($ty);
        return $ty;
    }

    /**
     * 生成数字验证码
     *
     * @param int $length 验证码长度，默认6位,最大11位
     * @return string
     */
    public static function _createNumCode($length = 6)
    {
        $length <= 11 ?: $length = 11;
        $rand_max = pow(10, $length) - 1;

        return str_pad(mt_rand(0, $rand_max), $length, '0', STR_PAD_LEFT);
    }
}

