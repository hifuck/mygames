<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/9
 * Time: 17:03
 */

namespace App\Model;


use EasySwoole\Core\Swoole\ServerManager;

class Manager extends BaseModel
{
    protected $tableName = 'active_managers';

    public function add($data)
    {
        return $this->db->insert($this->tableName, $data);
    }


    public function send_message($active_id, $data = '')
    {
        $selects = ['client_id'];
        $clients = $this->db->where('active_id', $active_id)->get($this->tableName, null, $selects);
        foreach ($clients as $client) {
            $fd = $client['client_id'];
            $info = ServerManager::getInstance()->getServer()->connection_info($fd);
            if (is_array($info)) {
//                TaskManager::async(function () use ($fd, $data) {
//                    SocketResponse::response($fd, $data);
//                });
                ServerManager::getInstance()->getServer()->push($fd, $data);
            } else {
                $this->db->where('active_id', $active_id)->where('client_id', $fd)->delete($this->tableName);
            }
        }
    }
}