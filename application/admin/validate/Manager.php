<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 2018/9/10
 * Time: 22:05
 */

namespace app\admin\validate;


use think\Validate;

class Manager extends Validate
{
    protected $rule = [
        'account' => 'require|min:5|max:25|unique:manager',
        'email' => 'email',
        'password' => 'require|min:6|max:12|confirm:repassword',
        'code' => 'require|captcha',
    ];

    protected $message = [
        'account.require' => '账号不能为空',
        'account.unique' => '账号已存在',
        'account.min' => '账号不能少于5个字符',
        'account.max' => '账号不能超过25个字符',
        'email' => '邮箱格式错误',
        'password.require' => '密码不能为空',
        'password.min' => '密码不能少于6位',
        'password.max' => '密码不能多余12位',
        'password.confirm' => '两次密码不一致',
        'code.require' => '验证码不能为空',
        'code.captcha' => '验证码不正确',
    ];

    //场景验证
    protected $scene = [
        'edit' => ['email','password'],
        'email' => ['email'],
        'password' => ['password'],
        'login' => [
            'account'=>'require|min:5|max:25',
            'password'=>'require|min:6|max:12',
            'code'=>'require|captcha'
        ],
        'add' => ['account','email','password'],
    ];

}