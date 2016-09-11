<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 生成面包屑行为类
 * @author 艾逗笔<765532665@qq.com>
 */
class CrumbBehavior extends Behavior {

	public function run(&$params) {
		$addon = get_addon();
		$addon_info = D('Addons')->get_addon_info();
		if ($addon) {
			$crumb[0] = array(
				'title' => '扩展功能',
				'url' => U('Mp/Addons/manage'),
				'class' => ''
			);
			$crumb[1] = array(
				'title' => $addon_info['name'],
				'url' => U('/addon/'.$addon.'/index'),
				'class' => ''
			);
			if (ACTION_NAME == 'index') {
				$crumb[2] = array(
					'title' => '功能导航',
					'url' => '',
					'class' => 'active'
				);
			} elseif (ACTION_NAME == 'rule') {
				$crumb[2] = array(
					'title' => '响应规则',
					'url' => '',
					'class' => 'active'
				);
			} elseif (ACTION_NAME == 'setting') {
				$crumb[2] = array(
					'title' => '配置参数',
					'url' => '',
					'class' => 'active'
				);
			} elseif (ACTION_NAME == 'entry') {
				$crumb[2] = array(
					'title' => '封面入口',
					'url' => '',
					'class' => 'active'
				);
			} else {
				$crumb[2] = array(
					'title' => '业务导航',
					'url' => '',
					'class' => 'active'
				);
			}
		}
		return $crumb;
	}
}

 ?>