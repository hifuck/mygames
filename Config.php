<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/12/30
 * Time: 下午10:59
 */

return [
    'MAIN_SERVER'=>[
        'HOST'=>'0.0.0.0',
        'PORT'=>9501,
//        'SERVER_TYPE'=>\EasySwoole\Core\Swoole\ServerManager::TYPE_WEB_SERVER,
        'SERVER_TYPE'=>\EasySwoole\Core\Swoole\ServerManager::TYPE_WEB_SOCKET_SERVER,
        'SOCK_TYPE'=>SWOOLE_TCP,//该配置项当为SERVER_TYPE值为TYPE_SERVER时有效
        'RUN_MODEL'=>SWOOLE_PROCESS,
        'SETTING'=>[
            'task_worker_num' => 16, //异步任务进程
            'task_max_request'=>20,
            'max_request'=>5000,//强烈建议设置此配置项
            'worker_num'=>16,
            //支持静态文件
            'document_root'         => EASYSWOOLE_ROOT.'/Public',  // 静态资源目录
            'enable_static_handler' => true,
        ],
    ],
    'DEBUG'=>true,
    'TEMP_DIR'=>EASYSWOOLE_ROOT.'/Temp',
    'LOG_DIR'=>EASYSWOOLE_ROOT.'/Log',
    'EASY_CACHE'=>[
        'PROCESS_NUM'=>1,//若不希望开启，则设置为0
        'PERSISTENT_TIME'=>0//如果需要定时数据落地，请设置对应的时间周期，单位为秒
    ],
    'CLUSTER'=>[
        'enable'=>false,
        'token'=>null,
        'broadcastAddress'=>['255.255.255.255:9556'],
        'listenAddress'=>'0.0.0.0',
        'listenPort'=>9556,
        'broadcastTTL'=>5,
        'serviceTTL'=>10,
        'serverName'=>'easySwoole',
        'serverId'=>null
    ],
    'database' => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'port'      => '8306',
        'database'  => 'test',
        'username'  => 'root',
        'password'  => 'fengrong666',
        'charset'   => 'utf8mb4',
    ]
];