<?php

/**
 * 生成侧边栏导航行为类
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Behavior;
use Think\Behavior;

class SidenavBehavior extends Behavior {

	public function run(&$params) {
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
							'url' => U('Mp/lists', ['mp_type'=>1]),
							'class' => $ctl == 'mp' && $params['mp_type'] == 1 ? 'active' : ''
						],
						[
							'title' => '微信小程序',
							'url' => U('Mp/lists', ['mp_type'=>2]),
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
						]
					]
				]
			];
		} elseif (in_array($ctl, ['addons']) || get_addon()) {
			foreach ($access_addons as $k => $v) {
				if (isset($v['config']['sidebar']) && $v['config']['sidebar'] == 1) {
					if (isset($v['config']['sidebar_list']['addon'])) {
						$mp_sidebar = $v['config']['sidebar_list']['mp'];
						foreach ($mp_sidebar as $kk => $vv) {
							$sidenav[] = $vv;
						}
					}
				}
				if (get_addon() == $v['bzname']) {
					$v['class'] = 'active';
				}
				$addons[] = $v;
			}
			$sidenav[] = [
				'title' => '全部应用',
				'url' => 'javascript:;',
				'class' => 'icon icon-ul',
				'attr' => 'data="icon"',
				'children' => $addons
			];
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
                            'url' => U('Fans/lists') ,
                            'class' => $ctl == 'fans' ? 'active' : ''
                        ]
					]
				]
			];
		} else {
			$sidenav = [
				array(
					'title' => '公众号功能',
					'url' => 'javascript:;',
					'class' => 'icon icon-signup',
					'attr' => 'data="icon"',
					'children' => [
						[
							'title' => '基础设置',
							'url' => U('Index/index'),
							'class' => ''
						],
						[
							'title' => '微信支付',
							'url' => U('Payment/wechat'),
							'class' => ''
						],
						[
							'title' => '自动回复',
							'url' => U('AutoReply/keyword'),
							'class' => ''
						],
						[
							'title' => '自定义菜单',
							'url' => U('CustomMenu/publish'),
							'class' => ''
						],
						[
							'title' => '场景二维码',
							'url' => U('SceneQrcode/lists'),
							'class' => ''
						],
						[
							'title' => '粉丝管理',
							'url' => U('Fans/lists'),
							'class' => ''
						],
						[
							'title' => '消息管理',
							'url' => U('Message/lists'),
							'class' => ''
						],
						[
							'title' => '素材管理',
							'url' => U('Material/text'),
							'class' => ''
						]
					]
				),
				[
					'title' => '应用功能',
					'url' => 'javascript:;',
					'class' => 'icon icon-job',
					'attr' => 'data="icon"',
					'children' => [
					
					]
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
		}
		return $sidenav;
	}
}

 ?>