<?php

namespace app\wx\model;

use think\Model;
use think\cache\driver\Redis;

class User extends Model
{

    private $redis;
    //初始化
    public function _initialize()
    {
        $redis = new Redis();
        if(!$this->redis){$this->redis = $redis;}
    }

    /**
     * 新增一条数据
     * @param $userInfo
     * @return User
     */
    public function add($userInfo)
    {
            $result = self::create($userInfo);
            return $result;
    }
}
