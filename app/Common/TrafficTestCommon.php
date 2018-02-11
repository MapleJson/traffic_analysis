<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

use Illuminate\Support\Facades\Redis;

class TrafficTestCommon
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
     * 返回模拟用户行为
     * @param int $length
     * @return array
     */
    public static function testVisitList($length = 1)
    {
        $list = [
            'list' => [],
            'length' => $length
        ];
        for($i = 0; $i < $length; $i++){
            //66%几率为访问，33%几率为点击
            $detail = rand(0, 2) ? self::testSingleVisit() : self::testSingleClick();
            array_push($list['list'], $detail);
        }
        return $list;
    }

    /**
     * 模拟的页面访问(单次)
     * @return array
     */
    public static function testSingleVisit()
    {
        $detail = self::testSingleDefault();
        $detail['behavior_type'] = Constant::BE_TYPE_VISIT;//行为类型
        return $detail;
    }

    /**
     * 模拟埋点的点击事件(单次)
     * @return array
     */
    public static function testSingleClick()
    {
        $detail = self::testSingleDefault();
        $cate_rand = rand(1, 2);
        $action_rand = rand(1, 3);
        $detail['behavior_type'] = Constant::BE_TYPE_CLICK;//行为类型
        $detail['event_category'] = 'category' . $cate_rand;//点击事件的分类
        $detail['event_action'] = 'action' . $action_rand;//点击事件的动作
        $detail['event_name'] = 'name' . $action_rand;//对点击事件动作的解释，1对1解释

        return $detail;
    }

    /**
     * 行为产生的公用信息
     * @return array
     */
    private static function testSingleDefault()
    {
        $user_id = 'id'.rand(1, 30);
        $rand_page = rand(1, 4);
        $detail = [
            'user_id'           => $user_id,//页面生成的用户id，作为用户唯一标示
            'ip'                => self::idToIp($user_id),//用户id对应ip
            'equipment_type'    => self::idToEqType($user_id),//用户id对应设备
            'page_title'        => 'page' . $rand_page,//页面标题
            'page_url'          => sprintf('http://%s.test.com/%s.html', $rand_page, rand(1, 4)),//访问的网址
            'web_id'            => 1,//网站id，多站点监控时可以用这个区分
            'visit_time'        => time() - rand(1, 5),//访问时间，模拟延迟到达所以减去一定时间
        ];
        return $detail;
    }

    /**
     * user_id和ip一对一配对
     * 模拟真实环境
     * @param $user_id
     * @return string
     */
    private static function idToIp($user_id)
    {
        $redis_name = RedisCommon::getRedisPrefix() . '_id_to_ip';
        $ip = Redis::hGet($redis_name, $user_id);
        if($ip){
            return $ip;
        }
        $ip = Common::_createIp();
        Redis::hSetNx($redis_name, $user_id, $ip);

        //缓存到0点
        $expire_time = strtotime(date('Y-m-d') . '+1 day') - time();
        Redis::expire($redis_name, $expire_time);
        return $ip;
    }

    /**
     * user_id和equipment_type一对一配对
     * 模拟真实环境
     * @param $user_id
     * @return string
     */
    private static function idToEqType($user_id)
    {
        $redis_name = RedisCommon::getRedisPrefix() . '_id_to_eq_type';
        $equipment_type = Redis::hGet($redis_name, $user_id);
        if($equipment_type){
            return $equipment_type;
        }
        $equipment_type = rand(Constant::EQ_TYPE_PC, Constant::EQ_TYPE_IOS);
        Redis::hSetNx($redis_name, $user_id, $equipment_type);

        //缓存到0点
        $expire_time = strtotime(date('Y-m-d') . '+1 day') - time();
        Redis::expire($redis_name, $expire_time);
        return $equipment_type;
    }
}

