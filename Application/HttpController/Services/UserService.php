<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/19
 * Time: 16:30
 */

namespace App\HttpController\Services;


use App\HttpController\Socket\SocketResponse;
use App\Model\Activity;
use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Core\Swoole\Task\TaskManager;

class UserService
{
    //用户接入
    public static function addUser($active_id, $user_id, $fd)
    {
        $user_key = UserService::get_user_key($active_id, $user_id);
        $list_key = UserService::get_list_key($active_id);
        $user_info = Cache::getInstance()->get($user_key);
        //未接入
        if (!$user_info) {
            $activeObj = new Activity();
            $active = $activeObj->find($active_id);
            if ($active['question_index'] != 1) {
                //游戏已经开始
                $data['type'] = 88;
                UserService::sendDataBags($active_id, $data, $fd);
            } else {
                //用户 fd 对照
                $user_client['user_id'] = $user_id;
                $user_client['active_id'] = $active_id;
                $user_client['fd'] = $fd;
                Cache::getInstance()->set($user_key, $user_client);
                //用户列表
                $user_list = Cache::getInstance()->get($list_key);
                if (!$user_list || count($user_list) == 0) {
                    $user_list = [];
                }
                $user_list[] = $user_key;
                Cache::getInstance()->set($list_key, $user_list);
            }
        } else {
            //刷新fd
            $user_client['user_id'] = $user_id;
            $user_client['active_id'] = $active_id;
            $user_client['fd'] = $fd;
            Cache::getInstance()->set($user_key, $user_client);
        }
    }

    //用户淘汰/失去连接
    public static function removeUser($active_id, $user_id, $user_key = null)
    {
        if (!$user_key) {
            $user_key = UserService::get_user_key($active_id, $user_id);
        }
        $list_key = UserService::get_list_key($active_id);

        Cache::getInstance()->del($user_key);

        $user_list = Cache::getInstance()->get($list_key);
        $user_list = is_array($user_list) ? $user_list : [];
        foreach ($user_list as $index => $user) {
            if ($user_key == $user) {
                unset($user_list[$index]);
                break;
            }
        }
        Cache::getInstance()->set($list_key, $user_list);
    }

    //发送消息
    public static function sendDataBags($active_id, $data = [], $fd = null)
    {
        if ($fd) {
            TaskManager::async(function () use ($active_id, $data, $fd) {
                if (check_fd($fd)) {
                    SocketResponse::response($fd, $data);
                }
            });
        } else {
            $user_key_list = UserService::getUserList($active_id);
            foreach ($user_key_list as $index => $user_key) {
                $user = Cache::getInstance()->get($user_key);
                $fd = $user['fd'];
                if (check_fd($fd)) {
                    SocketResponse::response($fd, $data);
                } else {
                    UserService::removeUser($active_id, null, $user_key);
                }
            }
        }
    }

    public static function getUserCount($active_id)
    {
        $list_key = UserService::get_list_key($active_id);
        $user_list = Cache::getInstance()->get($list_key);
        return count($user_list);
    }

    public static function getUserList($active_id)
    {
        $list_key = UserService::get_list_key($active_id);
        $user_list = Cache::getInstance()->get($list_key);
        return is_array($user_list) ? $user_list : [];
    }


    static function get_user_key($active_id, $user_id)
    {
        return 'active_' . $active_id . '_user_' . $user_id;
    }


    static function get_list_key($active_id)
    {
        return 'user_fd_' . $active_id;
    }
}