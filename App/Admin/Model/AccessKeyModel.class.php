<?php

namespace Admin\Model;
use Think\Model;

/**
 * access_key模型
 */
class AddonsModel extends Model {

    /**
     * 获取公众号的一个access_key
     * @desc
     * @author 16
     * @date 2018/5/16
     */
    public function get_mp_access_key(){
        $map['mpid'] = get_mpid();
        $map['status'] = 1;

       return $this->field('ak,sk')->where($map)->order('update_at DESC')->find();
    }
}

