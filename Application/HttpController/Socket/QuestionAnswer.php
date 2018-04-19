<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 14:12
 */

namespace App\HttpController\Socket;


use App\HttpController\Services\ScreenManagerService;
use App\HttpController\Services\UserService;
use App\Model\Activity;
use App\Model\Questions;
use EasySwoole\Core\Socket\WebSocketController;

class QuestionAnswer extends WebSocketController
{
    protected $userids = 'user_ids_';
    protected $manager_fds = 'manager_fds_';
    protected $question_round = 'question_round_';

    #用户端 用户接入
    function user_login()
    {
        $request = $this->request()->getArg('content');
        $user_id = $request['user_id'];
        $active_id = $request['active_id'];
        UserService::addUser($active_id, $user_id, $this->client()->getFd());
        $data['type'] = 1;
        $data['count'] = UserService::getUserCount($active_id);
        ScreenManagerService::sendDataBags($active_id, $data);
    }

    function user_logout()
    {
        $request = $this->request()->getArg('content');
        $user_id = $request['user_id'];
        $active_id = $request['active_id'];
        UserService::removeUser($active_id, $user_id);
        $data['type'] = 1;
        $data['count'] = UserService::getUserCount($active_id);
        ScreenManagerService::sendDataBags($active_id, $data);
    }

    //用户答题
    function user_answer()
    {
        $request = $this->request()->getArg('content');
        $id = $request['question_id'];
        $active_id = $request['active_id'];
        $questionObj = new Questions();
        $question = $questionObj->findById($id);
        //回答错误
        if ($question['answer'] != $request['answer']) {
            UserService::removeUser($active_id, $request['user_id']);
            $data['type'] = 1;
            $data['count'] = UserService::getUserCount($active_id);
            ScreenManagerService::sendDataBags($active_id, $data);
            $user_data['type'] = 4;
            UserService::sendDataBags($active_id, $user_data, $this->client()->getFd());
        } else {
            $data['type'] = 3;
            UserService::sendDataBags($active_id, $data, $this->client()->getFd());
        }
    }

    #屏幕端 屏幕介入
    function admin_login()
    {
        $request = $this->request()->getArg('content');
        $active_id = $request['active_id'];
        $fd = $this->client()->getFd();
        $data['type'] = 1;
        $data['count'] = UserService::getUserCount($active_id);
        ScreenManagerService::adddManager($active_id, $fd);
        ScreenManagerService::sendDataBags($active_id, $data, $fd);
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
        $data['id'] = $question['id'];
        $data['title'] = $question['title'];
        $data['options'] = unserialize($question['options']);
        $data['display_order'] = $question['display_order'];
        //给用户发送题目
        UserService::sendDataBags($active_id, $data);
        $data['answer'] = $question['answer'];
        //返回屏幕题目和答案
        ScreenManagerService::sendDataBags($active_id, $data);
        $active = new Activity();
        $active->change_question_index($active_id, $display_order, $round_num);
    }


    function actionNotFound(?string $actionName)
    {
        $this->response()->write("action call {$actionName} not found");
    }
}