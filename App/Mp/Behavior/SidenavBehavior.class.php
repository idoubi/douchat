<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 生成侧边栏导航行为类
 * @author 艾逗笔<765532665@qq.com>
 */
class SidenavBehavior extends Behavior {

	public function run(&$params) {
		$access_addons = D('Addons')->get_access_addons();
		if (CONTROLLER_NAME == 'Mp') {
			$sidenav = array(
				array(
					'title' => '公众号管理',
					'url' => U('Mp/lists'),
					'class' => 'icon icon-ul active'
				)
			);
		} elseif (CONTROLLER_NAME == 'Addons' || get_addon()) {
			$sidenav[] = array(
				'title' => '扩展功能',
				'url' => 'javascript:;',
				'class' => 'icon icon-ul active',
				'attr' => 'data="icon"',
				'children' => $access_addons
			);
			foreach ($access_addons as $k => $v) {
				if (isset($v['config']['sidebar']) && $v['config']['sidebar'] == 1) {
					if (isset($v['config']['sidebar_list']['addon'])) {
						$mp_sidebar = $v['config']['sidebar_list']['mp'];
						foreach ($mp_sidebar as $kk => $vv) {
							$sidenav[] = $vv;
						}
					}
				}
			}
		} else {
			$sidenav = array(
				array(
					'title' => '首页',
					'url' => U('Index/index'),
					'class' => 'icon icon-home'
				),
				array(
					'title' => '基础设置',
					'url' => 'javascript:;',
					'class' => 'icon icon-setting',
					'attr' => 'data="icon"',
					'children' => array(
						array(
							'title' => '微信支付',
							'url' => U('Payment/wechat'),
							'class' => ''
						)
					)
				),
				array(
					'title' => '基本功能',
					'url' => 'javascript:;',
					'class' => 'icon icon-signup',
					'attr' => 'data="icon"',
					'children' => array(
						array(
							'title' => '自动回复',
							'url' => U('AutoReply/keyword'),
							'class' => ''
						),
						array(
							'title' => '自定义菜单',
							'url' => U('CustomMenu/publish'),
							'class' => ''
						),
						array(
							'title' => '场景二维码',
							'url' => U('SceneQrcode/lists'),
							'class' => ''
						)
					)
				),
				array(
					'title' => '管理功能',
					'url' => 'javascript:;',
					'class' => 'icon icon-job',
					'attr' => 'data="icon"',
					'children' => array(
						array(
							'title' => '粉丝管理',
							'url' => U('Fans/lists'),
							'class' => ''
						),
						array(
							'title' => '消息管理',
							'url' => U('Message/lists'),
							'class' => ''
						),
						array(
							'title' => '素材管理',
							'url' => U('Material/text'),
							'class' => ''
						)
					)
				)
			);
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