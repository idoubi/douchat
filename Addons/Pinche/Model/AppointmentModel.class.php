<?php

/**
 * 预约模型
 */
namespace Addons\Pinche\Model;

use Think\Model;

class AppointmentModel extends Model {

    protected $tableName = 'pinche_appointment';
	
    
    public function getByIid($iid) {
    	$data = $this->where(['mpid'=>get_mpid(), 'iid'=>$iid])->select();
    	return $this->parseData($data);
	}

    public function getByOpenid($openid) {
        $data = $this->where(['mpid'=>get_mpid(), 'openid'=>$openid])->select();
        return $this->parseData($data);
    }

    public function parseData($data) {
        $WeappFans = D('Mp/WeappFans');
        $Info = D('Addons://Pinche/Info');
        foreach ($data as &$v) {
            $v['info'] = $Info->getById($v['iid']);
            $v['user'] = $WeappFans->getByOpenid($v['openid']);
        }
        return $data;
    }
}