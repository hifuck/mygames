<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 14:12
 */

namespace App\HttpController\Socket;


use App\Model\Activity;
use App\Model\Manager;
use App\Model\Questions;
use App\Model\QuestionUser;
use EasySwoole\Core\Socket\WebSocketController;
use EasySwoole\Core\Swoole\Task\TaskManager;

class QuestionAnswer extends WebSocketController
{
    #用户端 用户接入
    function user_login()
    {
        $request = $this->request()->getArg('content');
        $user_id = $request['user_id'];
        $active_id = $request['active_id'];
        $round_num = $request['round_num'];
        $user_client = new QuestionUser();
        if (!$user_client->is_online($active_id, $user_id, $round_num)) {
            $client_log['user_id'] = $user_id;
            $client_log['active_id'] = $active_id;
            $client_log['round_number'] = $round_num;
            $client_log['status'] = 1;
            $client_log['client_id'] = $this->client()->getFd();
            $user_client->add($client_log);
        }
        $data['type'] = 1;
        $data['count'] = $user_client->online_num($active_id, $round_num);
        $data = json_encode($data, 256);
        $manager = new Manager();
        $manager->send_message($request['active_id'], $data);
    }

    #屏幕端 屏幕介入
    function admin_login()
    {
        $request = $this->request()->getArg('content');
        $active_id = $request['active_id'];
        $round_num = $request['round_num'];
        $user_client = new QuestionUser();
        $count = $user_client->online_num($active_id, $round_num);
        $manager = new Manager();
        $manager_info['active_id'] = $active_id;
        $manager_info['client_id'] = $this->client()->getFd();
        $manager->add($manager_info);
        $fd = $this->client()->getFd();
        $data['type'] = 1;
        $data['count'] = $count;
        $data = json_encode($data, 256);
        TaskManager::async(function () use ($fd, $data) {
            SocketResponse::response($fd, $data);
        });
    }

    #发送问题
    function send_question()
    {
        $request = $this->request()->getArg('content');
        $active_id = $request['active_id'];
        $round_num = $request['round_num'];
        $display_order = $request['num'];
        $que = new Questions();
        $question = $que->find($active_id, $round_num, $display_order);
        $data['type'] = 2;
        $data['options'] = unserialize($question['options']);
        $data['display_order'] = $question['display_order'];
        $data['title'] = $question['title'];
        $data['answer'] = $question['answer'];
        $data['id'] = $question['id'];
        $data = json_encode($data, 256);
        $active = new Activity();
        $active->change_question_index($active_id, $display_order, $round_num);
        //给用户发送题目
        $user_client = new QuestionUser();
        $user_client->send_message($active_id, $round_num, $data);
        //返回屏幕题目
        $fd = $this->client()->getFd();
        TaskManager::async(function () use ($fd, $data) {
            SocketResponse::response($fd, $data);
        });
    }


    function actionNotFound(?string $actionName)
    {
        $this->response()->write("action call {$actionName} not found");
    }
}