<?php
namespace app\index\controller;

use app\BaseController;

class Index extends BaseController
{
    // 签证详情
    public function index()
    {
        return view();
    }
    // 签证分类
    public function visa_type()
    {
        return view();
    }
    // 评论
    public function visa_evaluate()
    {
        return view();
    }
    // 旅游
    public function index1()
    {
        return view();
    }    
    // 添加评价
    public function add()
    {
        return view();
    }
    //问答
    public function visa_answers()
    {
        return view();
    }
    //分享
    public function visa_share()
    {
        return view();
    }
    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}


