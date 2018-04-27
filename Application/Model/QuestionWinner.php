<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/9
 * Time: 17:12
 */

namespace App\Model;


class QuestionWinner extends BaseModel
{
    protected $tableName = 'question_winners';

    public function add($winner)
    {
        if (!$this->findWinner($winner)) {
            return $this->db->insert($this->tableName, $winner);
        }
        return false;
    }

    public function findWinner($winner)
    {
        $active_id = $winner['active_id'];
        $round_number = $winner['round_number'];
        $user_id = $winner['user_id'];
        return $this->db->where('active_id', $active_id)->where('round_number', $round_number)->where('user_id', $user_id)->getOne($this->tableName);
    }
}