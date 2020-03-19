<?php
// 应用公共文件
use think\facade\Route;
//设置插件入口路由

Route::get('captcha/[:config]','\\think\\captcha\\CaptchaController@index');
