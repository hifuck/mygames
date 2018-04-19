<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/19
 * Time: 16:29
 */

namespace App\HttpController\Services;


use App\HttpController\Socket\SocketResponse;
use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Core\Swoole\Task\TaskManager;

class ScreenManagerService
{
    static $key = 'screen_managers_';

    public static function sendDataBags($active_id, $data = [], $fd = null)
    {
        TaskManager::async(function () use ($active_id, $data, $fd) {
            if ($fd) {
                SocketResponse::response($fd, $data);
            } else {
                $fd = ScreenManagerService::getManager($active_id);
                if (check_fd($fd)) {
                    SocketResponse::response($fd, $data);
                }
            }
        });
    }

    public static function adddManager($active_id, $fd)
    {
        Cache::getInstance()->set(ScreenManagerService::$key . $active_id, $fd);
    }

    public static function getManager($active_id)
    {
        $manager = Cache::getInstance()->get(ScreenManagerService::$key . $active_id);
        return $manager;
    }

}