<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/4
 * Time: 12:11
 */

namespace App\Model;


class Questions extends BaseModel
{
    protected $tableName = 'questions';


    public function find($active_id, $round_num, $display_order)
    {
        return $this->db->where('active_id', $active_id)->where('round_number', $round_num)->where('display_order', $display_order)->getOne($this->tableName);
    }
}