<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * RBAC权限节点管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class NodeController extends BaseController {

	/**
	 * 自定义模型
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public $model = array(
		'name' => 'rbac_node',
		'title' => '权限节点',
		'fields' => array(
			array(
				'name' => 'name',
				'title' => '节点标识',
				'type' => 'text',
				'placeholder' => 'Admin',
				'tip' => '节点英文标识'
			),
			array(
				'name' => 'title',
				'title' => '节点名称',
				'type' => 'text',
				'placeholder' => '后台模块',
				'tip' => '节点中文名称'
			),
			array(
				'name' => 'status',
				'title' => '状态',
				'type' => 'radio',
				'options' => array(
					1 => '启用',
					0 => '禁用'
				),
				'value' => 1
			),
			array(
				'name' => 'remark',
				'title' => '节点描述',
				'type' => 'textarea'
			),
			array(
				'name' => 'sort',
				'title' => '排序',
				'type' => 'text',
				'value' => 0,
				'tip' => '节点按排序值从高到低排列'
			),
			array(
				'name' => 'pid',
				'title' => '上级节点',
				'type' => 'hidden',
				'value' => 0
			),
			array(
				'name' => 'level',
				'title' => '节点类型',
				'type' => 'hidden',
				'value' => 1
			)
		),
		'validate' => array(
			array('name', 'require', '节点标识不能为空', 1, 'regex', 3),
			array('title', 'require', '节点名称不能为空', 1, 'regex', 3),
		),
		'lists' => array(
			array(
				'name' => 'name',
				'title' => '节点标识'
			),
			array(
				'name' => 'title',
				'title' => '节点名称'
			),
			array(
				'name' => 'remark',
				'title' => '节点描述'
			),

		)
	);


	/**
	 * 初始化
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function _initialize() {
		parent::_initialize();
		$action = strtolower(ACTION_NAME);
		$crumb = array(
			array(
				'title' => '系统管理',
				'url' => U('Index/index'),
				'class' => ''
			),
			array(
				'title' => '权限节点管理',
				'url' => U('Node/lists'),
				'class' => ''
			)
		);
		switch ($action) {
			case 'lists':
				$crumb[] = array(
					'title' => '节点列表',
					'url' => '',
					'class' => 'active'
				);
				$nav = array(
					array(
						'title' => '节点列表',
						'url' => '',
						'class' => 'active'
					)
				);
				break;
			case 'add':
				$crumb[] = array(
					'title' => '添加节点',
					'url' => '',
					'class' => 'active'
				);
				$nav = array(
					array(
						'title' => '添加节点',
						'url' => '',
						'class' => 'active'
					)
				);
				break;
			case 'edit':
				$crumb[] = array(
					'title' => '编辑节点',
					'url' => '',
					'class' => 'active'
				);
				$nav = array(
					array(
						'title' => '编辑节点',
						'url' => '',
						'class' => 'active'
					)
				);
				break;
			default:
				# code...
				break;
		}
		
		$this->assign('add_button', true);
		$this->assign('del_button', true);
		$this->assign('crumb', $crumb);
		$this->assign('nav', $nav);
	}

	/**
	 * 添加节点
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add() {
		$this->model['info'] = array(
			'pid' => I('pid'),
			'level' => I('level')
		);
		parent::add();
	}

	/**
	 * 节点列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$results = M('rbac_node')->order('sort desc')->select();
		$nodes = $this->parse_node($results);
		$this->assign('nodes', $nodes);
		$this->display();
	}

	/**
	 * 解析节点数组
	 * @author 艾逗笔<765532665@qq.com>
	 */
	private function parse_node($nodes, $pid = 0) {
		$arr = array();
		foreach ($nodes as $k => $v) {
			if ($v['pid'] == $pid) {
				$v['children'] = $this->parse_node($nodes, $v['id']);
				$arr[] = $v;
			}
		}
		return $arr;
	}
}


?>