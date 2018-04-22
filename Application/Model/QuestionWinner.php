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

    public function add($data)
    {
        return $this->db->insert($this->tableName, $data);
    }
}