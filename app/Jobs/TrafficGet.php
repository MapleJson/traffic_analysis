<?php

namespace App\Jobs;

use App\Common\Constant;
use App\Common\RedisCommon;
use App\Common\ReturnCommon;
use App\Http\Model\UserSourceModel;
use App\Http\Model\UserVisitModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Exception;

class TrafficGet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 120;

    private $visit_detail;

    /**
     * Create a new job instance.
     * @param $visit_detail
     * @return void
     */
    public function __construct($visit_detail = [])
    {
        $this->visit_detail = $visit_detail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $length =  $this->visit_detail['length'];
        $list =  $this->visit_detail['list'];

        $this->visitRecordLandDb($list);
        $this->dataAnalysis($list);
    }

    /**
     * @author fangyi
     *
     * 单条数据分析入口
     * @param $list
     */
    private function dataAnalysis($list)
    {
        foreach ($list as $detail){
            try{
                $this->trafficAnalysis($detail);
                $this->eventAnalysis($detail);
            }catch(Exception $e){}
        }
    }

    /**
     * @author fangyi
     *
     * 点击事件分析
     * @param $detail
     */
    private function eventAnalysis($detail)
    {

        if($detail['behavior_type'] != Constant::BE_TYPE_CLICK){
            return;
        }

        $type = Constant::$equipment_list[$detail['equipment_type']];
        $cate = $detail['event_category'];
        $user_id = $detail['user_id'];

        RedisCommon::clickCalc($type, $cate, $user_id);
    }

    /**
     * @author fangyi
     *
     * pv,uv流量分析
     * @param $detail
     */
    private function trafficAnalysis($detail)
    {
        if($detail['behavior_type'] != Constant::BE_TYPE_VISIT) {
            return;
        }
        $host = parse_url($detail['page_url']);
        $host = $host['host'];

        $type = Constant::$equipment_list[$detail['equipment_type']];

        RedisCommon::pvCalc($type, $host, $detail['user_id']);
        RedisCommon::uvCalc($type, $host, $detail['user_id']);
    }

    /**
     * @author fangyi
     *
     * 详情记录
     * @param $list
     */
    private function visitRecordLandDb($list)
    {
        $user_visit_list = [];
        $user_source_list = [];
        $user_visit_model = new UserVisitModel();
        $user_source_model = new UserSourceModel();
        foreach ($list as $detail){
            $user_id = $detail['user_id'];
            $web_id = $detail['web_id'];
            $user_visit_list[] = $user_visit_model->defaultData($detail);

            if(!isset($user_source_list[$user_id])){
                if(!$user_source_model->isExist($web_id, $user_id)){
                    $user_source_list[$user_id] = $user_source_model->defaultData($detail);
                }
            }
        }

        $user_visit_model->insert($user_visit_list);
        $user_source_model->insert($user_source_list);
    }
}
