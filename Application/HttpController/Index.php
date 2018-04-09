<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 9:44
 */

namespace App\HttpController;


use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        $content = file_get_contents(__DIR__ . '/user.html');
        $this->response()->write($content);
    }

    function back()
    {
        $content = file_get_contents(__DIR__ . '/manager.html');
        $this->response()->write($content);
    }

}