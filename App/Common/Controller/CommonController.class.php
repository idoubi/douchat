<?php 

namespace Common\Controller;
use Think\Controller;

/**
 * 模块公用控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class CommonController extends Controller {

	public $model = array();

	/**
	 * 初始化
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected function _initialize() {
		if (!is_file(SITE_PATH.'/Data/install.lock')) {				// 如果框架未安装，则跳转到安装页面
			$this->redirect('Install/Index/index');
		}
		global $_G;
		$_G['site_path'] = SITE_PATH . '/';
		$_G['site_url'] = str_replace('index.php', '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		$_G['addons_path'] = str_replace('./', $_G['site_path'], ADDON_PATH);
		$_G['addons_url'] = $_G['site_url'] . str_replace('./', '', ADDON_PATH);
		$_G['module_name'] = strtolower(MODULE_NAME);
		$_G['controller_name'] = strtolower(CONTROLLER_NAME);
		$_G['action_name'] = strtolower(ACTION_NAME);
		add_hook('rbac', 'Common\Behavior\RbacBehavior');
		hook('rbac');											// 执行权限检测钩子
		$this->user_id = session(C('USER_AUTH_KEY'));			
		$this->user_info = get_user_info();
		$this->user_access = D('User/User')->get_user_access($this->user_id);
	}

	/**
	 * 通用数据列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function common_lists($model = array()) {
		cookie('__forward__', $_SERVER['HTTP_REFERER']);
		!empty($model) && $this->model = $model;
		if (IS_POST) {
			$target = I('target');
			$keyword = I('keyword');
			// if (!$target) {
			// 	$this->error('请选择搜索项');
			// }
			// if (!$keyword) {
			// 	$this->error('请输入搜素内容');
			// }
			$this->model['list_map'][$target] =  array('like', '%'.$keyword.'%');
			$this->assign('search_tip', $this->model['list_search'][$target].'为“'.$keyword.'”的搜索结果');
			$this->assign('keyword', $keyword);
			$this->assign('target', $target);
		}
		$fields_arr = array();
		foreach ($this->model['lists'] as $k => $v) {
			if (!$v['name']) {
				$v['name'] = $k;
			}
			$fields_arr[] = $v['name'];
		}
		$fields = implode(',', $fields_arr);
		$lists['fields'] = $this->model['lists'];

		$page = max(1, intval(I('p')));
		$per = $this->model['per'] ? $this->model['per'] : 20;
		if ($this->model['name']) {
			$count = M($this->model['name'])->where($this->model['list_map'])->count();
			$results = M($this->model['name'])->where($this->model['list_map'])->field($fields)->order($this->model['list_order'])->page($page.','.$per)->select();
		} else {
			$list_data = isset($this->model['list_data']) ? $this->model['list_data'] : array();
			$count = $list_data ? count($list_data) : 0;
			$per = min($count,$per);
			$results = array();
			$begin = ($page-1)*$per;
			$end = $begin+$per;
			$n = 0;
			for($i=$begin;$i<$end;$i++) {
				$results[$n] = $list_data[$i];
				$n++;
			}
		}	
		foreach ($results as $k => &$v) {
			foreach ($this->model['lists'] as $m => $n) {
				if (!$n['name']) {
					$n['name'] = $m;
				}
				if ($n['format'] == 'image') {
					$src = $v[$n['name']] ? $v[$n['name']] : $n['extra']['placeholder'];
					$data[$k][$m] = "<img src='".$src."' ".$n['extra']['attr']." />";
				} elseif ($n['format'] == 'enum') {
					$options = $n['extra']['options'];
					$data[$k][$m] = $options[$v[$n['name']]];
				} elseif ($n['format'] == 'function') {													// 使用函数进行格式化
					$function = $n['extra']['function_name'];												// 用来对数据进行格式化的函数名称
					if (!$n['extra']['params']) {													// 如果参数不存在或者只有一个参数，则直接使用函数进行格式化
						$data[$k][$m] = $function($v[$n['name']]);									// 对数据进行格式化																// 跳出本次循环
					} else {																		// 存在两个及以上参数
						$params_str = str_replace('###', $v[$n['name']], $n['extra']['params']);
						$params_arr = explode(',', $params_str);
						switch (count($params_arr)) {
							case 1:
								$data[$k][$m] = $function($params_arr[0]);
								break;
							case 2:
								$data[$k][$m] = $function($params_arr[0], $params_arr[1]);
								break;
							case 3:
								$data[$k][$m] = $function($params_arr[0], $params_arr[1], $params_arr[2]);
								break;
							case 4:
								$data[$k][$m] = $function($params_arr[0], $params_arr[1], $params_arr[2], $params_arr[3]);
								break;
							default:
								$data[$k][$m] = $function($v[$n['name']]);
								break;
						}
					}
				} elseif ($n['format'] == 'callback') {													// 使用回调函数进行格式化
					$callback = $n['extra']['callback_name'];												// 用来对数据进行格式化的回调函数名称
					if (!$n['extra']['params']) {	// 如果参数不存在或者只有一个参数，则直接使用回调函数进行格式化
						$data[$k][$m] = $this->$callback($v[$n['name']]);									// 对数据进行格式化																// 跳出本次循环
					} else {																		// 存在两个及以上参数
						$params_str = str_replace('###', $n['name'], $n['extra']['params']);
						$params_arr = explode(',', $params_str);
						switch (count($params_arr)) {
							case 1:
								$data[$k][$m] = $this->$callback($v[$params_arr[0]]);
								break;
							case 2:
								$data[$k][$m] = $this->$callback($v[$params_arr[0]], $v[$params_arr[1]]);
								break;
							case 3:
								$data[$k][$m] = $this->$callback($v[$params_arr[0]], $v[$params_arr[1]], $v[$params_arr[2]]);
								break;
							case 4:
								$data[$k][$m] = $this->$callback($v[$params_arr[0]], $v[$params_arr[1]], $v[$params_arr[2]], $v[$params_arr[3]]);
								break;
							default:
								$data[$k][$m] = $this->$callback($v[$n['name']]);
								break;
						}
					}
				} elseif ($n['format'] == 'custom') {
					$format = '';
					foreach ($n['extra']['options'] as $p => $q) {
						$q[0] || $q[0] = $q['title'];
						$q[1] || $q[1] = $q['url'];
						$q[2] || $q[2] = $q['class'];
						$q[3] || $q[3] = $q['attr'];
						preg_match_all('/%7B(.*?)%7D/', $q[1], $match);
						if ($match[1]) {
							foreach($match[1] as $mm) {
								$search[] = '%7B'.$mm.'%7D';
								$replace[] = $v[$mm];
							}
						}
						$q[1] = str_replace($search, $replace, $q[1]);
						unset($search);
						unset($replace);
						$format .= '<a href="'.$q[1].'" class="'.$q[2].'" '.$q[3].'>'.$q[0].'</a>&nbsp;';
					}
				
					$data[$k][$m] = $format;
				} else {
					$data[$k][$m] = $v[$n['name']] !== '' ? $v[$n['name']] : $n['extra']['placeholder'];
				}
				if ($data[$k][$m] == '') {
					$data[$k][$m] = $n['extra']['placeholder'];
				}
			}
		}
		$lists['data'] = $data;
		$pagination = pagination($count, $per, $this->model['list_map']);
		$this->assign('pagination', $pagination);
		$this->assign('count', $count);
		$this->assign('model', $this->model);
		$this->assign('lists', $lists);
		$templateFile = APP_PATH . MODULE_NAME . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . C('DEFAULT_THEME') . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'lists' . C('TMPL_TEMPLATE_SUFFIX');
		if (!is_file($templateFile)) {
			$templateFile = APP_PATH . 'Common' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . C('DEFAULT_THEME') . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'lists' . C('TMPL_TEMPLATE_SUFFIX');
		}
		$this->display($templateFile);
	}

	/**
	 * 通用新增数据
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function common_add($model = array()) {
		!empty($model) && $this->model = $model;
		$add_success_url = $this->model['add_success_url'] ? $this->model['add_success_url'] : U('lists', array('addon'=>get_addon())); 
		$add_success_info = $this->model['add_success_info'] ? $this->model['add_success_info'] : '新增成功';
		$add_error_info = $this->model['add_error_info'] ? $this->model['add_error_info'] : '新增失败';
		if (IS_POST) {
			$fields = (json_decode($_POST['fields'], true));
			foreach ($fields as $k => $v) {
				if (isset($v['pre_type']) && isset($v['pre_name']) && isset($v['name'])) {
					if ($v['pre_type'] == 'function') {
						$function = $v['pre_name'];
						if (!isset($v['pre_params'])) {									
							$_POST[$v['name']] = $function($_POST[$v['name']]);		
						} else {	
							$params_str = str_replace('###', $_POST[$v['name']], $v['pre_params']);
							$params_arr = explode(',', $params_str);
							switch (count($params_arr)) {
								case 1:
									$_POST[$v['name']] = $function($params_arr[0]);
									break;
								case 2:
									$_POST[$v['name']] = $function($params_arr[0], $params_arr[1]);
									break;
								case 3:
									$_POST[$v['name']] = $function($params_arr[0], $params_arr[1], $params_arr[2]);
									break;
								case 4:
									$_POST[$v['name']] = $function($params_arr[0], $params_arr[1], $params_arr[2], $params_arr[3]);
									break;
								default:
									$_POST[$v['name']] = $function($_POST[$v['name']]);
									break;
							}
						}
					} elseif ($v['pre_type'] == 'callback') {
						$callback = $v['pre_name'];
						if (!isset($v['pre_params'])) {									
							$_POST[$v['name']] = $this->$callback($_POST[$v['name']]);		
						} else {	
							$params_str = str_replace('###', $_POST[$v['name']], $v['pre_params']);
							$params_arr = explode(',', $params_str);
							switch (count($params_arr)) {
								case 1:
									$_POST[$v['name']] = $this->$callback($params_arr[0]);
									break;
								case 2:
									$_POST[$v['name']] = $this->$callback($params_arr[0], $params_arr[1]);
									break;
								case 3:
									$_POST[$v['name']] = $this->$callback($params_arr[0], $params_arr[1], $params_arr[2]);
									break;
								case 4:
									$_POST[$v['name']] = $this->$callback($params_arr[0], $params_arr[1], $params_arr[2], $params_arr[3]);
									break;
								default:
									$_POST[$v['name']] = $this->$callback($_POST[$v['name']]);
									break;
							}
						}
					}
				}
			}
			unset($_POST['fields']);
			$Model = M($this->model['name']);
			$_validate = $this->model['validate'];
			$_auto = $this->model['auto'];
			$Model->setProperty('_validate', $_validate);
			$Model->setProperty('_auto', $_auto);
			
			if (!$Model->create()) {
				$this->error($Model->getError());
			} else {
				$res = $Model->add();
				if (!$res) {
					$this->error($add_error_info);
				} else {
					$add_success_url = str_replace('%7Bpk%7D', $res, $add_success_url);
					$this->success($add_success_info, $add_success_url);
				}
			}
		} else {
			// foreach (I('get.') as $k => $v) {
			// 	$map[$k] = $v;
			// }
			if (I('id')) {
				$map['id'] = intval(I('id'));
			}
			if ($map) {
				$info = M($this->model['name'])->where($map)->find();
			}
			!empty($this->model['info']) && $info = $this->model['info'];
			foreach ($this->model['fields'] as $k => &$v) {
				foreach ($v['extra'] as $m => $n) {
					if (!isset($v[$m])) {
						$v[$m] = $n;
					}
				}
				if (!$v['name']) {
					$v['name'] = $k;
				}
				if ($info[$v['name']] != '') {
					$v['value'] = $info[$v['name']];
				}
				if (isset($v['options']) && $v['options'] == 'callback') {
					$callback = $v['callback_name'];
					$v['options'] = $this->$callback();
				}
				$fields[] = $v;
			}
			$this->assign('info', $info);
			$this->assign('fields', $fields);
			$this->assign('model', $this->model);
			$templateFile = APP_PATH . MODULE_NAME . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . C('DEFAULT_THEME') . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'add' . C('TMPL_TEMPLATE_SUFFIX');
			if (!is_file($templateFile)) {
				$templateFile = APP_PATH . 'Common' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . C('DEFAULT_THEME') . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'add' . C('TMPL_TEMPLATE_SUFFIX');
			}
			$this->display($templateFile);
		}	
	}

	/**
	 * 通用编辑数据
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function common_edit($model = array()) {
		!empty($model) && $this->model = $model;
		$edit_success_url = $this->model['edit_success_url'] ? $this->model['edit_success_url'] : U('lists', array('addon'=>get_addon())); 
		$edit_success_info = $this->model['edit_success_info'] ? $this->model['edit_success_info'] : '编辑成功';
		$edit_error_info = $this->model['edit_error_info'] ? $this->model['edit_error_info'] : '编辑失败';
		if (IS_POST) {
			$fields = (json_decode($_POST['fields'], true));
			foreach ($fields as $k => $v) {
				if (isset($v['pre_type']) && isset($v['pre_name']) && isset($v['name'])) {
					if ($v['pre_type'] == 'function') {
						$function = $v['pre_name'];
						if (!isset($v['pre_params'])) {									
							$_POST[$v['name']] = $function($_POST[$v['name']]);		
						} else {	
							$params_str = str_replace('###', $_POST[$v['name']], $v['pre_params']);
							$params_arr = explode(',', $params_str);
							switch (count($params_arr)) {
								case 1:
									$_POST[$v['name']] = $function($params_arr[0]);
									break;
								case 2:
									$_POST[$v['name']] = $function($params_arr[0], $params_arr[1]);
									break;
								case 3:
									$_POST[$v['name']] = $function($params_arr[0], $params_arr[1], $params_arr[2]);
									break;
								case 4:
									$_POST[$v['name']] = $function($params_arr[0], $params_arr[1], $params_arr[2], $params_arr[3]);
									break;
								default:
									$_POST[$v['name']] = $function($_POST[$v['name']]);
									break;
							}
						}
					} elseif ($v['pre_type'] == 'callback') {
						$callback = $v['pre_name'];
						if (!isset($v['pre_params'])) {									
							$_POST[$v['name']] = $this->$callback($_POST[$v['name']]);		
						} else {	
							$params_str = str_replace('###', $_POST[$v['name']], $v['pre_params']);
							$params_arr = explode(',', $params_str);
							switch (count($params_arr)) {
								case 1:
									$_POST[$v['name']] = $this->$callback($params_arr[0]);
									break;
								case 2:
									$_POST[$v['name']] = $this->$callback($params_arr[0], $params_arr[1]);
									break;
								case 3:
									$_POST[$v['name']] = $this->$callback($params_arr[0], $params_arr[1], $params_arr[2]);
									break;
								case 4:
									$_POST[$v['name']] = $this->$callback($params_arr[0], $params_arr[1], $params_arr[2], $params_arr[3]);
									break;
								default:
									$_POST[$v['name']] = $this->$callback($_POST[$v['name']]);
									break;
							}
						}
					}
				}
			}
			unset($_POST['fields']);
			$Model = M($this->model['name']);
			$_validate = $this->model['validate'];
			$_auto = $this->model['auto'];
			$Model->setProperty('_validate', $_validate);
			$Model->setProperty('_auto', $_auto);
			if (!$Model->create()) {
				$this->error($Model->getError());
			} else {
				if ($this->model['edit_map']) {
					foreach ($this->model['edit_map'] as $k => $v) {
						$edit_map[$k] = $v;
					}
				}
				$res = $Model->where($edit_map)->save();
				if ($res === false) {
					$this->error($edit_error_info);
				} else {
					$this->success($edit_success_info, $edit_success_url);
				}
			}
		} else {
			if ($this->model['find_map']) {
				$info = M($this->model['name'])->where($this->model['find_map'])->find();
				if (!$info) {
					$this->error('数据不存在或你没有编辑此条数据的权限');
				}
			} elseif ($this->model['edit_map']) {
				$info = M($this->model['name'])->where($this->model['edit_map'])->find();
				if (!$info) {
					$this->error('数据不存在或你没有编辑此条数据的权限');
				}
			}
			!empty($this->model['info']) && $info = $this->model['info'];
			foreach ($this->model['fields'] as $k => &$v) {
				foreach ($v['extra'] as $m => $n) {
					if (!isset($v[$m])) {
						$v[$m] = $n;
					}
				}
				if (!$v['name']) {
					$v['name'] = $k;
				}
				if ($info[$v['name']] != '') {
					$v['value'] = $info[$v['name']];
				}
				if (isset($v['options']) && $v['options'] == 'callback') {
					$callback = $v['callback_name'];
					$v['options'] = $this->$callback();
				}
				$fields[] = $v;
			}
			$this->assign('hiddens', $edit_map);
			$this->assign('info', $info);
			$this->assign('fields', $fields);
			$this->assign('model', $this->model);
			$templateFile = APP_PATH . MODULE_NAME . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . C('DEFAULT_THEME') . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'edit' . C('TMPL_TEMPLATE_SUFFIX');
			if (!is_file($templateFile)) {
				$templateFile = APP_PATH . 'Common' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . C('DEFAULT_THEME') . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . 'edit' . C('TMPL_TEMPLATE_SUFFIX');
			}
			$this->display($templateFile);
		}	
	}

	/**
	 * 通用数据删除
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function common_delete($model = array()) {
		!empty($model) && $this->model = $model;
		$delete_success_url = $this->model['delete_success_url'] ? $this->model['delete_success_url'] : U('lists', array('addon'=>get_addon())); 
		$delete_success_info = $this->model['delete_success_info'] ? $this->model['delete_success_info'] : '删除成功'; 
		if (IS_POST) {			// 批量删除
			if (isset($_POST['action']) && $_POST['action'] == 'mass_delete') {
				$model = I('model');
				if (!M($model)->autoCheckToken($_POST)) {
					$this->error('表单令牌错误');
				}
				$key = I('mass_delete_key');
				if (!$key || !I($key)) {
					$this->error('要批量删除的数据不存在');
				}
				foreach (I($key) as $k => $v) {
					$map[$key] = $v;
					$res = M($model)->where($map)->delete();
					if (!$res) {
						$this->error('删除数据错误');
					}
				}
				$this->success($delete_success_info, $delete_success_url);
			}
		} else {
			if ($this->model['delete_map']) {
				if (!M($this->model['name'])->where($this->model['delete_map'])->find()) {
					$this->error('数据不存在或你没有删除此条数据的权限');
				}
				foreach ($this->model['delete_map'] as $k => $v) {
					$delete_map[$k] = $v;
				}
			}
			$res = M($this->model['name'])->where($delete_map)->delete();
			if ($res === false) {
				$this->error('删除数据错误');
			} else {
				$this->success($delete_success_info, $delete_success_url);
			}
		}
	}

	/**
	 * 设置浏览器标题
	 */
	public function setMetaTitle($meta_title) {
		$this->model['meta_title'] = $meta_title;
		return $this;
	}

	/**
	 * 设置表单提交方式
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setSubmitType($submit_type) {
		$this->model['submit_type'] = $submit_type;
		return $this;
	}

	/**
	 * 设置模型
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setModel($model) {
		if (is_array($model)) {
			$this->model = $model;
		} else {
			$this->model['name'] = $model;
		}
		return $this;
	}

	/**
	 * 设置查询条件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setListMap($map) {
		$this->model['list_map'] = $map;
		return $this;
	}

	/**
	 * 设置搜索条件
	 */
	public function setListSearch($search) {
		$this->model['list_search'] = $search;
		return $this;
	}

	/**
	 * 设置数据排序
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setListOrder($order) {
		$this->model['list_order'] = $order;
		return $this;
	}

	public function setListPer($per) {
		$this->model['per'] = intval($per);
		return $this;
	}

	/**
	 * 设置编辑条件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setEditMap($map) {
		$this->model['edit_map'] = $map;
		return $this;
	}

	/**
	 * 设置数据查询条件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setFindMap($map) {
		$this->model['find_map'] = $map;
		return $this;
	}

	/**
	 * 设置删除条件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setDeleteMap($map) {
		$this->model['delete_map'] = $map;
		return $this;
	}

	/**
	 * 添加表单字段
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addFormField($name, $title, $type, $extra) {
		if (is_array($name)) {
			$this->model['fields'][] = $name;
		} else {
			$field = array(
				'name' => $name,
				'title' => $title,
				'type' => $type
			);
			foreach ($extra as $k => $v) {
				$field[$k] = $v;
			}
			$this->model['fields'][] = $field;
		}
		return $this;
	}

	/**
	 * 添加自动验证规则
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addValidate($field, $rule, $error_tip, $condition, $extra_rule, $validate_time) {
		if (is_array($field)) {
			$this->model['validate'][] = $field;
		} else {
			$validate = array($field, $rule, $error_tip, $condition, $extra_rule, $validate_time);
			$this->model['validate'][] = $validate;
		}
		return $this;
	}

	/**
	 * 添加自动完成规则
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addAuto($field, $rule, $condition, $extra_rule) {
		if (is_array($field)) {
			$this->model['auto'][] = $field;
		} else {
			$auto = array($field, $rule, $condition, $extra_rule);
			$this->model['auto'][] = $auto;
		}
		return $this;
	}

	/**
	 * 设置自动验证
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setValidate($validate) {
		$this->model['validate'] = $validate;
		return $this;
	}

	/**
	 * 设置自动完成
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setAuto($auto) {
		$this->model['auto'] = $auto;
		return $this;
	}

	/**
	 * 设置表单值
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setFormData($data) {
		$this->model['info'] = $data;
		return $this;
	}

	public function setListData($data) {
		$this->model['list_data'] = $data;
		return $this;
	}

	/**
	 * 设置表单字段
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setFormFields($fields) {
		$this->model['fields'] = $fields;
		return $this;
	}

	/**
	 * 设置新增成功后跳转链接
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setAddSuccessUrl($url) {
		$this->model['add_success_url'] = $url;
		return $this;
	}

	/**
	 * 设置编辑成功后跳转链接
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setEditSuccessUrl($url) {
		$this->model['edit_success_url'] = $url;
		return $this;
	}

	/**
	 * 设置删除成功跳转链接
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setDeleteSuccessUrl($url) {
		$this->model['delete_success_url'] = $url;
		return $this;
	}

	/**
	 * 设置新增成功后提示信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setAddSuccessInfo($info) {
		$this->model['add_success_info'] = $info;
		return $this;
	}

	/**
	 * 设置编辑成功后提示信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setEditSuccessInfo($info) {
		$this->model['edit_success_info'] = $info;
		return $this;
	}

	/**
	 * 设置删除成功后提示信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setDeleteSuccessInfo($info) {
		$this->model['delete_success_info'] = $info;
		return $this;
	}

	/**
	 * 添加显示项
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addListItem($name, $title, $format, $extra) {
		if (is_array($name)) {
			$this->model['lists'][] = $name;
		} else {
			$list = array(
				'name' => $name,
				'title' => $title,
				'format' => $format,
				'extra' => $extra
			);
			$this->model['lists'][] = $list;
		}
		return $this;
	}

	/**
	 * 添加面包屑
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addCrumb($title, $url, $class) {
		if (is_array($title)) {
			$this->model['crumb'][] = $title;
		} else {
			$crumb = array(
				'title' => $title,
				'url' => $url,
				'class' => $class
			);
			$this->model['crumb'][] = $crumb;
		}
		return $this;
	}

	/**
	 * 设置面包屑
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setCrumb($crumb) {
		$this->model['crumb'] = $crumb;
		return $this;
	}

	/**
	 * 添加导航
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addNav($title, $url, $class) {
		if (is_array($title)) {			
			$this->model['nav'][] = $title;
		} else {
			$nav = array(
				'title' => $title,
				'url' => $url,
				'class' => $class
			);
			$this->model['nav'][] = $nav;
		}
		return $this;
	}

	/**
	 * 设置导航
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setNav($nav) {
		$this->model['nav'] = $nav;
		return $this;
	}

	/**
	 * 添加子导航
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addSubNav($title, $url, $class) {
		if (is_array($title)) {
			$this->model['subnav'][] = $title;
		} else {
			$subnav = array(
				'title' => $title,
				'url' => $url,
				'class' => $class
			);
			$this->model['subnav'][] = $subnav;
		}
		return $this;
	}

	/**
	 * 设置子导航
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setSubNav($subnav) {
		$this->model['subnav'] = $subnav;
		return $this;
	}

	/**
	 * 设置左侧导航
	 */
	public function setSideNav($sidenav) {
		$this->model['sidenav'] = $sidenav;
		return $this;
	}

	/**
	 * 添加操作按钮
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function addButton($title, $url, $class, $attr) {
		if (is_array($title)) {
			$this->model['btn'][] = $title; 
		} else {
			$btn = array(
				'title' => $title,
				'url' => $url,
				'class' => $class,
				'attr' => $attr
			);
			$this->model['btn'][] = $btn;
		}
		return $this;
	}

	/**
	 * 设置提示信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setTip($tip) {
		$this->model['tip'] = $tip;
		return $this;
	}

	/**
	 * 显示模板
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
		global $_G;
		$this->model['meta_title'] || $this->model['meta_title'] = $this->meta_title;
		$this->model['crumb'] || $this->model['crumb'] = $this->crumb;
		$this->model['nav'] || $this->model['nav'] = $this->nav;
		$this->model['sidenav'] || $this->model['sidenav'] = $this->sidenav;
		$this->model['subnav'] || $this->model['subnav'] = $this->subnav;
		$this->model['btn'] || $this->model['btn'] = $this->btn;
		$this->model['tip'] || $this->model['tip'] = $this->tip;
		$this->model['list_search'] || $this->model['list_search'] = $this->list_search;
		$this->model['submit_type'] || $this->model['submit_type'] = $this->submit_type;

		$this->model['meta_title'] && $this->assign('meta_title', $this->model['meta_title']);
		$this->model['crumb'] && $this->assign('crumb', $this->model['crumb']);
		$this->model['nav'] && $this->assign('nav', $this->model['nav']);
		$this->model['sidenav'] && $this->assign('sidenav', $this->model['sidenav']);
		$this->model['subnav'] && $this->assign('subnav', $this->model['subnav']);
		$this->model['btn'] && $this->assign('btn', $this->model['btn']);
		$this->model['tip'] && $this->assign('tip', $this->model['tip']);
		$this->model['list_search'] && $this->assign('list_search', $this->model['list_search']);
		$this->model['submit_type'] && $this->assign('submit_type', $this->model['submit_type']);
		$this->assign('_G', $_G);
		parent::display($templateFile,$charset,$contentType,$content,$prefix);
	}
}


 ?>