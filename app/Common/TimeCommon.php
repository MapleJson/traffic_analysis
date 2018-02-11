<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

class TimeCommon
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
     * 计算两时间相差多少
     * @param string $type day,hour,minute,second,随便传则为second
     * @param int $start_time 起始时间戳
     * @param int $end_time 结束时间戳
     * @return mixed
     */
    public static function timeDef($type, $start_time, $end_time)
    {
        $type_list = [
            'day'    => floor((strtotime(date('Y-m-d 00:00:00', $end_time)) - strtotime(date('Y-m-d 00:00:00', $start_time))) / 86400),
            'hour'   => floor((strtotime(date('Y-m-d H:00:00', $end_time)) - strtotime(date('Y-m-d H:00:00', $start_time))) / 3600),
            'minute' => floor((strtotime(date('Y-m-d H:i:00', $end_time)) - strtotime(date('Y-m-d H:i:00', $start_time))) / 60),
            'second' => $end_time - $start_time
        ];
        if(!isset($type_list[$type])){
            return $type_list['second'];
        }
        return $type_list[$type];
    }

    /**
     * 距离下个特定时间点秒数(仅控制分钟级别)
     * @param int $min 0为1个小时 其他为传入分钟，仅限能被60整除
     * @return bool|int|string
     */
    public static function _nextClock($min = 0)
    {
        //$time为下一个整点
        if($min == 0){
            $time = date('Y-m-d H:0:0',time());
            $time = strtotime($time);
            $time = strtotime('+1 hours', $time);
        }else{
            if(60%$min != 0){
                return false;
            }
            $time = date('i');
            $num = (int)($time/$min);
            $num = ($num+1) * $min;
            if($num == 60){
                return self::_nextClock(0);
            }else {
                $time = strtotime(date('Y-m-d H:' . $num . ':0', time()));
            }
        }
        return $time - time();
    }
}

