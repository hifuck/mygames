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

class QuestionWinner extends BaseModel
{
    protected $tableName = 'question_winners';

    public function add($data)
    {
        return $this->db->insert($this->tableName, $data);
    }
}