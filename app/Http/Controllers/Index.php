<?php

namespace App\Http\Controllers;

use App\FunCommon;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Model\TrafficTotalStatModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Redis;
use App\Jobs\BehaviorData;
use DB;
use Log;
use Mockery\Exception;
use function PHPSTORM_META\type;

class Index extends Controller
{

    /**
     * 接收行为数据到队列
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $res = $request->all();
        return $res;
        $message = 'fail';
        try {
            //$str_before = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
            $str_before = $this->str_before;
            if (empty($str_before)) {
                return '行为参数不能为空';
            }
            $str = urldecode($str_before);//json转码
            $str = json_decode($str, true);//把字符串转成数组
            if(empty($str)){
                $str = json_decode($str_before,true);
            }
            if (is_array($str)) {
                $str = json_encode($str);
                Log::info('request:' . $str);
                Redis::rpush('behavior', $str);//behavior   //右进左出原则
                $job = (new BehaviorData())->onQueue('behavior');
                $this->dispatch($job);
                $message = 'success';
            } else {
                return $str;
            }
        } catch (Exception $e) {
            Log::info('接收行为数据错误:' . $e->getMessage());
        }
        return $message;
    }

    public function getredisto()
    {
        $data = Redis::rpop('behavior');
        var_dump($data);
//        retrun $data;
    }

}
