<?php

/**
 * 生成侧边栏导航行为类
 * @author 艾逗笔<http://idoubi.cc>
 */

namespace Mp\Behavior;

use Think\Behavior;

class SidenavBehavior extends Behavior
{

	public function run(&$params)
	{
		$ctl = $params['controller'];
		$act = $params['action'];
		$access_addons = D('Addons')->get_access_addons();
		if (in_array($ctl, ['mp', 'user', 'accesskey'])) {
			$sidenav = [
				[
					'title' => '账号管理',
					'url' => '',
					'class' => 'icon icon-ul',
					'children' => [
						[
							'title' => '微信公众号',
							'url' => U('Mp/lists', ['mp_type' => 1]),
							'class' => $ctl == 'mp' && $params['mp_type'] == 1 ? 'active' : ''
						],
						[
							'title' => '微信小程序',
							'url' => U('Mp/lists', ['mp_type' => 2]),
							'class' => $ctl == 'mp' && $params['mp_type'] == 2 ? 'active' : ''
						]
					]
				],
				[
					'title' => '个人中心',
					'url' => '',
					'class' => 'icon icon-user',
					'children' => [
						[
							'title' => '基本资料',
							'url' => U('User/profile'),
							'class' => $ctl == 'user' && $act == 'profile' ? 'active' : ''
						],
						[
							'title' => '修改密码',
							'url' => U('User/change_password'),
							'class' => $ctl == 'user' && $act == 'change_password' ? 'active' : ''
						],
						[
							'title' => '秘钥管理',
							'url' => U('AccessKey/lists'),
							'class' => $ctl == 'accesskey' ? 'active' : ''
						],

					]
				],
				[
					'title' => '应用中心',
					'url' => U('Mp/Addons/manage'),
					'class' => 'icon icon-job'
				]
			];
		} elseif (in_array($ctl, ['addons']) || get_addon()) {
			if ($act == 'manage') {
				$sidenav[] = [
					'title' => '全部应用',
					'url' => U('Addons/manage'),
					'class' => 'icon icon-list active'
				];
			} else {
				$sidenav[] = [
					'title' => get_addon_name(),
					'url' => 'javascript:;',
					'class' => 'icon icon-home',
					'attr' => 'data="icon"',
					'children' => D('Addons')->get_addon_nav()
				];
			}
		} elseif ($params['mp_type'] == 2) {
			$sidenav = [
				[
					'title' => '小程序管理',
					'url' => 'javascript:void(0)',
					'class' => 'icon icon-signup',
					'children' => [
						[
							'title' => '账号信息',
							'url' => U('Index/index'),
							'class' => $ctl == 'index' ? 'active' : ''
						],
						[
							'title' => '粉丝管理',
							'url' => U('Fans/lists'),
							'class' => $ctl == 'fans' ? 'active' : ''
						],
						[
							'title' => '支付管理',
							'url' => U('Payment/wechat'),
							'class' => $ctl == 'payment' ? 'active' : ''
						],
						[
							'title' => '模板消息',
							'url' => U('Tempmsg/lists'),
							'class' => $ctl == 'tempmsg' ? 'active' : ''
						]
					]
				]
			];
		} else {
			$sidenav = [
				array(
					'title' => '功能',
					'url' => 'javascript:;',
					'class' => 'icon icon-signup',
					'attr' => 'data="icon"',
					'children' => [
						[
							'title' => '基础设置',
							'url' => U('Index/index'),
							'class' => $ctl == 'index' ? 'active' : ''
						],
						[
							'title' => '支付管理',
							'url' => U('Payment/wechat'),
							'class' => $ctl == 'payment' ? 'active' : ''
						],
						[
							'title' => '自动回复',
							'url' => U('AutoReply/keyword'),
							'class' => $ctl == 'autoreply' ? 'active' : ''
						],
						[
							'title' => '自定义菜单',
							'url' => U('CustomMenu/publish'),
							'class' => $ctl == 'custommenu' ? 'active' : ''
						],
						[
							'title' => '场景二维码',
							'url' => U('SceneQrcode/lists'),
							'class' => $ctl == 'sceneqrcode' ? 'active' : ''
						],
						[
							'title' => '粉丝管理',
							'url' => U('Fans/lists'),
							'class' => $ctl == 'fans' ? 'active' : ''
						],
						[
							'title' => '消息管理',
							'url' => U('Message/lists'),
							'class' => $ctl == 'message' ? 'active' : ''
						],
						[
							'title' => '素材管理',
							'url' => U('Material/text'),
							'class' => $ctl == 'material' ? 'active' : ''
						]
					]
				),
				// [
				// 	'title' => '应用功能',
				// 	'url' => 'javascript:;',
				// 	'class' => 'icon icon-job',
				// 	'attr' => 'data="icon"',
				// 	'children' => []
				// ]
			];
			foreach ($access_addons as $k => $v) {
				if (isset($v['config']['sidebar']) && $v['config']['sidebar'] == 1) {
					if (isset($v['config']['sidebar_list']['mp'])) {
						$mp_sidebar = $v['config']['sidebar_list']['mp'];
						foreach ($mp_sidebar as $kk => $vv) {
							$sidenav[] = $vv;
						}
					}
				}
			}
		}
		return $sidenav;
	}
}
