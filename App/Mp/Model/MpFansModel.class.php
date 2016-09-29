<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 公众号粉丝模型
 * @author 艾逗笔<765532665@qq.com>
 */
class MpFansModel extends Model {

	/**
	 * 保存粉丝信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	function save_fans_info($openid = '') {
		if ($openid == '') {
			$openid = get_openid();
		}
		$mpid = get_mpid();
		if (!$openid || !$mpid) {
			return false;
		}
		$fans_wechat_info = get_fans_wechat_info($openid);
		if ($fans_wechat_info) {			// 如果拉取到了微信粉丝信息
			$data['is_subscribe'] = $fans_wechat_info['subscribe'];
			$data['nickname'] = $fans_wechat_info['nickname'];
			$data['sex'] = $fans_wechat_info['sex'];
			$data['language'] = $fans_wechat_info['language'];
			$data['city'] = $fans_wechat_info['city'];
			$data['province'] = $fans_wechat_info['province'];
			$data['country'] = $fans_wechat_info['country'];
			$data['headimgurl'] = $fans_wechat_info['headimgurl'];
			$data['subscribe_time'] = $fans_wechat_info['subscribe_time'];
			$data['remark'] = $fans_wechat_info['remark'];
			$data['groupid'] = $fans_wechat_info['groupid'];
			$data['tagid_list'] = json_encode($fans_wechat_info['tagid_list']);
		} else {		// 如果没有拉取到粉丝信息，比如订阅号
			$data['is_subscribe'] = 1;
			$data['subscribe_time'] = time();
		}
		$data['mpid'] = $mpid;
		$data['openid'] = $openid;
		if ($fansInfo = $this->get_fans_info($openid)) {		// 如果粉丝存在
			if ($fansInfo['is_bind']) {				// 如果粉丝已绑定，则不更新昵称、头像、性别等数据
				unset($data['nickname']);
				unset($data['headimgurl']);
				unset($data['sex']);
			}
			$map['openid'] = $openid;
			$map['mpid'] = $mpid;
			$this->where($map)->save($data);				// 如果粉丝信息存在，则保存粉丝信息
		} else {					// 如果粉丝不存在
			$this->add($data);
		}
		return $data;
	}

	/**
	 * 保存粉丝位置信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function save_fans_location($data, $openid = '') {
		if ($openid == '') {
			$openid = get_openid();
		}
		$mpid = get_mpid();
		if (!$openid || !$mpid) {
			return false;
		}
		if (M('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$openid))->save($data) === false) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 获取粉丝信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	function get_fans_info($openid = '', $field = '') {
		if ($openid == '') {
			$openid = get_openid();
		}
		$mpid = get_mpid();
		if (!$openid || !$mpid) {
			return false;
		}
		$map['openid'] = $openid;
		$map['mpid'] = $mpid;
		$fansInfo = $this->where($map)->find();
		if ($field) {
			return $fansInfo[$field];
		} else {
			return $fansInfo;
		}	
	}

	/**
	 * 获取公众号粉丝
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_fans_list($mpid = '') {
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$mpid) {
			return false;
		}
		$map['mpid'] = $mpid;
		$fans_list = M('mp_fans')->where($map)->order('subscribe_time desc')->select();
		if (!$fans_list) {
			return false;
		}
		return $fans_list;
	}
}



 ?>