<?php
/**
 * Created by PhpStorm.
 * User: colin
 * Date: 2018/9/18
 * Time: 21:07
 */

namespace app\admin\controller;


use app\admin\common\Common;
use think\Request;
use app\admin\model\Category as CategoryModel;

class Category extends Common
{
    //栏目列表
    public function index(){

        $res = CategoryModel::order('sort desc,id asc')->select();

        CategoryModel::getCate($res,0,-1,$cate);
        $count = CategoryModel::count();
        //$cateObj = CategoryModel::array_to_object($cate);

        $this->assign('cate',$cate);
        $this->assign('count',$count);
//        $this->assign('page',$page);
        //记得return,如果前端页面没显示，也没报错，可能是没有return
        return $this->fetch('index');
    }

    /**
     * 栏目添加
     */
    public function add(Request $request){

        if ($request->isPost()){
            $data = $request->post();

            $result = CategoryModel::create($data);
            if ($result){
                $this->success('栏目添加成功');
            }
        }
        $res = CategoryModel::all();
        //$list = CategoryModel::getCateall($res,0);
        //引用方式，没有返回值
        CategoryModel::getCate($res,0,-1,$list);

        $this->assign('cate',$list);
        return $this->fetch('add');
    }

    //栏目排序
    public function sort(Request $request){
        if ($request->isPost()){
            $data = $request->post();
            $res = CategoryModel::sort($data);
            if ($res){
                $this->success('更新成功');
            }
            $this->error('更新异常');
        }
    }

    //栏目更新
    public function edit(Request $request,$id){

        if ($request->isPost()){
            $data = $request->post();
            $cateids = CategoryModel::getChildids($id);
            $cateids[] = $id;
            if (in_array($data['pid'],$cateids)){
                $this->error('上级栏目选择错误');
            }
            $result = CategoryModel::where('id',$id)->update($data);
            if($result){
                $this->success('修改成功','category/index');
            }
            $this->error('更新失败');

        }
        $res = CategoryModel::all();
        $catename = CategoryModel::get($id);
        CategoryModel::getCate($res,0,-1,$list);

        $this->assign([
            'cate'=>$list,
            'catename'=>$catename,
        ]);
        return $this->fetch('edit');
    }

    //栏目删除
    public function del($id){
        $res = CategoryModel::where('pid',$id)->select();
        if ($res){
            $this->error('当前栏目有子栏目，不能删除');
        }
        if (CategoryModel::destroy($id)){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    //上传栏目图片
    public function uploadimg(){
        return json(['msg'=>'上传成功']);
    }
}