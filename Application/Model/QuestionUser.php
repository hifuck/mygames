<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/9
 * Time: 17:12
 */

namespace App\Model;


use App\HttpController\Socket\SocketResponse;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Task\TaskManager;

class QuestionUser extends BaseModel
{
    protected $tableName = 'question_users';

    public function add($data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function left_count($active_id, $round_num)
    {
        return $this->db->where('active_id', $active_id)->where('round_number', $round_num)->getValue($this->tableName, "count(*)");
    }

    public function is_online($active_id, $user_id)
    {
        $count = $this->db->where('active_id', $active_id)->where('round_number', 0)->where('user_id', $user_id)->getValue($this->tableName, "count(id)");
        if ($count > 0)
            return true;
        return false;
    }

    public function answer_wrong($active_id, $user_id, $round_number)
    {
        $this->db->where('active_id', $active_id)->where('round_number', $round_number)->where('user_id', $user_id)->update($this->tableName, ['status' => 0]);
    }


    public function send_message($active_id, $round_num, $data = '')
    {
        $selects = ['client_id'];
        $clients = $this->db->where('active_id', $active_id)->where('status', 1)->where('round_number', $round_num)->get($this->tableName, null, $selects);
        foreach ($clients as $client) {
            $fd = $client['client_id'];
            $info = ServerManager::getInstance()->getServer()->connection_info($fd);
            if (is_array($info)) {
                TaskManager::async(function () use ($fd, $data) {
                    SocketResponse::response($fd, $data);
                });
            } else {
                $this->db->where('active_id', $active_id)->where('client_id', $fd)->update($this->tableName, ['status' => 0]);
            }
        }
    }


}