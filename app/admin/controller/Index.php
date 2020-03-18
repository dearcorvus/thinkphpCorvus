<?php
namespace app\admin\controller;

use app\BaseController;

class Index extends BaseController
{
    public function _initialize()
    {
        $adminSettings = cmf_get_option('admin_settings');
        if (empty($adminSettings['admin_password']) || $this->request->path() == $adminSettings['admin_password']) {
            $adminId = cmf_get_current_admin_id();
            if (empty($adminId)) {
                session("__LOGIN_BY_CMF_ADMIN_PW__", 1);//设置后台登录加密码
            }
        }

        parent::_initialize();
    }

    public function index()
    {
        return view();
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
