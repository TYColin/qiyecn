<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 2018/9/9
 * Time: 22:26
 */

namespace app\admin\controller;

use app\admin\common\Common;
use think\Db;
use think\Loader;
use think\Request;

class Manager extends Common
{
    public function update(Request $request,$id){
        if($request->isPost()){
            $data = $request->post();
            $validate = Loader::validate('Manager');

            if(isset($data['account'])){
                unset($data['account']);
            }
            if (!$data['email']){
                unset($data['email']);
            }elseif (!$validate->scene('email')->check($data)){
                $this -> error($validate->getError());
            }

            if (!$data['password']){
                unset($data['password']);
                unset($data['repassword']);
            }elseif (!$validate->scene('password')->check($data)){
                $this -> error($validate->getError());
            }else{
                $data['password'] = md5($data['password']);
            }

            unset($data['repassword']);

            if ($data['state'] == 0 && $id == 1){
                $this -> error('超级管理员不能锁定');
            }else{

                $res = Db::name('manager')->where('id',$id)->update($data);

                if (!$res){
                    $this -> error('管理员修改失败');
                }
                $this->success('管理员修改成功','Manager/managerList');

            }

        }
        return $this->fetch('update');
    }
    public function delete($id){
        if ($id == 1){
            $this -> error('超级管理员不允许删除');
        }else{
            $result = Db::name('manager')->where('id',$id)->delete();
            if (!is_null($result)){
                $this->success('删除成功','manager/managerList');
            }
        }
    }
    public function managerList(){
        $result = Db::name('manager')->paginate(5);
        $count = Db::name('manager')->count();

        $this->assign('manager',$result);
        $this->assign('count',$count);

        return $this->fetch('manager_list');
    }
    public function add(Request $request)
    {
        if($request->isPost()){

            $data = $request->param();

            $validate = Loader::validate('Manager');

            if(!$validate->scene('add')->check($data)) {

                //$message = $validate->getError();
                $this -> error($validate->getError(),'Manager/add');

                //$status = 0;

            }else{
                //使用unset()函数销毁字段repassword
                unset($data['repassword']);

                $data['password'] = md5($data['password']);

                $res = Db::name('manager')->insert($data);
                if (!$res){
//                    $message = "添加失败";
//                    $status = 0;
                    $this -> error('添加失败','Manager/add');
                }else{
//                    $message = "添加成功";
//                    $status = 1;
                    $this -> success('添加成功','Manager/add');
                }

            }
//            return ['status' => $status, 'message' => $message];
        }
        return $this->fetch('add');
    }

    //管理员密码修改
    public function changePassword(Request $request){
        //1.判断是否为post提交
        if ($request->isPost()){
            $data = $request->post();

            //2.验证密码是否符合规则
            $validate = Loader::validate();
            if (!$validate->scene('password')->check($data)){
                $this->error($validate->getError());
            }

            //3.验证旧密码是否和数据库中一致
            $res = Db::name('manager')->where('account',session('login_name','','admin'))->find();
            if (md5($data['oldpassword'])!=$res['password']){
                $this->error('旧密码不正确');
            }

            //4.将新密码写入数据库
            $pass = Db::name('manager')->where('account',session('login_name','','admin'))->setField('password',md5($data['password']));
            if (!$pass){
                $this->error('密码修改失败');
            }else{
                $this->error('密码修改成功');
            }
        }
        return $this->fetch('change_password');
    }
}