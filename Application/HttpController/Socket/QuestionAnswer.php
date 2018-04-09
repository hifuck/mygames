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
        }
        $count = $user_client->online_num($request['active_id']);
        $manager = new Manager();
        $manager->send_message($request['active_id'],$count);
    }

    #屏幕端 屏幕介入
    function admin_login()
    {
        $request = $this->request()->getArg('content');
        $active_id = $request['active_id'];
        $user_client = new UserClient();
        $count = $user_client->online_num($active_id);
        $manager = new Manager();
        $manager_info['active_id'] = $active_id;
        $manager_info['client_id'] = $this->client()->getFd();
        $manager->add($manager_info);
        $fd = $this->client()->getFd();
        SocketResponse::response($fd, $count);
//        TaskManager::async(function () use ($fd, $count) {
//            SocketResponse::response($fd, $count);
//        });
    }

    #发送问题
    function send_question()
    {
        $request = $this->request()->getArg('content');
        $active_id = $request['active_id'];
        $q_id = $request['q_id'];

        $data['id']=$q_id;
        $data['question']='1+1=?';
        $data['answer']='2';
        $data['time']=10;
        $data=json_encode($data,256);

        $user_client=new UserClient();
        $user_client->send_message($active_id,$data);
    }


    function actionNotFound(?string $actionName)
    {
        $this->response()->write("action call {$actionName} not found");
    }
}