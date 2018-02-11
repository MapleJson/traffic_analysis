<?php

namespace App\Http\Model;


use App\Common\Constant;
use App\Common\ReturnCommon;

class UserVisitModel extends Model
{
    protected $table = 'user_visit';

    /**
     * 默认数据
     * @param $detail
     * @return array
     */
    public function defaultData($detail)
    {
        $data = [
            'web_id'            => isset($detail['web_id']) ? $detail['web_id'] : 0,
            'equipment_type'    => isset($detail['equipment_type']) ? $detail['equipment_type'] : Constant::EQ_TYPE_OTHER,
            'user_id'           => isset($detail['user_id']) ? $detail['user_id'] : '',
            'page_title'        => isset($detail['page_title']) ? $detail['page_title'] : '',
            'page_url'          => isset($detail['page_url']) ? $detail['page_url'] : '',
            'event_category'    => isset($detail['event_category']) ? $detail['event_category'] : '',
            'event_action'      => isset($detail['event_action']) ? $detail['event_action'] : '',
            'event_name'        => isset($detail['event_name']) ? $detail['event_name'] : '',
            'date'              => isset($detail['visit_time']) ? date('Ymd', $detail['visit_time']) : date('Ymd'),
            'time'              => isset($detail['visit_time']) ? $detail['visit_time'] : time(),
        ];
        self::fillDataTime($data);
        return $data;
    }
}
