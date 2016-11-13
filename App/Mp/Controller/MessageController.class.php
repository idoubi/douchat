<?php 

namespace Mp\Controller;
use Mp\Controller\BaseController;

/**
 * 公众号消息管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class MessageController extends BaseController {

	/**
	 * 消息列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$custom = array(
			'options' => array(
				'save_to_material' => array(
					'title' => '保存为素材',
					'url' => U('save_to_material', array('msgid'=>'{msgid}')),
					'class' => 'btn btn-sm btn-primary icon-signup'
				),
				'reply_message' => array(
					'title' => '回复消息',
					'url' => U('reply_message', array('msgid'=>'{msgid}')),
					'class' => 'btn btn-sm btn-info icon-topic'
				)
			)
		);
		$this->addCrumb('公众号管理', U('Mp/Index/index'), '')
			 ->addCrumb('消息管理', U('Mp/Message/lists'), '')
			 ->addCrumb('消息列表', '', 'active')
			 ->addNav('消息列表', '', 'active')
			 ->setModel('mp_message')
			 ->setListMap(array('mpid'=>get_mpid()))
			 ->setListOrder('create_time desc')
			 ->setListSearch(array(
			 	'msgtype' => '消息类型',
			 	'content' => '消息内容'
			 ))
			 ->addListItem('msgtype', '消息类型', 'enum', array('options'=>array('text'=>'文本消息','image'=>'图片消息','voice'=>'语音消息','shortvideo'=>'短视频消息','location'=>'地理位置消息','link'=>'链接消息')))
			 ->addListItem('msgid', '消息内容', 'callback', array('callback_name'=>'get_message_content'))
			 ->addListItem('create_time', '消息发送时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->addListItem('openid', '粉丝头像', 'function', array('function_name'=>'get_fans_headimg'))
			 ->addListItem('openid', '粉丝昵称', 'function', array('function_name'=>'get_fans_nickname'))
			 ->addListItem('msgid', '操作', 'custom', $custom)
			 ->common_lists();
	}

	/**
	 * 保存为素材
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function save_to_material() {
		$msgid = I('get.msgid');
		$message = M('mp_message')->where(array('mpid'=>get_mpid(),'msgid'=>$msgid))->find();
		if (!$message) {
			$this->error('消息不存在');
		} elseif ($message['save_status'] == 1) {
			$this->error('该消息已保存为素材');
		} else {
			$msgtype = $message['msgtype'];
			switch ($msgtype) {
				case 'text':
					$insert['content'] = $message['content'];
					break;
				
				default:
					$this->error('此类型消息暂时不支持保存为素材');
					break;
			}
			$insert['mpid'] = get_mpid();
			$insert['type'] = $msgtype;
			$insert['create_time'] = time();
			if (!M('mp_material')->add($insert)) {
				$this->error('保存素材失败');
			} else {
				M('mp_message')->where(array('mpid'=>get_mpid(),'msgid'=>$msgid))->setField('save_status',1);
				$this->success('保存素材成功', U('lists'));
			}
		}
	}

	/**
	 * 回复消息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function reply_message() {
		if (IS_POST) {
			$data = I('post.');
			$content = $data['content'];
			if (!$content) {
				$this->error('请填写回复内容');
			} else {
				$reply = array(
					'touser' => I('openid'),
					'msgtype' => 'text',
					'text' => array(
						'content' => I('content')
					)
				);
				$result = send_custom_message($reply);
				if ($result['errcode'] != 0) {
					$this->error($result['errmsg']);
				} else {
					M('mp_message')->where(array('mpid'=>get_mpid(),'msgid'=>I('msgid')))->setField('reply_status', 1);
					$this->success('回复成功', U('lists'));
				}
			}
		} else {
			$message = M('mp_message')->where(array('mpid'=>get_mpid(),'msgid'=>I('get.msgid')))->find();
			if (!$message) {
				$this->error('消息不存在');
			} elseif (time()-$message['create_time'] > 48*3600) {
				$this->error('该消息发送时间距离此刻已超过48小时，不能回复');
			} else {
				$this->addCrumb('公众号管理', U('Index/index'), '')
					 ->addCrumb('消息管理', U('Message/lists'), '')
					 ->addCrumb('回复消息', '', 'active')
					 ->addNav('回复消息', '', 'active')
					 ->addFormField('content', '回复内容', 'textarea')
					 ->addFormField('msgid', '消息ID', 'hidden', array('value'=>$message['msgid']))
					 ->addFormField('openid', '粉丝openid', 'hidden', array('value'=>$message['openid']))
					 ->common_add();
			}
		}
	}

	/**
	 * 获取消息内容
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_message_content($msgid) {
		$map['msgid'] = $msgid;
		$map['mpid'] = get_mpid();
		$message = M('mp_message')->where($map)->find();
		if (!$message) {
			return '';
		}
		switch ($message['msgtype']) {
			case 'text':
				return $message['content'];
				break;
			case 'image':
				// 感谢 @  平凡<58000865@qq.com> 提供的微信图片防盗链解决方案
            	return '<img src="'.$message['picurl'].'" width="100" height="100" />';      
				break;
			case 'voice':
				return '【语音】';
				break;
			case 'shortvideo':		
				return '【视频】';
				break;
			case 'location':
				return '【位置】'.$message['label'];
				break;
			case 'link':
				return '【链接】<a style="color:#08a5e0" href="'.$message['url'].'" target="_blank">'.$message['title'].'</a>';
				break;
			default:
				return '';
				break;
		}
	}

}



 ?>