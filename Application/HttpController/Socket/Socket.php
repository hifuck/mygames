<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/3/30
 * Time: 14:55
 */

namespace App\HttpController\Socket;


use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;

class Socket implements ParserInterface
{
    public function decode($raw, $client)
    {
        $command = new CommandBean();
        $json = json_decode($raw, 1);
        $command->setControllerClass(QuestionAnswer::class);
        $command->setAction($json['action']);
        $command->setArg('content', $json['content']);
        return $command;
    }

    public function encode(string $raw, $client, $commandBean): ?string
    {
        return $raw;
    }
}