<?php

namespace App\Http\Controllers;

use App\Common\ReturnCommon;
use App\Common\TrafficTestCommon;
use App\Common\CurlCommon;
use App\Jobs\TrafficGet;
use Illuminate\Http\Request;
use Redis;

class Test extends Controller
{
    public function test()
    {
        $url = 'http://fy.traffic.com/test2';
        $data = [
            'name' => 'fang'
        ];
        $method = 'DELETE';
        $res = CurlCommon::curlJson($url, $data, $method);
        return $res;
        /*$list = TrafficTestCommon::testVisitList(5);
        $class = new TrafficGet($list);
        $class->handle();*/
    }

    public function test2(Request $request)
    {
        $res = $request -> all();
        return $res;
    }


    public $str = 'ababcbacadefegdehijhklij';//目标字符串
    public $foreach_times = 0;//经历for循环次数

    /**
     * 分块，每块字母只在块中出现
     * @return array
     */
    public function strCut()
    {
        $split_num = [];
        echo $this->str . '<br>';//打印目标字符串
        //如果分块后字符串有剩余就一直分块
        while($this->str != ''){
            $sub_str = $this->sub();
            $split_num[] = strlen($sub_str);//将分块长度计入$split_num
        }
        echo $this->foreach_times . '<br>';//打印遍历次数
        return $split_num;
    }
    /**
     * 更改$this->str为剩余部分，返回分块的部分
     * 大概思路，第一个字符出现的最后位置为分块预期
     * 遍历这个预期分块，发现不满足则将不满足的字符出现最后位置为分块预期
     * 并从错误位置开始往后遍历
     * 如abbabccdd，第一次预期为abba，发现b不满足，修正预期为abbab，满足，返回abbab，字符串修改为ccdd
     * strrpos为寻找特定字符出现的最后位置
     * substr为截取字符串
     * strlen为获取字符串长度
     * @param int $index 遍历开始下标
     * @return string 分出来的块
     */
    private function sub($index = 0)
    {
        $last_index = strrpos($this->str, $this->str[$index]);
        for($i = $index + 1; $i < $last_index; $i++){
            $this->foreach_times += 1;
            $this_index = strrpos($this->str, $this->str[$i]);
            if($this_index > $last_index){
                return $this->sub($i);
            }
        }
        //本处使用主要为了将目标字符串分块
        //如abbaccdd 返回abba,$this->str则变成ccdd
        $sub_str = substr($this->str, 0, $last_index + 1);
        $this->str = substr($this->str, $last_index + 1, strlen($this->str));
        return $sub_str;
    }
}