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
                $managers = ScreenManagerService::getManagers($active_id);
                foreach ($managers as $index => $fd) {
                    if (check_fd($fd)) {
                        SocketResponse::response($fd, $data);
                    } else {
                        unset($managers[$index]);
                    }
                }
                ScreenManagerService::updateManagers($active_id, $managers);
            }
        });
    }

    public static function adddManager($active_id, $fd)
    {
        $manager_fds = Cache::getInstance()->get(ScreenManagerService::$key . $active_id);
        if (!$manager_fds)
            $manager_fds = [];
        $manager_fds[] = $fd;
        Cache::getInstance()->set(ScreenManagerService::$key . $active_id, $manager_fds);
    }

    public static function getManagers($active_id)
    {
        $managers=Cache::getInstance()->get(ScreenManagerService::$key . $active_id);
        return is_array($managers)?$managers:[];
    }

    public static function updateManagers($active_id, $managers)
    {
        Cache::getInstance()->set(ScreenManagerService::$key . $active_id, $managers);
    }
}