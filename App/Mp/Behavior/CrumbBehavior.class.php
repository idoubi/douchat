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
			$crumb[] = array(
				'title' => $addon_info['name'],
				'url' => '',
				'class' => ''
			);
			if (ACTION_NAME == 'index') {
				$crumb[] = array(
					'title' => '功能导航',
					'url' => '',
					'class' => 'active'
				);
			} elseif (ACTION_NAME == 'rule') {
				$crumb[] = array(
					'title' => '响应规则',
					'url' => '',
					'class' => 'active'
				);
			} elseif (ACTION_NAME == 'setting') {
				$crumb[] = array(
					'title' => '配置参数',
					'url' => '',
					'class' => 'active'
				);
			} elseif (ACTION_NAME == 'entry') {
				$crumb[] = array(
					'title' => '公众号入口',
					'url' => '',
					'class' => 'active'
				);
			} else {
				$crumb[] = array(
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