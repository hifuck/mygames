<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 9:44
 */

namespace App\HttpController;


use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        $key = 'ttt_ppp';
        $res = Cache::getInstance()->get($key);
        if ($res) {
            Cache::getInstance()->set($key, '测试');
        }
        $this->response()->write($res?$res:'');
    }

    function back()
    {
        $content = file_get_contents(__DIR__ . '/manager.html');
        $this->response()->write($content);
    }

}