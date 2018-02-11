<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class RedisCommon
{
    //保存例实例在此属性中
    private static $_instance;
    //redis前缀
    private static $redis_prefix = 'redis';

    //单例方法
    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 获取redis前缀
     * @return string
     */
    public static function getRedisPrefix()
    {
        return self::$redis_prefix;
    }

    /**
     * 清空本小时段redis
     * @param $key_rep
     */
    public static function delRedis($key_rep = '')
    {
        $list = Redis::keys(self::$redis_prefix . $key_rep);
        Log::info('删除redis', $list);
        foreach ($list as $k => $v){
            Redis::del($v);
        }
    }

    /**
     * pvuv总redis名称拼接
     * @param int|string $equipment_type
     * @param string $traffic_type pv|uv
     * @param string $date YmdH
     * @return string
     */
    public static function totalTrafficRedisName($equipment_type, $traffic_type, $date = '')
    {
        $redis_name = sprintf('%s_traffic_total_%s_%s_%s', self::$redis_prefix, $equipment_type, $traffic_type, $date ? $date : date('YmdH'));
        return $redis_name;
    }

    /**
     * 用户访问页面redis名称拼接
     * @param int|string $equipment_type
     * @param string $page_name
     * @param string $date YmdH
     * @return string
     */
    public static function userTrafficRedisName($equipment_type, $page_name, $date = '')
    {
        $redis_name = sprintf('%s_traffic_user_pv_%s_%s_%s', self::$redis_prefix, $equipment_type, $page_name, $date ? $date : date('YmdH'));
        return $redis_name;
    }

    /**
     * 点击事件总redis名称拼接
     * @param int|string $equipment_type
     * @param string $date YmdH
     * @return string
     */
    public static function totalEventRedisName($equipment_type, $date = '')
    {
        $redis_name = sprintf('%s_event_total_%s_%s', self::$redis_prefix, $equipment_type, $date ? $date : date('YmdH'));
        return $redis_name;
    }

    /**
     * 用户点击事件redis名称拼接
     * @param int|string $equipment_type
     * @param int $cate
     * @param string $date YmdH
     * @return string
     */
    public static function userEventRedisName($equipment_type, $cate, $date = '')
    {
        $redis_name = sprintf('%s_event_user_%s_%s_%s', self::$redis_prefix, $equipment_type, $cate, $date ? $date : date('YmdH'));
        return $redis_name;
    }

    /**
     * 用户总id列表redis名称拼接
     * @param int|string $equipment_type
     * @param int $page
     * @param string $date YmdH
     * @return string
     */
    public static function userTotalRedisName($equipment_type, $page, $date = '')
    {
        $redis_name = sprintf('%s_traffic_id_list_%s_%s_%s', self::$redis_prefix, $equipment_type, $page, $date ? $date : date('Ymd'));
        return $redis_name;
    }

    /**
     * @author fangyi
     *
     * pv统计
     * @param string $type
     * @param string $host
     * @param string $user_id
     */
    public static function pvCalc($type, $host, $user_id)
    {
        self::trafficInc('pv', $type, $host);
        self::userTrafficInc($type, $host, $user_id);

    }

    /**
     * @author fangyi
     *
     * uv统计
     * @param string $type
     * @param string $host
     * @param string $user_id
     */
    public static function uvCalc($type, $host, $user_id)
    {
        if(self::uvVerify($type, $host, $user_id)){
            self::trafficInc('uv', $type, $host);
        }
    }

    /**
     * @author fangyi
     *
     * uv统计
     * @param $type
     * @param $cate
     * @param $user_id
     */
    public static function clickCalc($type, $cate, $user_id)
    {
        self::eventRedisInc($type, $cate);
        self::userEventInc($type, $cate, $user_id);
    }

    /**
     * @author fangyi
     *
     * pv,uv流量增加公用方法
     * key = 设备类型 + pv|uv + 日期 + 小时
     * field = 页面， value = pv|uv量
     * 特殊field pv_sum|uv_sum 用于存当前key的pv|uv总量
     *
     * @param string $traffic_type
     * @param string type
     * @param string $page
     */
    private static function trafficInc($traffic_type = 'pv', $type, $page)
    {
        $redis_hash_name = self::totalTrafficRedisName($type, $traffic_type);

        $exists = Redis::hLen($redis_hash_name);

        Redis::hIncrBy($redis_hash_name, $page, 1);
        Redis::hIncrBy($redis_hash_name, $traffic_type . '_sum', 1);

        $exists ?: self::expire($redis_hash_name);
    }

    /**
     * @author fangyi
     *
     * 用户维度pv统计
     * key = 设备类型 + 页面 + 日期 + 小时
     * field = 用户访问id， value = pv量
     * 特殊field sum 用于存当前key的pv总量
     *
     * @param string $type
     * @param string $page
     * @param string $id
     */
    private static function userTrafficInc($type, $page, $id)
    {
        $redis_hash_name = self::userTrafficRedisName($type, $page);

        $exists = Redis::hLen($redis_hash_name);

        Redis::hIncrBy($redis_hash_name, $id, 1);

        $exists ?: self::expire($redis_hash_name);
    }


    /**
     * @author fangyi
     *
     * 点击事件Redis自增
     * @param $type
     * @param $cate
     */
    private static function eventRedisInc($type, $cate)
    {
        $redis_name = self::totalEventRedisName($type);

        $exists = Redis::hLen($redis_name);

        Redis::hIncrBy($redis_name, $cate, 1);

        $exists ?: self::expire($redis_name);
    }

    /**
     * @author fangyi
     *
     * 用户维度点击事件统计
     * key = 设备类型 + 点击事件分类 + 日期 + 小时
     * field = 用户访问id， value = 点击数
     *
     * @param string $type
     * @param string $cate
     * @param string $id
     */
    private static function userEventInc($type, $cate, $id)
    {
        $redis_hash_name = self::userEventRedisName($type, $cate);
        $exists = Redis::hLen($redis_hash_name);

        Redis::hIncrBy($redis_hash_name, $id, 1);

        $exists ?: self::expire($redis_hash_name);
    }

    /**
     * @author fangyi
     *
     * uv去重验证
     * @param string $type
     * @param string $page
     * @param string $id
     * @return bool
     */
    private static function uvVerify($type, $page, $id)
    {
        $redis_name = self::userTotalRedisName($type, $page);

        $res = Redis::sIsMember($redis_name, $id);
        if($res){
            return false;
        }
        $exists = Redis::sCard($redis_name);
        Redis::sAdd($redis_name, $id);

        $exists ?: self::expire($redis_name);

        return true;
    }

    /**
     * 缓存时间
     * @param $redis_name
     * @param $expire_time
     */
    private static function expire($redis_name, $expire_time = 0)
    {
        $expire_time ?: $expire_time = strtotime(date('Ymd 23:59:59')) - time() + 1;
        Redis::expire($redis_name, $expire_time);
    }
}

