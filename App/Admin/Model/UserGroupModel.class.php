<?php 

namespace Admin\Model;
use Think\Model;

class UserGroupModel extends Model {
	
	/**
	 * 获取分组
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_group_lists() {
		$lists = M('user_group')->order('create_time desc')->select();
		return $lists;
	}

	/**
	 * 获取单个分组信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_group_info($group_id) {
		if (!$group_id) {
			return false;
		}
		$map['id'] = $group_id;
		$info = M('user_group')->where($map)->find();
		return $info;
	}
}


 ?>