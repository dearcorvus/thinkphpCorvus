<?php
// 应用公共文件
use think\facade\Env;
use think\facade\Db;
use think\facade\Url;
use think\facade\Route;
use think\facade\Loader;
use think\facade\Request;

//设置插件入口路由

Route::get('captcha/new', "\\app\\common\\controller\\CaptchaController@index");



/**
 * 获取当前登录的管理员ID
 * @return int
 */
function cmf_get_current_admin_id()
{
    return session('ADMIN_ID');
}

/**
 * 判断前台用户是否登录
 * @return boolean
 */
function cmf_is_user_login()
{
    $sessionUser = session('user');
    return !empty($sessionUser);
}

/**
 * 检查权限
 * @param $userId  int        要检查权限的用户 ID
 * @param $name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
 * @param $relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
 * @return boolean            通过验证返回true;失败返回false
 */
function cmf_auth_check($userId, $name = null, $relation = 'or')
{
    if (empty($userId)) {
        return false;
    }

    if ($userId == 1) {
        return true;
    }

    $authObj = new \app\common\lib\Auth();
    if (empty($name)) {
        $request    = request();
        $module     = $request->module();
        $controller = $request->controller();
        $action     = $request->action();
        $name       = strtolower($module . "/" . $controller . "/" . $action);
    }
    return $authObj->check($userId, $name, $relation);
}

/**
 * 获取网站根目录
 * @return string 网站根目录
 */
function cmf_get_root()
{
    $request = Request::instance();
    $root    = $request->root();
    $root    = str_replace('/index.php', '', $root);
    if (defined('APP_NAMESPACE') && APP_NAMESPACE == 'api') {
        $root = preg_replace('/\/api$/', '', $root);
        $root = rtrim($root, '/');
    }

    return $root;
}

/**
 * 获取系统配置，通用
 * @param string $key 配置键值,都小写
 * @return array
 */
function cmf_get_option($key)
{
    if (!is_string($key) || empty($key)) {
        return [];
    }

    static $cmfGetOption;

    if (empty($cmfGetOption)) {
        $cmfGetOption = [];
    } else {
        if (!empty($cmfGetOption[$key])) {
            return $cmfGetOption[$key];
        }
    }

    $optionValue = cache('cmf_options_' . $key);

    if (empty($optionValue)) {
        $optionValue = Db::name('option')->where('option_name', $key)->value('option_value');
        if (!empty($optionValue)) {
            $optionValue = json_decode($optionValue, true);

            cache('cmf_options_' . $key, $optionValue);
        }
    }

    $cmfGetOption[$key] = $optionValue;

    return $optionValue;
}

/**
 * 验证码检查，验证完后销毁验证码
 * @param string $value
 * @param string $id
 * @param bool $reset
 * @return bool
 */
function cmf_captcha_check($value, $id = "", $reset = true)
{
    $captcha        = new \think\captcha\Captcha();
    $captcha->reset = $reset;
    return $captcha->check($value, $id);
}

/**
 * CMF密码加密方法
 * @param string $pw 要加密的原始密码
 * @param string $authCode 加密字符串
 * @return string
 */
function cmf_password($pw, $authCode = '')
{
    if (empty($authCode)) {
        $authCode = Env::get('database.authcode');
    }
    $result = "###" . md5(md5($authCode . $pw));
    return $result;
}

/**
 * CMF密码加密方法 (X2.0.0以前的方法)
 * @param string $pw 要加密的原始密码
 * @return string
 */
function cmf_password_old($pw)
{
    $decor = md5(Env::get('database.prefix'));
    $mi    = md5($pw);
    return substr($decor, 0, 12) . $mi . substr($decor, -4, 4);
}

/**
 * CMF密码比较方法,所有涉及密码比较的地方都用这个方法
 * @param string $password 要比较的密码
 * @param string $passwordInDb 数据库保存的已经加密过的密码
 * @return boolean 密码相同，返回true
 */
function cmf_compare_password($password, $passwordInDb)
{
    if (strpos($passwordInDb, "###") === 0) {
        return cmf_password($password) == $passwordInDb;
    } else {
        return cmf_password_old($password) == $passwordInDb;
    }
}


/**
 * 生成用户 token
 * @param $userId
 * @param $deviceType
 * @return string 用户 token
 */
function cmf_generate_user_token($userId, $deviceType)
{
    $userTokenQuery = Db::name("user_token")
        ->where('user_id', $userId)
        ->where('device_type', $deviceType);
    $findUserToken  = $userTokenQuery->find();
    $currentTime    = time();
    $expireTime     = $currentTime + 24 * 3600 * 180;
    $token          = md5(uniqid()) . md5(uniqid());
    if (empty($findUserToken)) {
        Db::name("user_token")->insert([
            'token'       => $token,
            'user_id'     => $userId,
            'expire_time' => $expireTime,
            'create_time' => $currentTime,
            'device_type' => $deviceType
        ]);
    } else {
        if ($findUserToken['expire_time'] <= time()) {
            Db::name("user_token")
                ->where('user_id', $userId)
                ->where('device_type', $deviceType)
                ->update([
                    'token'       => $token,
                    'expire_time' => $expireTime,
                    'create_time' => $currentTime
                ]);
        } else {
            $token = $findUserToken['token'];
        }

    }

    return $token;
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return string
 */
function get_client_ip($type = 0, $adv = false)
{
    return request()->ip($type, $adv);
}
/**
 * 获取后台风格名称
 * @return string
 */
function cmf_get_admin_style()
{
    $adminSettings = cmf_get_option('admin_settings');
    return empty($adminSettings['admin_style']) ? 'flatadmin' : $adminSettings['admin_style'];
}
