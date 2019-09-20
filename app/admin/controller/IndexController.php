<?php

namespace app\admin\controller;

use think\facade\Db;

/**
 * 首页
 * Class IndexController
 * @package app\admin\controller
 */
class IndexController extends BaseController
{
    public function index()
    {
        $sys_info = cache('sys_cache_server_info');
        if (!$sys_info) {
            $sys_info = $this->getServerInfo();
            cache('sys_cache_server_info', $sys_info, 100);
        }
        return view('index', [
            'sys_info' => $sys_info,
        ]);
    }

    /**
     * 获取系统信息
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\PDOException
     */
    protected function getServerInfo()
    {
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO'; //zlib
        $sys_info['safe_mode']      = (boolean)ini_get('safe_mode') ? 'YES' : 'NO'; //safe_mode = Off
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : 'NO';
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']           = phpversion();
        $sys_info['ip']             = GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
        $sys_info['max_ex_time']    = @ini_get("max_execution_time") . 's'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain']         = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit']   = ini_get('memory_limit');

        $musql_version             = Db::query("select version() as ver");
        $sys_info['mysql_version'] = $musql_version[0]['ver'];
        if (function_exists("gd_info")) {
            $gd                 = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = "未知";
        }

        return $sys_info;
    }

    //更新侧边栏状态
    public function flexible()
    {
        $menu = $this->request->get('menu', 'open', 'trim');
        session('menu_status', $menu);
    }
}
