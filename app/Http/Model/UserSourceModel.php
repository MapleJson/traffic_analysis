<?php

namespace App\Http\Model;


use App\Common\Constant;
use App\Common\ReturnCommon;

class UserSourceModel extends Model
{
    protected $table = 'user_source';

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
            'page_url'          => isset($detail['page_url']) ? $detail['page_url'] : '',
            'source'            => isset($detail['source']) ? $detail['source'] : '',
            'medium'            => isset($detail['medium']) ? $detail['medium'] : '',
            'campaign'          => isset($detail['campaign']) ? $detail['campaign'] : '',
            'content'           => isset($detail['content']) ? $detail['content'] : '',
            'term'              => isset($detail['term']) ? $detail['term'] : '',
            'date'              => isset($detail['visit_time']) ? date('Ymd', $detail['visit_time']) : date('Ymd'),
            'time'              => isset($detail['visit_time']) ? $detail['visit_time'] : time(),
        ];
        self::fillDataTime($data);
        return $data;
    }

    /**
     * 查找是否存在
     * @param $web_id
     * @param $user_id
     * @return bool
     */
    public function isExist($web_id, $user_id)
    {
        $res = $this->where('user_id', $user_id)
            ->where('web_id', $web_id)
            ->first();
        return ($res ? true : false);
    }
}
