<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 2018/9/13
 * Time: 18:11
 */

namespace app\admin\controller;

use app\admin\common\Common;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\validate\Manager;
use think\Session;

class Login extends Controller
{
    public function index(Request $request){

        if($request->isPost()){
            //1.接收数据
            $data = $request->post();

            //2.登录验证
            $res = $this->loginCheck($data);

            //3.将登录信息记录到登录日志，如果账号不存在，不用记录，用mid标识是否记录
            if (isset($res['mid'])){
                $data_log=[
                    'mid'=>$res['mid'],
                    'ip'=>$request->ip(),
                    'logintime'=>time(),
                    'msg'=>$res['msg']
                ];
                //只保存当前管理员的最新30条记录
                $log_rows = Db::name('loginlog')->where('mid',$res['mid'])->count();
                $log_min = Db::name('loginlog')->where('mid',$res['mid'])->min('logintime');
                if ($log_rows == 30){
                    Db::name('loginlog')->where('mid',$res['mid'])->where('logintime',$log_min)->delete();
                }
                Db::name('loginlog')->insert($data_log);
            }

            //4.判断code,是否登录成功
            if ($res['code']!==1){
                $this->error($res['msg'],'login/index');
            }
            $this->redirect('index/index');

        }

        if(session('login_name','','admin') && session('login_id','','admin')){
            $this->redirect('index/index');
        }

        return $this->fetch('login');
    }

    protected function loginCheck($data){
        //1.后端验证，用户名，密码是否符合规则，验证码是否正确
        $validate = new Manager();
        if (!$validate->scene('login')->check($data)) {
            //$this->error($validate->getError(),'login/index');
            return ['code'=>0,'msg'=>$validate->getError()];
        }

        //2.验证用户名与数据库中是否一致
        $res = Db::name('manager')->where('account',$data['account'])->find();
        if (!$res){
            //$this->error('账号不存在','login/index');
            return ['code'=>0,'msg'=>'账号不存在'];
        }

        //3.验证密码与数据库中是否一致
        $data['password'] = md5($data['password']);
        if ($data['password'] !== $res['password']){
//            $this->error('密码输入错误','login/index');
            return ['code'=>0,'msg'=>'密码输入错误','mid'=>$res['id']];
        }

        //4.验证用户名状态
        if ($res['state'] !== 1){
//            $this -> error('账号已锁定，不允许登录','login/index');
            return ['code'=>0,'msg'=>'账号已锁定，不允许登录','mid'=>$res['id']];
        }

        //5.将登录信息写入session,登录成功
        Session::set('login_name',$res["account"],'admin');
        Session::set('login_id',$res["id"],'admin');
//        $this->redirect('index/index');
        return ['code'=>1,'mid'=>$res['id'],'msg'=>'登录成功'];

    }

    public function logout(){
        session(null,'admin');
        $this->success('正在退出...','login/index');
    }
}