<?php
namespace app\admin\controller;




use app\admin\common\Common;
use think\Controller;
use think\Db;
use think\Session;

class Index extends Common
{
    public function index()
    {

        return $this->fetch('index');
    }

    /**
     * 后台首页面
     * @return mixed
     */
    public function system(){

        //获取服务器信息，超全局变量，$_SERVER
        $system=[
            //服务器ip
            'ip'=>$_SERVER['SERVER_ADDR'],
            //服务器域名
            'host'=>$_SERVER['SERVER_NAME'],
            //操作系统
            'os'=>php_uname('s'),
            //运行环境//服务器标识字符串
            'server'=>$_SERVER['SERVER_SOFTWARE'],
            //服务器端口
            'port'=>$_SERVER['SERVER_PORT'],
            //php 版本
            'php_ver'=>PHP_VERSION,
            //数据库版本
            'mysql_ver'=>Db::query('select version()')[0]['version()'],
            //数据库名
            'database'=>config('database')['database']
        ];

        //获取数据库中的管理员登录日志记录

        $log_res = Db::name('loginlog')->order('logintime desc')->where('mid',session('login_id','','admin'))->limit(10)->select();
        $this->assign('log_res',$log_res);
        $this->assign('system',$system);
        return $this->fetch('system');
    }
}
