<?php

/**
 * 留言数据管理模型
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Addons\IdouGuestbook\Model;
use Think\Model;

class IdouGuestbookListModel extends Model {
	
	/**
	 * 获取数据列表
	 */
	public function get($page = 1, $per = 10, $map = [], $order = 'create_time desc') {
		if (!isset($map['mpid'])) {
			$map['mpid'] = get_mpid();
		}
		return $this->where($map)->page($page, $per)->order($order)->select();
	}
	
}

?>