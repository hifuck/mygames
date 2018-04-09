<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 9:44
 */

namespace App\HttpController;


use App\Model\Manager;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        $manager = new Manager();
        $clients = $manager->get_managers_clients(1);
        $content = file_get_contents(__DIR__ . '/user.html');
        $this->response()->write($content);
    }

    function back()
    {
        $content = file_get_contents(__DIR__ . '/manager.html');
        $this->response()->write($content);
    }
}