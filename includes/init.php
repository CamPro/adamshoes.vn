<?php

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

error_reporting(E_ALL);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 取得当前ecshop所在的根目录 */
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));

if (!file_exists(ROOT_PATH . 'data/install.lock') && !file_exists(ROOT_PATH . 'includes/install.lock')
    && !defined('NO_CHECK_INSTALL'))
{
    header("Location: ./install/index.php\n");

    exit;
}

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path', '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path', '.:' . ROOT_PATH);
}

require(ROOT_PATH . 'data/config.php');

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_insert.php');
require(ROOT_PATH . 'includes/lib_goods.php');
require(ROOT_PATH . 'includes/lib_article.php');

/* 对用户传入的变量进行转义操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());

/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 创建错误处理对象 */
$perr = isset($_COOKIE['client']) ? 'message_mobile.dwt' : 'message.dwt';
$err = new ecs_error($perr);

/* 载入系统参数 */
$_CFG = load_config();

/* 载入语言文件 */
require(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/common.php');
include(ROOT_PATH . 'themes/' . $_CFG['template'].'/lang/' . $_CFG['lang'] . '/common.php');

if ($_CFG['shop_closed'] == 1)
{
    /* 商店关闭了，输出关闭的消息 */
    header('Content-type: text/html; charset='.EC_CHARSET);

    die('<div style="margin: 150px; text-align: center; font-size: 14px"><p>' . $_LANG['shop_closed'] . '</p><p>' . $_CFG['close_comment'] . '</p></div>');
}

if (is_spider())
{
    /* 如果是蜘蛛的访问，那么默认为访客方式，并且不记录到日志中 */
    // if (!defined('INIT_NO_USERS'))
    // {
    //     define('INIT_NO_USERS', true);
    //      整合UC后，如果是蜘蛛访问，初始化UC需要的常量
    //     if($_CFG['integrate_code'] == 'ucenter')
    //     {
    //          $user = & init_users();
    //     }
    // }
    $_SESSION = array();
    $_SESSION['user_id']     = 0;
    $_SESSION['user_name']   = '';
    $_SESSION['email']       = '';
    $_SESSION['user_rank']   = 0;
    $_SESSION['discount']    = 1.00;
}

if (!defined('INIT_NO_USERS'))
{
    /* 初始化session */
    include(ROOT_PATH . 'includes/cls_session.php');

    $sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'));

    define('SESS_ID', $sess->get_session_id());
}

if(isset($_SERVER['PHP_SELF']))
{
    $_SERVER['PHP_SELF']=htmlspecialchars($_SERVER['PHP_SELF']);
}
if (!defined('INIT_NO_SMARTY'))
{
    header('Cache-control: private');
    header('Content-type: text/html; charset='.EC_CHARSET);

    /* 创建 Smarty 对象。*/
    require(ROOT_PATH . 'includes/cls_template.php');
    $smarty = new cls_template;

    $smarty->cache_lifetime = $_CFG['cache_time'];


    // chuyển chế độ phát triển giao diện
    if (isset($_GET['dev']) && $_GET['dev'] == 1) {
        setcookie('dev', 1 , time()+ (84000*30), '/');

    }elseif (isset($_GET['dev']) && $_GET['dev'] == 0) {
        setcookie('dev', 1 , time()- (84000*30), '/');
    }

    if(isset($_COOKIE['dev']) && $_COOKIE['dev'] == 1){

        $smarty->template_dir   = ROOT_PATH . 'themes/bkshop2016v2';
        $smarty->cache_dir      = ROOT_PATH . 'temp/caches_dev';
        $smarty->compile_dir    = ROOT_PATH . 'temp/compiled_dev';
    }else{
        $smarty->template_dir   = ROOT_PATH . 'themes/' . $_CFG['template'];
        $smarty->cache_dir      = ROOT_PATH . 'temp/caches';
        $smarty->compile_dir    = ROOT_PATH . 'temp/compiled';
    }

    // end

     // chuyển chế độ phát triển giao diện
    if (isset($_GET['tester']) && $_GET['tester'] == 1) {
        setcookie('_tester', 1 , time()+ (6*30*24*3600), '/');

    }elseif (isset($_GET['dev']) && $_GET['dev'] == 0) {
        setcookie('_tester', 1 , time()- (6*30*24*3600), '/');
    }

    if ((DEBUG_MODE & 2) == 2)
    {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    }
    else
    {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }
    $smarty->assign('mydomain', $base_path);
    $smarty->assign('lang', $_LANG);
    $smarty->assign('month',date('m'));
    $smarty->assign('ecs_charset', EC_CHARSET);
    $basename = basename($_SERVER['SCRIPT_NAME'], '.php');
    $client = isset($_GET['client']) ? $_GET['client'] : '';
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    $uachar = "/(nokia|asus|sony|ericsson|microsoft|samsung|oppo|lg|philips|panasonic|alcatel|lenovo|iphone|htc|mobile|android)/i";
    //|| preg_match($uachar, $ua)
    $requri =  $_SERVER['REQUEST_URI'];
    $pos = strpos($requri,"?");
    $uri = $pos ? $requri.'&amp;' : $requri.'?';
    $swich_mobile = htmlentities($uri);
    $smarty->assign('swich_mobile', $swich_mobile);

    if((!isset($_COOKIE['client']) && $client == 'mobile') || preg_match($uachar, $ua)){
        setcookie('client', 'mobile', time()+ (84000*30), '/');
        $display_mode = $basename.'_mobile.dwt';
    }elseif (isset($_COOKIE['client']) && $_COOKIE['client'] == 'mobile') {
        $display_mode = $basename.'_mobile.dwt';
    }else{
         $display_mode = $basename.'.dwt';
    }
    if($client == 'full'){
        setcookie('client', 'mobile', time()-(84000*30), '/');
        //ecs_header("Location: ./");
    }


    if (!empty($_CFG['stylename']))
    {
        $smarty->assign('ecs_css_path', 'themes/' . $_CFG['template'] . '/style_' . $_CFG['stylename'] . '.css');
    }
    else
    {
        $smarty->assign('ecs_css_path', 'themes/' . $_CFG['template'] . '/style.css');
    }

}

if (!defined('INIT_NO_USERS'))
{
    /* 会员信息 */
    //$user =& init_users();

    if (!isset($_SESSION['user_id']))
    {
        /* 获取投放站点的名称 */
        $site_name = isset($_GET['from'])   ? $_GET['from'] : @addslashes($_LANG['self_site']);
        $from_ad   = !empty($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

        $_SESSION['from_ad'] = $from_ad; // 用户点击的广告ID
        $_SESSION['referer'] = stripslashes($site_name); // 用户来源

        unset($site_name);

        if (!defined('INGORE_VISIT_STATS'))
        {
            visit_stats();
        }
    }

    if (empty($_SESSION['user_id']))
    {

        $_SESSION['user_id']     = 0;
        $_SESSION['user_name']   = '';
        $_SESSION['email']       = '';
        $_SESSION['user_rank']   = 0;
        $_SESSION['discount']    = 1.00;
        if (!isset($_SESSION['login_fail']))
        {
            $_SESSION['login_fail'] = 0;
        }
    }

    /* 设置推荐会员 */
    if (isset($_GET['u']))
    {
        set_affiliate();
    }

    /* session 不存在，检查cookie */
    if (!empty($_COOKIE['ECS']['user_id']) && !empty($_COOKIE['ECS']['password']))
    {
        // 找到了cookie, 验证cookie信息
        $sql = 'SELECT user_id, user_name, password ' .
                ' FROM ' .$ecs->table('users') .
                " WHERE user_id = '" . intval($_COOKIE['ECS']['user_id']) . "' AND password = '" .$_COOKIE['ECS']['password']. "'";

        $row = $db->GetRow($sql);

        if (!$row)
        {
            // 没有找到这个记录
           $time = time() - 3600;
           setcookie("ECS[user_id]",  '', $time, '/');
           setcookie("ECS[password]", '', $time, '/');
        }
        else
        {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            update_user_info();
        }
    }

    if (isset($smarty))
    {
        $smarty->assign('ecs_session', $_SESSION);
    }
}


if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
}
if ((DEBUG_MODE & 4) == 4)
{
    include(ROOT_PATH . 'includes/lib.debug.php');
}

/* 判断是否支持 Gzip 模式 */
if (!defined('INIT_NO_SMARTY') && gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}

/* Shopiy */
@include(ROOT_PATH.'themes/'.$_CFG['template'].'/functions.php');
/*start seo custom*/
//load file cau hinh cua eseo

//@include(ROOT_PATH.'efom/init.php');
/*end seo custom*/
?>