<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 2018/9/16
 * Time: 15:31
 */

namespace app\admin\controller;


use app\admin\common\Common;
use think\Db;
use think\Request;

class Config extends Common
{
    public function index(Request $request){

        //查询数据库中配置信息
        $config = Db::name('config')->find();
        $config=json_decode($config['config']);

        //更新数据
        if ($request->isPost()){
            $data = $request->post();

            //文件上传，需要在前端表单中添加:enctype="multipart/form-data" 属性
            //使用$request->file（）方法获取二进制对象
            $file = $request->file('logoimg');
            if ($file){
                $data['logo'] = $this->uploadimg($file);
            }
            $res = Db::name('config')->find();
            if (!$res){
                Db::name('config')->insert(['config'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
            }else{
                $result = Db::name('config')->where('id',$res['id'])->update(['config'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
                if (!$result){
                    $this->error('更新失败');
                }
                $this->success('更新成功');
            }

        }
        //变量赋值
        $this->assign('config',$config);
        //记得return
        return $this->fetch('index');
    }

    protected function uploadimg($file){
        //上传图片，move()方法的第二个参数用于定义路径和文件名
        if ($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads','logo');
            if ($info){
                return $info->getSaveName();
            }else{
                return $info->getError();
            }
        }
    }
}