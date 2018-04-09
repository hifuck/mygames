<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/9
 * Time: 17:12
 */

namespace App\Model;


class UserClient extends BaseModel
{
    protected $tableName = 'user_clients';

    public function add($data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function online_num($active_id)
    {
        return $this->db->where('active_id', $active_id)->getValue($this->tableName, "count(*)");
    }

    public function is_online($active_id, $openid)
    {
        $count = $this->db->where('active_id', $active_id)->where('openid', $openid)->getValue($this->tableName, "count(*)");
        if ($count > 0)
            return true;
        return false;
    }

    public function get_user_clients($active_id)
    {
        $selects=['client_id'];
        return $this->db->where('active_id',$active_id)->get($this->tableName,null,$selects);
    }

}