<?php

/**
 * 账号管理入口
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Controller;
use Mp\Controller\BaseController;

class IndexController extends BaseController {
	
	// 初始化
	public function __construct() {
		parent::__construct();
		$mpid = I('mpid', get_mpid(), 'intval');
		$mp_info = M('mp')->where(['user_id'=>$this->user_id])->find($mpid);
		if (empty($mp_info)) {
			$this->error('账号不存在或你没有此账号的管理权限');
		}
		$token = md5($mp_info['origin_id']);
		M('mp')->where(['id'=>$mpid,'user_id'=>$this->user_id])->setField('token', $token);
		$this->mpid = get_mpid($mpid);		// 缓存当前管理账号
		$this->mp_type = get_mp_type();		// 获取当前账号类别
		D('User/User')->set_default_mp($mpid);			// 设置当前用户默认管理账号
	}

	// 首页
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