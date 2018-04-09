<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 9:44
 */

namespace App\HttpController;


use App\Model\User;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        $openid = str_random(16);
        $user = new User();
        if (!$user->has_join(1, $openid)) {
            $data['username'] = str_random(6);
            $data['openid'] = $openid;
            $data['active_id'] =1;
            $user_id = $user->add($data);
        }
        $content = file_get_contents(__DIR__ . '/user.html');
        $this->response()->write($content);
    }

    function back()
    {
        $content = file_get_contents(__DIR__ . '/manager.html');
        $this->response()->write($content);
    }
}