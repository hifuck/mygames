<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/4
 * Time: 12:11
 */

namespace App\Model;


class Activity extends BaseModel
{
    protected $tableName = 'activities';


    public function find($id)
    {
        return $this->db->where('id', $id)->getOne($this->tableName);
    }

    public function change_question_index($id, $number, $round)
    {
        $data = [
            'question_index' => $number,
            'question_round' => $round
        ];
        return $this->db->where('id', $id)->update($this->tableName, $data);
    }
}