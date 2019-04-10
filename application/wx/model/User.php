<?php

namespace app\wx\model;

use think\Model;
use think\cache\driver\Redis;
use think\Db;

class User extends Model
{

    private $redis;
    //初始化
    public function _initialize()
    {
        $redis = new Redis();
        if(!$this->redis){$this->redis = $redis;}
    }

    public function add($userInfo)
    {
        //判断数据条数
        if(count($userInfo)>1){
            $result = self::create($userInfo);
            return $result;
        }else{
            Db::startTrans();
            try{
                $result = $this->saveAll($userInfo);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            return $result;
        }
    }
}
