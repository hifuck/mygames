<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/9
 * Time: 下午1:04
 */

namespace EasySwoole;

use App\HttpController\Socket\Socket;
use EasySwoole\Core\AbstractInterface\EventInterface;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Swoole\EventHelper;
use EasySwoole\Core\Swoole\EventRegister;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Utility\File;

Class EasySwooleEvent implements EventInterface
{

    public function frameInitialize(): void
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');
//        $this->loadConf(EASYSWOOLE_ROOT . '/Conf');
    }

    public function mainServerCreate(ServerManager $server, EventRegister $register): void
    {
        EventHelper::registerDefaultOnMessage($register, new Socket());

        Di::getInstance()->set('MYSQL', \MysqliDb::class, Array(
                'host'     => '127.0.0.1',
                'username' => 'my_swoole',
                'password' => 'e6W8dNWYZwGMnB5n',
                'db'       => 'my_swoole',
                'port'     => 3306,
                'charset'  => 'utf8')
        );
        // TODO: Implement mainServerCreate() method.
    }

    public function onRequest(Request $request, Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public function afterAction(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    function loadConf($ConfPath)
    {
        $Conf = Config::getInstance();
        $files = File::scanDir($ConfPath);
        if (is_array($files)) {
            foreach ($files as $file) {
                $data = require_once $file;
                $Conf->setConf(strtolower(basename($file, '.php')), (array)$data);
            }
        }
    }
}