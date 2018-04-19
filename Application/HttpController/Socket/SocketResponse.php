<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/4/9
 * Time: 21:32
 */

namespace App\HttpController\Socket;

use EasySwoole\Core\Swoole\ServerManager;

class SocketResponse
{
    static function response($fd, $data = [], $msg = '', $code = 200, int $opCode = 1, bool $finish = true)
    {
        $res['code'] = $code;
        $res['msg'] = $msg;
        $res['data'] = $data;
        $data = json_encode($data, 256);
        $server = ServerManager::getInstance()->getServer();
        return $server->push($fd, $data, $opCode, $finish);
    }
}