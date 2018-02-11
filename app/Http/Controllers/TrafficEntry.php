<?php

namespace App\Http\Controllers;

use App\Common\RedisCommon;
use App\Common\ReturnCommon;
use App\Common\TrafficTestCommon;
use App\Jobs\TrafficGet;
use Illuminate\Http\Request;
use Log;
use Exception;
use Illuminate\Support\Facades\Redis;

class TrafficEntry extends Controller
{
    /**
     * 接收行为数据到队列
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $res = TrafficTestCommon::testVisitList(2);
        try{
            $job = (new TrafficGet($res))->onQueue('visit');
            $this->dispatch($job);
        }catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }

}
