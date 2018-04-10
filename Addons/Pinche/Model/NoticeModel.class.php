<?php

/**
 * 通知模型
 */
namespace Addons\Pinche\Model;

use Think\Model;

class NoticeModel extends Model {

    protected $tableName = 'pinche_notice';

    public function get() {
        $data = $this->where(['mpid'=>get_mpid()])->select();
        return $data;
    }

    public function getById($id) {
        $data = $this->where(['mpid'=>get_mpid(), 'id'=>$id])->find();
        return $data;
    }
}