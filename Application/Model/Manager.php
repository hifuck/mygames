<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/9
 * Time: 17:03
 */

namespace App\Model;


class Manager extends BaseModel
{
    protected $tableName = 'active_managers';

    public function add($data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function get_managers_clients($active_id)
    {
        $selects=['client_id'];
        return $this->db->where('active_id',$active_id)->get($this->tableName,null,$selects);
    }
}