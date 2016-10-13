<?php 

namespace Mp\Controller;
use Mp\Controller\BaseController;

/**
 * 自动回复控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class AutoReplyController extends BaseController {
	
	/**
	 * 关键词回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function keyword() {
		$options = array(
			'add' => array(
				'title' => '编辑',
				'url' => U('edit', array('id'=>'{id}')),
				'class' => 'btn btn-primary btn-sm icon-edit'
			),
			'delete' =>	array(
				'title' => '删除',
				'url' => U('delete', array('id'=>'{id}')),
				'class' => 'btn btn-danger btn-sm icon-delete'
			)
		);
		$this->addCrumb('公众号管理', U('Mp/Index/index'), '')
			 ->addCrumb('自动回复', U('Mp/AutoReply/keyword'), '')
			 ->addCrumb('关键词回复', '', 'active')
			 ->addNav('关键词回复', '', 'active')
			 ->addNav('特殊消息回复', U('special'), '')
			 ->addNav('事件回复', U('event'), '')
			 ->addNav('未识别回复', U('unrecognize'), '')
			 ->addButton('添加文本回复', U('add?type=text'), 'btn btn-primary')
			 ->addButton('添加图片回复', U('add?type=image'), 'btn btn-info')
			 ->addButton('添加图文回复', U('add?type=news'), 'btn btn-success')
			 ->setModel('mp_auto_reply')
			 ->setListMap(array('mpid'=>get_mpid(),'type'=>'keyword'))
			 ->setListOrder('id desc')
			 ->addListItem('id', '关键词', 'callback', array('callback_name'=>'get_keyword'))
			 ->addListItem('reply_type', '回复类型', 'enum', array('options'=>array('text'=>'文本','image'=>'图片','news'=>'图文')))
			 ->addListItem('material_id', '回复内容', 'callback', array('callback_name'=>'get_reply_content'))
			 ->addListItem('id', '操作', 'custom', array('options'=>$options))
			 ->common_lists();
	}

	/**
	 * 添加文本回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add() {
		if (IS_POST) {
			$data = I('post.');
			$type = $data['reply_type'];
			$result = D('MpAutoReply')->add_auto_reply($type, $data);
			if ($result['errcode'] != 0) {
				$this->error($result['errmsg']);
			} else {
				$this->success($result['errmsg'], U('keyword'));
			}
		} else {
			$type = I('get.type');
			$type_arr = array('text'=>'文本','image'=>'图片','news'=>'图文');
			$this->addCrumb('公众号管理', U('Index/index'), '')
				 ->addCrumb('关键词回复', U('AutoReply/keyword'), '')
				 ->addCrumb('添加'.$type_arr[$type].'回复', '', '')
				 ->addNav('添加'.$type_arr[$type].'回复', '', 'active')
				 ->addFormField('keyword', '关键词', 'text')
				 ->addFormField('reply_type', '回复类型', 'hidden', array('value'=>$type));
			switch ($type) {
				case 'text':
					$this->addFormField('content', '文本内容', 'textarea');
					break;
				case 'image':
					$this->addFormField('image', '回复图片', 'image');
					break;
				case 'news':
					$this->addFormField('title', '图文标题', 'text')
					     ->addFormField('picurl', '图文封面', 'image')
					     ->addFormField('description', '图文描述', 'textarea')
					     ->addFormField('url', '图文链接', 'text');
					break;
				default:
					# code...
					break;
			}	 
			$this->common_add();
		}
	}

	/**
	 * 编辑自动回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function edit() {
		if (IS_POST) {
			$data = I('post.');
			$type = $data['reply_type'];
			$result = D('MpAutoReply')->edit_auto_reply($type, $data);
			if ($result['errcode'] != 0) {
				$this->error($result['errmsg']);
			} else {
				$this->success($result['errmsg'], U('keyword'));
			}
		} else {
			$result = D('MpAutoReply')->get_auto_reply(I('get.id'));
			if ($result['errcode'] != 0) {
				$this->error($result['errmsg']);
			}
			$form_data = $result['result'];
			$type = $form_data['reply_type'];
			$type_arr = array('text'=>'文本','image'=>'图片','news'=>'图文');
			$this->addCrumb('公众号管理', U('Index/index'), '')
				 ->addCrumb('关键词回复', U('AutoReply/keyword'), '')
				 ->addCrumb('编辑'.$type_arr[$type].'回复', '', 'active')
				 ->addNav('编辑'.$type_arr[$type].'回复', '', 'active')
				 ->addFormField('keyword', '关键词', 'text', array('attr'=>'readonly'))
				 ->addFormField('reply_id', '回复规则ID', 'hidden')
				 ->addFormField('material_id', '素材ID', 'hidden')
				 ->addFormField('rule_id', '关键词触发规则ID', 'hidden')
				 ->addFormField('reply_type', '回复类型', 'hidden')
				 ->setFormData($form_data);
			switch ($type) {
				case 'text':
					$this->addFormField('content', '文本内容', 'textarea');
					break;
				case 'image':
					$this->addFormField('image', '回复图片', 'image');
					break;
				case 'news':
					$this->addFormField('title', '图文标题', 'text')
					     ->addFormField('picurl', '图文封面', 'image')
					     ->addFormField('description', '图文描述', 'textarea')
					     ->addFormField('url', '图文链接', 'text');
					break;
				default:
					# code...
					break;
			}
			$this->common_edit();
		}
	}

	/**
	 * 获取自动回复关键词
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_keyword($reply_id) {
		$reply_rule = D('MpRule')->get_auto_reply_rule($reply_id);
		return $reply_rule['keyword'];
	}

	/**
	 * 获取回复内容
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_reply_content($material_id) {
		return D('MpMaterial')->get_material($material_id);
	}

	/**
	 * 删除关键词回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function delete() {
		$result = D('MpAutoReply')->get_auto_reply(I('get.id'));
		if ($result['errcode'] != 0) {
			$this->error($result['errmsg']);
		} else {
			$data = $result['result'];
			$type = $data['reply_type'];
			unset($result);
			$result = D('MpAutoReply')->delete_auto_reply($type, $data);
			if ($result['errcode'] != 0) {
				$this->error($result['errmsg']);
			} else {
				$this->success($result['errmsg']);
			}
		}
	}

	/**
	 * 非关键词回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function special() {
		if (IS_POST) {
			C('TOKEN_ON', false);
			if (!I('type') || count(I('type')) == 0) {
				$this->error('无法设置非关键词回复');
			}
			$types = I('type');
			$AutoReply = D('MpAutoReply');
			$data['mpid'] = get_mpid();
			foreach ($types as $k => $v) {
				$data['type'] = $v;
				$data['reply_type'] = I($v);
				$data['keyword'] = I($v.'_keyword');
				$data['addon'] = I($v.'_addon');
				if (!$AutoReply->create($data)) {
					$this->error($AutoReply->getError());
				} else {
					$res = $AutoReply->get_auto_reply_by_type($v);
					if ($res) {
						$data['id'] = $res['id'];
						$AutoReply->save($data);
					} else {
						unset($data['id']);
						$AutoReply->add($data);
					}
				}
			}
			
			$this->success('保存特殊消息回复成功');
		} else {
			$AutoReply = D('MpAutoReply');
			$show = array(
				array(
					'name' => 'image',
					'title' => '图片消息',
					'value' => $AutoReply->get_auto_reply_by_type('image')
				),
				array(
					'name' => 'voice',
					'title' => '语音消息',
					'value' => $AutoReply->get_auto_reply_by_type('voice')
				),
				array(
					'name' => 'shortvideo',
					'title' => '短视频消息',
					'value' => $AutoReply->get_auto_reply_by_type('shortvideo')
				),
				array(
					'name' => 'location',
					'title' => '位置消息',
					'value' => $AutoReply->get_auto_reply_by_type('location')
				),
				array(
					'name' => 'link',
					'title' => '链接消息',
					'value' => $AutoReply->get_auto_reply_by_type('link')
				),
			);
			$this->assign('show', $show);
			$addons = D('Addons')->get_installed_addons();
			$this->assign('addons', $addons);
			$crumb = array(
				array(
					'title' => '公众号管理',
					'url' => U('Index/index'),
					'class' => ''
				),
				array(
					'title' => '自动回复',
					'url' => U('AutoReply/keyword'),
					'class' => ''
				),
				array(
					'title' => '特殊消息回复',
					'url' => '',
					'class' => 'active'
				)
			);
			$nav = array(
				array(
					'title' => '关键词回复',
					'url' => U('keyword'),
					'class' => ''
				),
				array(
					'title' => '特殊消息回复',
					'url' => U('special'),
					'class' => 'active'
				),
				array(
					'title' => '事件回复',
					'url' => U('event'),
					'class' => ''
				),
				array(
					'title' => '未识别回复',
					'url' => U('unrecognize'),
					'class' => ''
				)
			);
			$tip = '当用户在公众号发送以下几种类型消息时，如果选择了响应插件，系统会把消息分发到指定的插件进行处理。如果绑定了关键词，系统会根据关键词回复中设置的内容直接回复。';
			$this->assign('crumb', $crumb);
			$this->assign('nav', $nav);
			$this->assign('tip', $tip);
			$this->display();
		}
	}

	/**
	 * 事件回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function event() {
		if (IS_POST) {
			C('TOKEN_ON', false);
			if (!I('type') || count(I('type')) == 0) {
				$this->error('无法设置非关键词回复');
			}
			$types = I('type');
			$AutoReply = D('MpAutoReply');
			$data['mpid'] = get_mpid();
			foreach ($types as $k => $v) {
				$data['type'] = $v;
				$data['reply_type'] = I($v);
				$data['keyword'] = I($v.'_keyword');
				$data['addon'] = I($v.'_addon');
				if (!$AutoReply->create($data)) {
					$this->error($AutoReply->getError());
				} else {
					$res = $AutoReply->get_auto_reply_by_type($v);
					if ($res) {
						$data['id'] = $res['id'];
						$AutoReply->save($data);
					} else {
						unset($data['id']);
						$AutoReply->add($data);
					}
				}
			}
			
			$this->success('保存事件回复成功');
		} else {
			$AutoReply = D('MpAutoReply');
			$show = array(
				array(
					'name' => 'subscribe',
					'title' => '用户关注',
					'value' => $AutoReply->get_auto_reply_by_type('subscribe')
				),
				array(
					'name' => 'unsubscribe',
					'title' => '用户取消关注',
					'value' => $AutoReply->get_auto_reply_by_type('unsubscribe'),
					'tip' => '用户取消关注，自动回复内容不生效。可以为用户取消关注事件指定一个插件进行响应，从而进行诸如减少积分之类的操作。'
				),
				array(
					'name' => 'scan',
					'title' => '扫描二维码',
					'value' => $AutoReply->get_auto_reply_by_type('scan')
				),
				array(
					'name' => 'report_location',
					'title' => '上报地理位置',
					'value' => $AutoReply->get_auto_reply_by_type('report_location')
				),
				array(
					'name' => 'click',
					'title' => '点击菜单拉取消息',
					'value' => $AutoReply->get_auto_reply_by_type('click'),
					'tip' => '用户点击菜单时，默认会根据菜单推送的KEY值响应对应的关键词。此处的设置会把用户点击菜单拉取消息事件分发到指定的插件进行响应'
				),
			);
			$this->assign('show', $show);
			$addons = D('Addons')->get_installed_addons();
			$this->assign('addons', $addons);
			$crumb = array(
				array(
					'title' => '公众号管理',
					'url' => U('Index/index'),
					'class' => ''
				),
				array(
					'title' => '自动回复',
					'url' => U('AutoReply/keyword'),
					'class' => ''
				),
				array(
					'title' => '事件回复',
					'url' => '',
					'class' => 'active'
				)
			);
			$nav = array(
				array(
					'title' => '关键词回复',
					'url' => U('keyword'),
					'class' => ''
				),
				array(
					'title' => '特殊消息回复',
					'url' => U('special'),
					'class' => ''
				),
				array(
					'title' => '事件回复',
					'url' => U('event'),
					'class' => 'active'
				),
				array(
					'title' => '未识别回复',
					'url' => U('unrecognize'),
					'class' => ''
				)
			);
			$tip = '当用户在公众号触发以下几种类型事件时，如果选择了响应插件，系统会把消息分发到指定的插件进行处理。如果绑定了关键词，系统会根据关键词回复中设置的内容直接回复。';
			$this->assign('tip', $tip);
			$this->assign('crumb', $crumb);
			$this->assign('nav', $nav);
			$this->display('special');
		}
	}

	// 未识别回复
	public function unrecognize() {
		if (IS_POST) {
			C('TOKEN_ON', false);
			if (!I('type') || count(I('type')) == 0) {
				$this->error('无法设置非关键词回复');
			}
			$types = I('type');
			$AutoReply = D('MpAutoReply');
			$data['mpid'] = get_mpid();
			foreach ($types as $k => $v) {
				$data['type'] = $v;
				$data['reply_type'] = I($v);
				$data['keyword'] = I($v.'_keyword');
				$data['addon'] = I($v.'_addon');
				if (!$AutoReply->create($data)) {
					$this->error($AutoReply->getError());
				} else {
					$res = $AutoReply->get_auto_reply_by_type($v);
					if ($res) {
						$data['id'] = $res['id'];
						$AutoReply->save($data);
					} else {
						unset($data['id']);
						$AutoReply->add($data);
					}
				}
			}
			
			$this->success('保存未识别回复成功');
		} else {
			$AutoReply = D('MpAutoReply');
			$show = array(
				array(
					'name' => 'unrecognize',
					'title' => '未识别回复',
					'value' => $AutoReply->get_auto_reply_by_type('unrecognize')
				)
			);
			$this->assign('show', $show);
			$addons = D('Addons')->get_installed_addons();
			$this->assign('addons', $addons);
			$crumb = array(
				array(
					'title' => '公众号管理',
					'url' => U('Index/index'),
					'class' => ''
				),
				array(
					'title' => '自动回复',
					'url' => U('AutoReply/keyword'),
					'class' => ''
				),
				array(
					'title' => '未识别回复',
					'url' => '',
					'class' => 'active'
				)
			);
			$nav = array(
				array(
					'title' => '关键词回复',
					'url' => U('keyword'),
					'class' => ''
				),
				array(
					'title' => '特殊消息回复',
					'url' => U('special'),
					'class' => ''
				),
				array(
					'title' => '事件回复',
					'url' => U('event'),
					'class' => ''
				),
				array(
					'title' => '未识别回复',
					'url' => U('unrecognize'),
					'class' => 'active'
				)
			);
			$tip = '当用户在公众号发送的消息未触发关键词回复、特殊消息回复、事件回复几种回复规则时，如果有设置未识别回复规则，则按此处设置的规则进行回复。';
			$this->assign('tip', $tip);
			$this->assign('crumb', $crumb);
			$this->assign('nav', $nav);
			$this->display();
		}
	}

}

 ?>