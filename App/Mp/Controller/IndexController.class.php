<?php 

namespace Mp\Controller;
use Mp\Controller\BaseController;

class IndexController extends BaseController {

	/**
	 * 初始化
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function _initialize() {
		if (I('mpid')) {
			$mp_info = M('mp')->find(I('mpid'));
			if ($mp_info['user_id'] == get_user_id()) {
				$token = md5($mp_info['origin_id']);
				M('mp')->where(array('id'=>I('mpid')))->setField('token', $token);
				get_token($token);
				get_mpid(I('mpid'));							// 缓存当前公众号
				D('User/User')->set_default_mp(I('mpid'));		// 设置当前用户默认管理公众号
			} else {
				$this->error('你不具备此公众号的管理权限');
			}
		}
		parent::_initialize();
	}

	/**
	 * 公众号管理首页
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function index() {
		global $_G;
		$info = get_mp_info();
		$this->assign('info', $info);
		$this->addCrumb('公众号管理', U('Mp/Index/index'), '')
			 ->addCrumb('首页', '', 'active')
			 ->addNav('接口配置', '', 'active')
			 ->addNav('清除缓存', U('clear_cache'), '')
			 ->assign('api_url', U('/interface/'.$info['token'].'@'.C('HTTP_HOST')))
			 ->display();
	}

	/**
	 * 清除缓存
	 */
	public function clear_cache() {
		$mp_info = get_mp_info();
		$flag = 'wechat_access_token'.$mp_info['appid'];
		S($flag, null);
		$this->success('清除缓存成功');
	}
}

 ?>