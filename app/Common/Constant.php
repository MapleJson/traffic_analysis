<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common;

class Constant
{
    const BE_TYPE_VISIT = 1;//访问
    const BE_TYPE_CLICK = 2;//点击

    const EQ_TYPE_PC = 1;//设备PC
    const EQ_TYPE_WAP = 2;//设备WAP
    const EQ_TYPE_ANDROID = 3;//设备Android
    const EQ_TYPE_IOS = 4;//设备IOS
    const EQ_TYPE_OTHER = 5;//未知设备

    const EVENT_TYPE_ASK = 1;//咨询
    const EVENT_TYPE_ANALOG_OPEN = 2;//模拟开户
    const EVENT_TYPE_REAL_OPEN = 3;//真实开户
    const EVENT_TYPE_LOG = 4;//登录
    const EVENT_TYPE_DEPOSIT = 5;//入金

    //设备对应数字和文字
    public static $equipment_list = [
        self::EQ_TYPE_PC      => 'pc',
        self::EQ_TYPE_WAP     => 'wap',
        self::EQ_TYPE_ANDROID => 'android',
        self::EQ_TYPE_IOS     => 'ios',
        self::EQ_TYPE_OTHER   => 'other',
    ];
}

