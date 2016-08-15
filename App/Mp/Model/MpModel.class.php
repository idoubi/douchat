<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 微信公众号管理模型
 * @author 艾逗笔<765532665@qq.com>
 */
class MpModel extends Model {

	/**
	 * 自动验证
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected $_validate = array(
		array('name', 'require', '公众号名称不能为空'),
		array('origin_id', 'require', '公众号原始ID不能为空'),
		array('origin_id', '/^gh_[0-9|a-z]{12}$/', '公众号原始ID格式错误'),
		array('origin_id', '', '具有相同原始ID的公众号已存在', 0, 'unique', 1),
		array('type', 'number', '公众号类型错误')
	);

	/**
	 * 自动完成
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected $_auto = array(
		array('status', '1'),
		array('create_time', 'time', 1, 'function'),
		array('valid_token', 'get_nonce', 1, 'function'),
		array('token', 'get_token', 3, 'callback'),
		array('encodingaeskey', 'get_encodingaeskey', 1, 'callback'),
		array('user_id', 'get_user_id', 1, 'function')
	);

	/**
	 * 获取公众号标识
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected function get_token() {
		return md5(I('origin_id'));
	}

	/**
	 * 获取消息加解密秘钥
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected function get_encodingaeskey() {
		return get_nonce(43);
	}

	/**
	 * 获取公众号列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_mp_lists($user_id) {
		if (empty($user_id)) {
			return false;
		}
		$map['user_id'] = $user_id;
		$mp_lists = M('mp')->where($map)->select();
		return $mp_lists;
	}

	/**
	 * 获取公众号信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_mp_info($mpid = '') {
		!$mpid && $mpid = get_mpid();
		if (!$mpid) {
			return false;
		}
		$map['id'] = $mpid;
		$mp_info = M('mp')->where($map)->find();
		return $mp_info;
	}

}


?>