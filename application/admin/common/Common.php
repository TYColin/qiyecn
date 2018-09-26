<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 2018/9/13
 * Time: 20:24
 */

namespace app\admin\common;


use think\Controller;
use think\Request;


class Common extends Controller
{
    /**
     * 公共控制器
     * 初始化方法
     */
    protected function _initialize()
    {
        parent::_initialize(); // 继承父类的初始化方法

        //define('LOGIN_NAME',Session::get('login_name','admin'));

        //用户未登录
        if(!session('login_name', '', 'admin') || !session('login_id', '', 'admin')){
            $request = Request::instance();
            //获取当前控制器
            $controller = $request->controller();
            //获取当前操作
            $action = $request->action();
            if ($controller === "Index" && $action === "index"){
                $this->redirect('login/index');
            }
            $this->error("未登录，不允许访问",'login/index');

        }

        //将用户登录信息分配给变量
        $this->assign('admin',session('login_name','','admin'));
    }

}