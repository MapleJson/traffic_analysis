<?php

/**
 * date:2018-01-09
 * author:fang
 * desc:pv uv统计落库，event统计落库
 * 每个小时的第一分钟执行
 */

namespace App\Http\Controllers;

use App\Common\RedisCommon;
use DB;
use Log;
use Mockery\Exception;
//use Redis;
use Illuminate\Support\Facades\Redis;

class TrafficLandDb extends Controller
{
    protected $equipment_type_list = [
        'pc'        => 1,
        'wap'       => 2,
        'android'   => 3,
        'ios'       => 4,
    ];
    protected $event_cate_list = [
        1 => '访问',
        2 => '咨询',
        3 => '模拟开户',
        4 => '真实开户',
        5 => '登录',
        6 => '入金'
    ];
    protected $event_action_list = [
        'click', 'down'
    ];

    protected $time, $date, $hour, $Db;

    public function __construct()
    {
        $delay_hour = '-1';
        $this->time = date('YmdH',strtotime($delay_hour . ' hour'));
        $this->date = date('Ymd',strtotime($delay_hour . ' hour'));
        $this->hour = date('H', strtotime($delay_hour . ' hour'));
        $this->Db = DB::connection('dmpmysql');
    }

    /**
     * pv,uv落库
     * @return bool
     */
    public function trafficLandDb()
    {
        $list = $this->getTrafficFromRedis();
        return $this->insertTrafficData($list);
    }

    /**
     * 点击事件落库
     * @return bool
     */
    public function eventLandDb()
    {
        $list = $this->getEventFromRedis();
        return $this->insertEventData($list);
    }

    /**
     * 取redis点击事件数据组装返回list
     * @return array
     */
    public function getEventFromRedis()
    {
        $total_list = [];
        foreach ($this->equipment_type_list as $type => $v){
            $redis_name = RedisCommon::totalEventRedisName($type);
            $res = Redis::hGetAll($redis_name);

            if($res){
                $total_list = array_merge($total_list, $this->totalEventList($type, $res));
            }
        }
        return $total_list;
    }

    /**
     * 取redis pv uv数据并组装返回list
     * @return array
     */
    private function getTrafficFromRedis()
    {
        $total_list = [];
        foreach ($this->equipment_type_list as $k => $v){
            $pv_redis_name = RedisCommon::totalTrafficRedisName($k, 'pv');
            $pv_res = Redis::hGetAll($pv_redis_name);

            if(!$pv_res){
                continue;
            }

            $uv_redis_name = RedisCommon::totalTrafficRedisName($k, 'uv');
            $uv_res = Redis::hGetAll($uv_redis_name);

            unset($pv_res['pv_sum']);

            $total_list = array_merge($total_list, $this->totalTrafficList($k, $pv_res, $uv_res));
        }
        return $total_list;
    }

    /**
     * 落traffic_user_stat表的数据组装
     * @param $type
     * @param $page_name
     * @return mixed
     */
    private function userPvList($type, $page_name)
    {
        $redis_name = RedisCommon::userTrafficRedisName($type, $page_name);
        $res = Redis::hGetAll($redis_name);

        $list = [];
        foreach ($res as $k => $v){
            $list[] = [
                'user_id' => $k,
                'pv' => $v,
                'update_time' => time(),
                'create_time' => time()
            ];
        }

        return $list;
    }

    /**
     * 落event_user_stat表的数据组装
     * @param $type
     * @param $cate
     * @return mixed
     */
    private function userEventList($type, $cate)
    {
        $redis_name = RedisCommon::userEventRedisName($type, $cate);
        $res = Redis::hGetAll($redis_name);
        $list = [];
        foreach ($res as $k => $v){
            $list[] = [
                'user_id' => $k,
                'num' => $v,
                'update_time' => time(),
                'create_time' => time()
            ];
        }

        return $list;
    }

    /**
     * 落traffic_total_stat表的数据组装
     * @param $type
     * @param $pv_res
     * @param $uv_res
     * @return array
     */
    private function totalTrafficList($type, $pv_res, $uv_res)
    {
        $total_list = [];
        foreach ($pv_res as $k => $v){
            $total_list[] = [
                'type' => $this->equipment_type_list[$type],
                'pv' => $v,
                'uv' => isset($uv_res[$k]) ? $uv_res[$k] : 0,
                'page_name' => $k,
                'date' => $this->date,
                'hour' => $this->hour,
                'time' => strtotime(sprintf('%s %s:00:00',$this->date, $this->hour)),
                'update_time' => time(),
                'create_time' => time(),
                'user_list' => $this->userPvList($type, $k)
            ];
        }

        return $total_list;
    }

    /**
     * 落event_total_stat表数据组装
     * @param $type
     * @param $list
     * @return array
     */
    private function totalEventList($type, $list)
    {
        $res = [];
        foreach ($list as $cate => $num){
            $res[] = [
                'type' => $this->equipment_type_list[$type],
                'category' => $cate,
                'name' => $this->event_cate_list[$cate],
                'num'   => $num,
                'date' => $this->date,
                'hour' => $this->hour,
                'time' => strtotime(sprintf('%s %s:00:00',$this->date, $this->hour)),
                'update_time' => time(),
                'create_time' => time(),
                'user_list'   => $this->userEventList($type, $cate)
            ];
        }
        return $res;
    }

    /**
     * 插入pvuv数据
     * @param $data
     * @return bool
     */
    private function insertTrafficData($data)
    {
        if(!count($data)){
            return 'none data';
        }
        $user_list = [];
        DB::beginTransaction();
        try{
            foreach ($data as $k => $v){
                $single_user_list = $v['user_list'];
                unset($v['user_list']);
                $id = $this->Db->table('traffic_total_stat')->insertGetId($v);
                foreach ($single_user_list as $key => $val){
                    $single_user_list[$key]['traffic_id'] = $id;
                }
                $user_list = array_merge($user_list, $single_user_list);
            }
            $this->Db->table('traffic_user_stat')->insert($user_list);
            DB::commit();

            $key_rep = 'traffic*' . $this->time;
            RedisCommon::delRedis($key_rep);
            return 'success';
        }catch(Exception $e){
            DB::rollBack();
            return 'exception' . $e->getMessage();
        }
    }

    /**
     * 插入点击事件数据
     * @param $data
     * @return bool
     */
    private function insertEventData($data)
    {
        if(!count($data)){
            return 'none data';
        }

        $user_list = [];
        DB::beginTransaction();
        try{
            foreach ($data as $k => $v){
                $single_user_list = $v['user_list'];
                unset($v['user_list']);
                $id = $this->Db->table('event_total_stat')->insertGetId($v);
                foreach ($single_user_list as $key => $val){
                    $single_user_list[$key]['event_id'] = $id;
                }
                $user_list = array_merge($user_list, $single_user_list);
            }
            $this->Db->table('event_user_stat')->insert($user_list);
            DB::commit();

            $key_rep = 'event*' . $this->time;
            RedisCommon::delRedis($key_rep);
            return 'success';
        }catch(Exception $e){
            DB::rollBack();
            return 'exception' . $e->getMessage();
        }
    }
}
