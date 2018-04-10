<?php

/**
 * 拼车信息模型
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Addons\pinche\Model;

use Think\Model;

class InfoModel extends Model {
	
	protected $tableName = 'pinche_info';

	protected $_validate = array(
        array('name','require','请输入姓名'),
        array('phone','require','请输入手机号码'),
        array('phone','/^1[34578]\d{9}$/','手机号码格式错误'),
        array('departure','require','请选择出发地'),
        array('destination','require','请选择目的地'),
        array('date','require','请选择出发日期'),
        array('time','require','请选择出发时间'),
        array('surplus','require','请选择人数')
    );


    public function deleteOne($id) {
        return $this->where(['mpid'=>get_mpid()])->delete($id);
    }
	
	public function get() {
		$data = $this->where(['mpid'=>get_mpid()])->select();
        return $this->parseData($data);
	}

	public function getById($id) {
	    $data = $this->where(['mpid'=>get_mpid(), 'id'=>$id])->find();
        return $data;
    }

    public function getByOpenid($openid, $page = 1, $per = 20) {
	    $data = $this->where(['mpid'=>get_mpid(), 'openid'=>$openid])
			->page($page, $per)
			->order('addtime desc')
			->select();
	    return $this->parseData($data);
    }
    
    public function getCountByOpenid($openid) {
    	return $this->where(['mpid'=>get_mpid(), 'openid'=>$openid])->count();
	}

    private function parseData($data) {
        $WeappFans = D('WeappFans');
        foreach ($data as &$v) {
            $v['user'] =$WeappFans->getByOpenid($v['openid']);
        }
        return $data;
    }
}