<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2018/4/4
 * Time: 12:07
 */

namespace App\Model;


use EasySwoole\Core\Component\Di;

class BaseModel
{
    protected $db;

    function __construct()
    {
        $db = Di::getInstance()->get("MYSQL");
        if ($db instanceof \MysqliDb) {
            $this->db = $db;
        }
    }

}