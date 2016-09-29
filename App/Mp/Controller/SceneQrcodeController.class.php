<?php 

namespace Mp\Controller;
use Mp\Controller\BaseController;

/**
 * 场景二维码管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class SceneQrcodeController extends BaseController {

	/**
	 * 场景二维码列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$this->addCrumb('公众号管理', U('Index/index'), '')
			 ->addCrumb('场景二维码', U('SceneQrcode/lists'), '')
			 ->addCrumb('二维码列表', '', 'active')
			 ->addNav('二维码管理', '', 'active')
			 ->addNav('扫码统计', U('statistics'), '')
			 ->addButton('创建二维码', U('add'), 'btn btn-primary')
			 ->setModel('scene_qrcode')
			 ->setListMap(array('mpid'=>get_mpid()))
			 ->setListOrder('ctime desc')
			 ->addListItem('scene_name', '场景名称')
			 ->addListItem('keyword', '关联关键词')
			 ->addListItem('scene_type', '二维码类型', 'enum', array('options'=>array(0=>'临时二维码',1=>'永久二维码')))
			 ->addListItem('expire', '过期时间（秒）', '', array('placeholder'=>'永不过期'))
			 ->addListItem('scene_id', '场景ID', '', array('placeholder'=>'无'))
			 ->addListItem('scene_str', '场景字符串', '', array('placeholder'=>'无'))
			 ->addListItem('short_url', '二维码', 'image', array('attr'=>'width=100,height=100'))
			 ->addListItem('ctime', '二维码创建时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->addListItem('id', '扫码总人数', 'callback', array('callback_name'=>'get_scan_count','params'=>'###'))
			 ->addListItem('id', '扫码总次数', 'callback', array('callback_name'=>'get_scan_times','params'=>'###'))
			 ->addListItem('id', '操作', 'custom', array(
			 	'options' => array(
			 		array(
			 			'title' => '查看扫码结果',
			 			'url' => U('statistics', array('qrcode_id'=>'{id}')),
			 			'class' => 'btn btn-primary btn-sm icon-ul'
			 		)
			 	)
			 ))
			 ->common_lists();
	}

	/**
	 * 获取扫码总人数
	 */
	public function get_scan_count($id) {
		$results = M('scene_qrcode_statistics')->distinct(true)->field('openid')->where(array('mpid'=>get_mpid(),'qrcode_id'=>$id))->select();
		return count($results);
	}

	/**
	 * 获取扫码总次数
	 */
	public function get_scan_times($id) {
		$count = M('scene_qrcode_statistics')->where(array('mpid'=>get_mpid(),'qrcode_id'=>$id))->count();
		return $count;
	}

	/**
	 * 场景二维码类型
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add() {
		if (IS_POST) {
			$data['mpid'] = get_mpid();
			$data['ctime'] = time();
			$data['scene_name'] = $_POST['scene_name'];
			$data['keyword'] = $_POST['keyword'];
			if (!$data['scene_name']) {
				$this->error('场景名称必填');
			}
			if (!$data['keyword']) {
				$this->error('关联关键词必填');
			}
			if ($_POST['scene_type'] == 0) {     // 创建临时二维码
				$data['expire'] = $_POST['expire'] ? $_POST['expire'] : 1800;
				$qrCode = M('scene_qrcode')->where(array('mpid'=>get_mpid(),'scene_type'=>0))->order('scene_id desc')->find();
				if (!$qrCode['scene_id']) {
					$data['scene_id'] = 320001;
				} else {
					$data['scene_id'] = intval($qrCode['scene_id'])+1;
				}
				
				$data['scene_type'] =0;
				$qrCode = get_qr_code($data['scene_id'], 0, $data['expire']);
				if ($qrCode['errcode']) {
					$this->error('未能成功创建二维码，错误信息：'.$qrCode['errmsg']);
				}
				$data['ticket'] = $qrCode['ticket'];
				$data['url'] = $qrCode['url'];
				$data['short_url'] = get_short_url(get_qr_url($qrCode['ticket']));
			} elseif ($_POST['scene_type'] == 1) {		// 创建永久二维码
				if ($_POST['scene_str'] == '') {	// 如果没有填场景值字符串，则系统自动生成场景值ID
					$qrCode = M('scene_qrcode')->where(array('mpid'=>get_mpid(),'scene_type'=>1))->order('scene_id desc')->find();
					if (!$qrCode['scene_id']) {
						$data['scene_id'] = 100001;
					} else {
						$data['scene_id'] = intval($qrCode['scene_id'])+1;
					}
					$qrCode = get_qr_code($data['scene_id'], 1);
				} else {	// 如果填了场景值字符串，则使用场景值字符串
					$data['scene_str'] = $_POST['scene_str'];
					$qrCode = get_qr_code($data['scene_str'], 2);
				}
				$data['scene_type'] = 1;
				if ($qrCode['errcode']) {
					$this->error('未能成功创建二维码，错误信息：'.$qrCode['errmsg']);
				}
				$data['ticket'] = $qrCode['ticket'];
				$data['url'] = $qrCode['url'];
				$data['short_url'] = get_short_url(get_qr_url($qrCode['ticket']));
			} else {
				$this->error('二维码类型有误');
			}
			$res = M('scene_qrcode')->add($data);
			if ($res) {
				$this->success('创建场景二维码成功',U('lists'));
			} else {
				$this->error('创建场景二维码失败');
			}
		} else {
			$this->addCrumb('公众号管理', U('Index/index'), '')
				 ->addCrumb('场景二维码', U('SceneQrcode/lists'), '')
				 ->addCrumb('创建二维码', '', 'active')
				 ->addNav('创建二维码', '', 'active')
				 ->addFormField('scene_name', '场景名称', 'text')
				 ->addFormField('keyword', '关联关键词', 'text', array('tip'=>'扫码带参数二维码时，会触发此关键词'))
				 ->addFormField('scene_type', '二维码类型', 'radio', array('options'=>array(0=>'临时二维码',1=>'永久二维码'),'value'=>1))
				 ->addFormField('scene_str', '场景值字符串', 'text', array('tip'=>'创建临时二维码不需要填此项'))
				 ->addFormField('expire', '二维码过期时间', 'number', array('tip'=>'创建永久二维码不需要填此项，最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。','placeholder'=>'单位：秒'))
				 ->common_add();
		}
		
	}

	/**
	 * 扫码统计
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function statistics() {
		if (I('get.qrcode_id')) {
			$this->setListMap(array('mpid'=>get_mpid(),'qrcode_id'=>I('get.qrcode_id')));
		} else {
			$this->setListMap(array('mpid'=>get_mpid()));
		}
		$this->addCrumb('公众号管理', U('Index/index'), '')
			 ->addCrumb('场景二维码', U('SceneQrcode/lists'), '')
			 ->addCrumb('扫码统计', '', 'active')
			 ->addNav('二维码管理', U('lists'), '')
			 ->addNav('扫码统计', U('statistics'), 'active')
			 ->setModel('scene_qrcode_statistics')
			 ->setListOrder('ctime desc')
			 ->addListItem('openid', '扫码者头像', 'function', array('function_name'=>'get_fans_headimg'))
			 ->addListItem('openid', '扫码者昵称', 'function', array('function_name'=>'get_fans_nickname'))
			 ->addListItem('scene_name', '二维码场景名称')
			 ->addListItem('keyword', '关联关键词')
			 ->addListItem('scan_type', '扫码类型', 'enum', array('options'=>array('subscribe'=>'扫码关注','scan'=>'扫码带参数')))
			 ->addListItem('ctime', '扫码时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->common_lists();
	}

}



 ?>