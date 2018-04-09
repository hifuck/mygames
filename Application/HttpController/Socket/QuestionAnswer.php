<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 14:12
 */

namespace App\HttpController\Socket;


use App\Model\Manager;
use App\Model\User;
use App\Model\UserClient;
use EasySwoole\Core\Socket\WebSocketController;
use EasySwoole\Core\Swoole\Task\TaskManager;

class QuestionAnswer extends WebSocketController
{
    #用户端 用户接入
    function user_login()
    {
        $request = $this->request()->getArg('content');
        $openid = str_random(16);
        $active_id = $request['active_id'];
        $user = new User();
        if (!$user->has_join($active_id, $openid)) {
            $data['username'] = str_random(6);
            $data['openid'] = $openid;
            $data['active_id'] = $request['active_id'];
            $user_id = $user->add($data);
        } else {
            $user_info = $user->find($openid, $active_id);
            $user_id = $user_info['id'];
        }
        $user_client = new UserClient();
        if (!$user_client->is_online($request['active_id'], $openid)) {
            $client_log['user_id'] = $user_id;
            $client_log['client_id'] = $this->client()->getFd();
            $client_log['active_id'] = $request['active_id'];
            $user_client->add($client_log);
            //随后去掉
            $user_fd= $this->client()->getFd();
            TaskManager::async(function () use ($user_fd, $openid) {
                SocketResponse::response($user_fd, $openid);
            });
        }
        $count = $user_client->online_num($request['active_id']);
        $manager = new Manager();
        $clients = $manager->get_managers_clients($request['active_id']);
        if (count($clients) > 0) {
            foreach ($clients as $client) {
                TaskManager::async(function () use ($client, $count) {
                    SocketResponse::response($client, $count);
                });
            }
        }
        $this->response()->write('欢迎登录');
    }

    #屏幕端 屏幕介入
    function admin_login()
    {
        $request = $this->request()->getArg('content');
        $active_id = $request['active_id'];
        $user_client = new UserClient();
        $data['num'] = $user_client->online_num($active_id);
        $data['msg'] = '连接成功';
        $data['code'] = 200;
        $this->response()->write(json_encode($data, 256));
    }


    function actionNotFound(?string $actionName)
    {
        $this->response()->write("action call {$actionName} not found");
    }
}