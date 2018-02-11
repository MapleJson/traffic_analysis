<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    const CREATED_AT = 'create_time';//设置数据库默认创建时间字段
    const UPDATED_AT = 'update_time';//设置数据库默认更新时间字段
    protected $dateFormat = 'U';//默认存入的时间格式为时间戳

    /**
     * 自动填充数据的时间
     * 供批量插入使用
     * @param $data
     */
    protected static function fillDataTime(&$data)
    {
        $data['update_time'] = time();
        $data['create_time'] = time();
    }
}
