<?php 

namespace Admin\Model;
use Think\Model;

/**
 * 公众号模型
 * @author 艾逗笔<765532665@qq.com>
 */
class MpModel extends Model {

	/**
	 * 根据公众号ID获取公众号基本信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_mp_info($mpid = '') {
		if (!$mpid) {
			return false;
		}
		$mp_info = $this->find(intval($mpid));
		if (!$mp_info) {
			return false;
		}
		return $mp_info;
	}

}

?>