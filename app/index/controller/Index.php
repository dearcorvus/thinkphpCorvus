<?php
namespace app\index\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return view();
    }
    public function visa_type()
    {
        return view();
    }
    public function visa_evaluate()
    {
        return view();
    }

    public function index1()
    {
        return view();
    }    


    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}


