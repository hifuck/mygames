<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/4
 * Time: 12:11
 */

namespace App\Model;


class User extends BaseModel
{
    protected $tableName = 'users';

    public function add($data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function has_join($active_id, $openid)
    {
        $count = $this->db->where('active_id', $active_id)->where('openid', $openid)->getValue($this->tableName, "count(*)");
        if ($count > 0)
            return true;
        return false;
    }

    public function find($openid, $active_id)
    {
        return $this->db->where('active_id', $active_id)->where('openid', $openid)->getOne($this->tableName);
    }
}