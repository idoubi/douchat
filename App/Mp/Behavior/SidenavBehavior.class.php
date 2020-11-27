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
		$mod = $params['module'];
		$ctl = $params['controller'];
		$act = $params['action'];
		$access_addons = D('Addons')->get_access_addons();

		$modctl = $mod . '/' . $ctl;
		if (in_array($modctl, ['mp/index', 'mp/payment', 'mp/message', 'mp/fans', 'mp/autoreply', 'mp/material', 'mp/custommenu', 'mp/sceneqrcode', 'mp/addons'])) {
			$sidenav = [
				[
					'title' => '账号接入',
					'url' => U('Index/index'),
					'icon' => 'wechat',
					'class' => $ctl == 'index' ? 'active' : ''
				],
				[
					'title' => '支付管理',
					'url' => U('Payment/wechat'),
					'icon' => 'paypal',
					'class' => $ctl == 'payment' ? 'active' : ''
				],
				[
					'title' => '自动回复',
					'url' => U('AutoReply/keyword'),
					'icon' => 'reply',
					'class' => $ctl == 'autoreply' ? 'active' : ''
				],
				[
					'title' => '自定义菜单',
					'url' => U('CustomMenu/publish'),
					'icon' => 'list-alt',
					'class' => $ctl == 'custommenu' ? 'active' : ''
				],
				[
					'title' => '场景二维码',
					'url' => U('SceneQrcode/lists'),
					'icon' => 'qrcode',
					'class' => $ctl == 'sceneqrcode' ? 'active' : ''
				],
				[
					'title' => '粉丝管理',
					'url' => U('Fans/lists'),
					'icon' => 'user',
					'class' => $ctl == 'fans' ? 'active' : ''
				],
				[
					'title' => '消息管理',
					'url' => U('Message/lists'),
					'icon' => 'comments',
					'class' => $ctl == 'message' ? 'active' : ''
				],
				[
					'title' => '素材管理',
					'url' => U('Material/text'),
					'icon' => 'newspaper-o',
					'class' => $ctl == 'material' ? 'active' : ''
				],
				[
					'title' => '功能插件',
					'url' => U('Mp/Addons/manage'),
					'icon' => 'plug',
					'class' => $ctl == 'addons' ? 'active' : ''
				]
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


			return $sidenav;
		}

		if (in_array($modctl, ['mp/mp', 'mp/user', 'mp/accesskey'])) {
			$sidenav = [
				[
					'title' => '账号管理',
					'class' => $ctl == 'mp' ? 'active' : '',
					'icon' => 'wechat',
					'children' => [
						[
							'title' => '微信公众号',
							'url' => U('Mp/lists', ['type' => 1]),
							'class' => $ctl == 'mp' && $params['type'] == 1 ? 'active' : ''
						],
						[
							'title' => '微信小程序',
							'url' => U('Mp/lists', ['type' => 2]),
							'class' => $ctl == 'mp' && $params['type'] == 2 ? 'active' : ''
						]
					]
				],
				// [
				// 	'title' => '功能插件',
				// 	'url' => U('Mp/Addons/manage'),
				// 	'class' => $ctl == 'addons' ? 'icon icon-job active' : 'icon icon-job',
				// ],
				[
					'title' => '个人中心',
					'url' => '',
					'class' => '',
					'icon' => 'user',
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

			];

			return $sidenav;
		}

		if (get_addon()) {
			$navs = D('Addons')->get_addon_nav();
			foreach ($navs as $v) {
				unset($v['children']);
				$sidenav[] = $v;
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
		}
		return $sidenav;
	}
}
