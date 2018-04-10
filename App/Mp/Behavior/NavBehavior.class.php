<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 生成插件导航行为类
 * @author 艾逗笔<765532665@qq.com>
 */
class NavBehavior extends Behavior {

	public function run(&$params) {
		$addon = get_addon();
		if ($addon && ACTION_NAME == 'index') {
			$addonnav['index'] = array(
				'title' => '功能导航',
				'url' => U('Mp/Web/index', array('addon'=>$addon)),
				'class' => 'active'
			);	
			// return $addonnav;
		}
		$addon_config = D('Addons')->get_addon_config();
		if ($addon_config['respond_rule'] == 1) {
			$addonnav['rule'] = array(
				'title' => '响应规则',
				'url' => U('/addon/'.$addon.'/rule'),
				'class' => ACTION_NAME == 'rule' ? 'active' : ''
			);
		}
		if ($addon_config['setting'] == 1) {
			if (isset($addon_config['setting_list_group'])) {
				foreach ($addon_config['setting_list_group'] as $k => $v) {
					if ($v['is_show'] == 1) {
						if (I('get.type')) {
							$type = I('get.type');
						} elseif ($addon_config['setting_list_default_group']) {
							$type = $addon_config['setting_list_default_group'];
						} else {
							$types = array_keys($addon_config['setting_list_group']);
							$type = $types[0];
						}
						$children[] = array(
							'title' => $v['title'],
							'url' => U('/addon/'.$addon.'/setting', array('type'=>$k)),
							'class' => $type == $k ? 'active' : ''
						);
					}
				}
			} else {
				$children = array(
					array(
						'title' => '默认配置',
						'url' => U('/addon/'.$addon.'/setting'),
						'class' => 'active'
					)
				);
			}
			$addonnav['setting'] = array(
				'title' => '配置参数',
				'url' => U('/addon/'.$addon.'/setting'),
				'class' => ACTION_NAME == 'setting' ? 'active' : '',
				'children' => $children
			);
		}
		if ($addon_config['entry'] == 1) {
			$entry_list = $this->parse_entry($addon_config['entry_list']);
			$addonnav['entry'] = array(
				'title' => '公众号入口',
				'url' => !empty($entry_list) ? $entry_list[0]['url'] : '',
				'class' => $addon_config['entry_list'][I('act')] ? 'active' : '',
				'children' => $entry_list
			);
		}
		if ($addon_config['menu'] == 1) {
			$menu_list = $this->parse_menu($addon_config['menu_list']);
			$addonnav['menu'] = array(
				'title' => '业务导航',
				'url' => !empty($menu_list) ? $menu_list[0]['url'] : '',
				'class' => ACTION_NAME != 'rule' && ACTION_NAME != 'setting' && ACTION_NAME !='entry' ? 'active' : '',
				'children' => $menu_list
			);
		}
		return $addonnav;
	}

	private function parse_entry($entry_list) {
		foreach ($entry_list as $k => $v) {
			$arr['title'] = $v;
			$arr['url'] = U('/addon/'.get_addon().'/entry/'.$k);
			$arr['class'] = I('act') == $k ? 'active' : '';
			$children[] = $arr;
		}
		return $children;
	}

	private function parse_menu($menu_list) {
		foreach ($menu_list as $k => $v) {
			$arr['title'] = $v;
			$arr['url'] = U('/addon/'.get_addon().'/web/'.$k);
			$arr['class'] = ACTION_NAME == $k ? 'active' : '';
			$children[] = $arr;
		}
		return $children;
	}
}

 ?>