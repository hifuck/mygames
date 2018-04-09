<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/4/9
 * Time: 21:32
 */

namespace App\HttpController\Socket;

use EasySwoole\Core\Socket\Response;
use EasySwoole\Core\Swoole\ServerManager;

class SocketResponse extends Response
{
    static function response($fd, $data, int $opCode = 1, bool $finish = true)
    {
        $server = ServerManager::getInstance()->getServer();
        return $server->push($fd, $data, $opCode, $finish);
    }
}