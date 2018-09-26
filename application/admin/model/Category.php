<?php

namespace app\admin\model;

use think\Model;

class Category extends Model
{
    //获取栏目列表
    /**
     * @param int $pid 上级栏目标识
     * @param int $level 层级数
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCategory($pid=0,$level=-1){
        //获取$pid的所有数据
        $res = self::where('pid',$pid)->select();
        //定义一个静态数组，用于存储有用的数据
        static $arr=array();
        //每次调用，将层级数加1
        $level += 1;
        if ($level == 0){
            $str = "";
        }else{
            $str = "|";
        }
        //遍历取出$res中的所有数据
        foreach ($res as $v){
            //创建一个临时数组，保存一条有用的数据,
            $tmp_arr=array();
            $tmp_arr['id']=$v['id'];
            //str_repeat()函数循环输出给定字符
            $tmp_arr['name']=$str.str_repeat("----",$level).$v['name'];
            $tmp_arr['pid']=$v['pid'];
            //因为前端数据输出时使用 {volist}, 是一个二维数组，这里需要返回一个二维数组，每调用一次将结果添加到$arr中
            $arr[]=$tmp_arr;
            //递归调用本方法，传入的$pid为当前数据的id
            self::getCategory($v['id'],$level);
        }
        return $arr;
    }


    //优化获取栏目列表,通过传入数据表中的数据进行遍历，可显著提交执行效率，降低数据库压力

    /**
     * @param $data 所有栏目数据
     * @param int $pid 上级栏目标识
     * @param int $level 层级数
     * @return array
     */
    public static function getCateall($data,$pid=0,$level=-1){
        //获取$pid的所有数据
        //$res = self::where('pid',$pid)->select();
        //定义一个静态数组，用于存储有用的数据
        static $arr=array();
        //每次调用，将层级数加1
        $level += 1;
        if ($level == 0){
            $str = "";
        }else{
            $str = "|";
        }
        //遍历取出$res中的所有数据
        foreach ($data as $v){
            if ($pid == $v['pid']){
                //创建一个临时数组，保存一条有用的数据,
                $tmp_arr=array();
                $tmp_arr['id']=$v['id'];
                //str_repeat()函数循环输出给定字符
                $tmp_arr['name']=$str.str_repeat("----",$level).$v['name'];
                $tmp_arr['pid']=$v['pid'];
                //因为前端数据输出时使用 {volist}, 是一个二维数组，这里需要返回一个二维数组，每调用一次将结果添加到$arr中
                $arr[]=$tmp_arr;
                //递归调用本方法，传入的$pid为当前数据的id
                self::getCateall($data,$v['id'],$level);
                //有用数据保存后将其销毁
                unset($v);
            }

        }
        return $arr;
    }

    //获取栏目列表--引用方式
    public static function getCate($data,$pid=0,$level=-1,&$arr=[]){
        //获取$pid的所有数据
        //$res = self::where('pid',$pid)->select();
        //定义一个静态数组，用于存储有用的数据
        //static $arr=array();
        //每次调用，将层级数加1
        $level += 1;
        if ($level == 0){
            $str = "";
        }else{
            $str = "|";
        }
        //遍历取出$res中的所有数据
        foreach ($data as $v){
            if ($pid == $v['pid']){
                //创建一个临时数组，保存一条有用的数据,
                $tmp_arr=array();
                $tmp_arr['id']=$v['id'];
                //str_repeat()函数循环输出给定字符
                $tmp_arr['name']=$str.str_repeat("----",$level).$v['name'];
                $tmp_arr['pid']=$v['pid'];
                $tmp_arr['sort']=$v['sort'];
                //因为前端数据输出时使用 {volist}, 是一个二维数组，这里需要返回一个二维数组，每调用一次将结果添加到$arr中
                $arr[]=$tmp_arr;
                //递归调用本方法，传入的$pid为当前数据的id
                self::getCate($data,$v['id'],$level,$arr);
                //有用数据保存后将其销毁
                unset($v);
            }

        }

    }

    //更新栏目排序
    public static function sort($data){
        if ($data){
            foreach ($data as $id=>$v){
                self::where('id',$id)->update(['sort'=>$v]);
            }

        }
        return true;
    }
}
