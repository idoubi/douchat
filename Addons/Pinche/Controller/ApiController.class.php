<?php

/**
 * 微拼车接口控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Addons\pinche\Controller;

use Mp\Controller\ApiBaseController;

class ApiController extends ApiBaseController {

    public $wxaFansModel;
    public $infoModel;
    public $appointmentModel;
    public $favModel;
    public $noticeModel;
    public $commentModel;
    public $zanModel;
	
	public function __construct() {
		parent::__construct();
		$this->WxaFansModel = D('Mp/WxaFans');
		$this->infoModel = D('Addons://Pinche/Info');
		$this->appointmentModel = D('Addons://Pinche/Appointment');
		$this->favModel = D('Addons://Pinche/Fav');
		$this->noticeModel = D('Addons://Pinche/Notice');
		$this->commentModel = D('Addons://Pinche/Comment');
		$this->zanModel = D('Addons://Pinche/Zan');
	}
	
	/**
	 * 获取拼车列表
	 */
	public function getInfoList() {
		$page = I('page', 1, 'intval');
		$per = 20;
		
		$start = I('start', '');	// 出发地点
		$over = I('over', '');		// 目的地
		$date = I('date', '');		// 出发时间
		
		$map['mpid'] = $this->mpid;
		$map['status'] = 1;
//		$map['time'] = ['egt', time()];
		if (!empty($date)) {
			$map['date'] = ['lt', $date];
		}
		if (!empty($start)) {
			$map['departure'] = ['like', "%{$start}%"];
		}
		if (!empty($over)) {
			$map['destination'] = ['like', "%{$over}%"];
		}
		
		$data = M('pinche_info')->where($map)->page($page, $per)->order('time desc')->select();
		foreach ($data as &$v) {
			$v['user'] = $this->WxaFansModel->getByOpenid($v['openid']);
		}
		
		$this->response(0, '获取成功', $data);
	}

	/**
     * 获取拼车详情
     */
    public function getInfoDetail(){
        $id = I('iid', 0, 'intval');
        $data = $this->infoModel->getById($id);
        if (empty($data)) {
            $this->response(1001,  '没有找到该信息');
        }
        
        $data['user'] = $this->WxaFansModel->getByOpenid($data['openid']);
        $this->responseOk($data);
    }
	
	/**
	 * 发布拼车信息
	 */
	public function addInfo() {
		$this->checkLogin();
		
		if (IS_POST) {
			$_POST['time'] = strtotime(I('date').I('time'));
			$_POST['mpid'] = $this->mpid;
			$_POST['openid'] = $this->openid;
			if ($this->infoModel->create()) {
				$id = $this->infoModel->add();
				if ($id) {
					$this->response(0, '发布成功', $id);
				}
			}
		}
		
		$this->response(1001, '发布失败');
	}

    /**
     * 检测是否已收藏
     */
    public function checkFav() {
        $this->checkLogin();
        
        if ($this->favModel->where([
        	'mpid'=>get_mpid(),
			'openid'=>get_openid(),
			'iid'=>I('iid')
		])->find()) {
            $this->response(0, '已收藏');
        }
        
        $this->response(1001, '未收藏');
    }
	
	/**
	 * 添加收藏
	 */
	public function addFav() {
		$this->checkLogin();
		if (!$this->favModel->where([
			'mpid' => $this->mpid,
			'openid' => $this->openid,
			'iid' => I('iid')
		])->find()) {
			$res = $this->favModel->add([
				'mpid' => $this->mpid,
				'openid' => $this->openid,
				'iid' => I('iid'),
				'time' => time()
			]);
			if (!$res) {
				$this->response(1001, '收藏失败');
			}
		}
		
		$this->response(0, '收藏成功');
	}
	
	/**
	 * 取消收藏
	 */
	public function deleteFav() {
		$this->checkLogin();
		
		if ($this->favModel->where([
			'mpid' => $this->mpid,
			'openid' => $this->openid,
			'iid' => I('iid')
		])->find()) {
			$res = $this->favModel->where([
				'mpid' => $this->mpid,
				'openid' => $this->openid,
				'iid' => I('iid')
			])->delete();
			if (!$res) {
				$this->response(1001, '取消收藏失败');
			}
			
		}
		$this->response(0, '取消收藏成功');
	}

    /**
     * 获取评论总数
     */
    public function getCommentCount() {
        $count = $this->commentModel->where([
            'mpid' => $this->mpid,
            'type' => I('type'),
            'iid' => I('iid')
        ])->count();
        
        $this->response(0, '获取成功', $count);
    }

    /**
     * 获取评论列表
     */
    public function getCommentList() {
        $page = I('page',1, 'intval');
        $per = 20;
        $data = $this->commentModel->where([
           'mpid' => $this->mpid,
           'type' => I('type'),
           'iid' => I('iid')
        ])->page($page, $per)->order('time desc')->select();
        
        foreach ($data as &$v) {
            $v['user'] = $this->WxaFansModel->getByOpenid($v['openid']);
        }
        
        $this->response(0, '获取成功', $data);
    }
	
	/**
	 * 评论点赞
	 */
	public function addCommentZan() {
		$this->checkLogin();
		
		if (IS_POST) {
			$cid = I('cid', 0, 'intval');
			if ($this->commentModel->where(['mpid'=>$this->mpid, 'id'=>$cid])->find()) {
				if (!$this->zanModel->where(['mpid'=>$this->mpid, 'openid'=>$this->openid, 'cid'=>$cid])->find()) {
					$zid = $this->zanModel->add([
						'mpid' => $this->mpid,
						'openid' => $this->openid,
						'cid' => $cid,
						'time' => time()
					]);
					if ($zid) {
						$this->commentModel->where(['mpid'=>$this->mpid, 'id'=>$cid])->setInc('zan');
						$this->response(0, '点赞成功', $this->commentModel->where(['mpid'=>$this->mpid, 'id'=>$cid])->getField('zan'));
					}
				}
			}
		}
		
		$this->response(1001, '点赞失败');
	}

    /**
     * 添加预约
     */
    public function addAppointment() {
        $this->checkLogin();

        $iid = I('iid', 0, 'intval');
        $info = $this->infoModel->getById($iid);
        if (empty($info)) {
            $this->response(1001, '拼车信息不存在');
        }

        $data['mpid'] = $this->mpid;
        $data['openid'] = $this->openid;
        $data['iid'] = $iid;
        $data['name'] = I('name','');
        $data['phone'] = I('phone','');
        $data['surplus'] = I('surplus','');
        $data['time'] = time();

        $rules = array(
            array('name','require','请输入姓名'),
            array('phone','require','请输入手机号码'),
            array('phone','/^1[34578]\d{9}$/','手机号码格式错误'),
            array('surplus','require','请选择人数')
        );

        $where['mpid'] = $this->mpid;
        $where['openid'] = $this->openid;
        $where['iid'] = $data['iid'];
        $appointment = M('pinche_appointment')->where($where)->find();
        if(!empty($appointment)){
            $this->response(1001, '请不要重复预约');
        }

        if ($id = M('pinche_appointment')->validate($rules)->add($data)) {
        	// TODO：发送模板消息通知车主
//            $postData['touser'] = $info['openId'];
//            $postData['template_id'] = 'l5gcjhy3C_Tu-mjhoCNHOrbW4P7xlRw72dzu3iZ5tVw';
//            $postData['page'] = '/pages/appointment/index?id='.$id;
//            $postData['form_id'] = i('form_id');
//            $postData['data']['keyword1']['value'] = $data['name'];
//            $postData['data']['keyword2']['value'] = $data['phone'];
//            $postData['data']['keyword3']['value'] = $info['destination'];
//            $postData['data']['keyword4']['value'] = $info['departure'];
//            $postData['data']['keyword5']['value'] = date('Y-m-d H:i',$info['time']);
//            sendMessage($postData);
//            msg('notice',$info['uid'],'10000',$data['name'].'预约了您发布的拼车信息,请及时处理',$postData['page']);
            $this->response(0, '预约成功');
        }

        $this->response(1001, '预约失败');
    }

    /**
     * 获取通知列表
     */
    public function getNoticeList() {
        $data = $this->noticeModel->get();
        $this->response(0, '获取成功', $data);
    }

    /**
     * 获取通知详情
     */
    public function getNoticeDetail() {
        $id = I('id', 0, 'intval');
        $data = $this->noticeModel->getById($id);
        $this->response(0, '获取成功', $data);
    }
	
	/**
	 * 我发布的拼车数
	 */
	public function getMyInfoCount() {
		$this->checkLogin();
		$data = $this->infoModel->getCountByOpenid($this->openid);
		$this->response(0, '获取成功', $data);
	}
	
	/**
	 * 我发布的拼车
	 */
	public function getMyInfoList() {
		$this->checkLogin();
		$page = I('page', 1, 'intval');
		$data = $this->infoModel->getByOpenid($this->openid, $page);
		$this->response(0, '获取成功', $data);
	}
	
	/**
	 * 我的预约数
	 */
	public function getMyAppointmentCount() {
		$this->checkLogin();
		
		$data = $this->appointmentModel->where([
			'mpid'=>$this->mpid, 'openid'=>$this->openid
		])->count();
		
		$this->response(0, '获取成功', $data);
	}
	
	/**
	 * 我的预约
	 */
	public function getMyAppointmentList() {
		$this->checkLogin();
		
		$data = $this->appointmentModel->where([
			'mpid' => $this->openid,
			'openid' => $this->openid
		])->select();
		foreach ($data as &$v) {
			$v['user'] = $this->WxaFansModel->getByOpenid($v['openid']);
			$v['info'] = $this->infoModel->where([
				'mpid' => $this->mpid,
				'id' => $v['iid']
			])->find();
		}
		
		$this->response(0, '获取成功', $data);
	}
	
	/**
	 * 我的预约乘客
	 */
	public function getMyPassengerList() {
		$this->checkLogin();
		
		$infoList = $this->infoModel->where([
			'mpid' => $this->mpid,
			'openid' => $this->openid
		])->field('id')->select();
		$data = [];
		foreach ($infoList as $v) {
			$appointment = $this->appointmentModel->where([
				'mpid' => $this->mpid,
				'iid' => $v['id']
			])->find();
			if (!empty($appointment)) {
				$appointment['user'] = $this->WxaFansModel->getByOpenid($appointment['openid']);
				$appointment['info'] = $this->infoModel->where([
					'mpid' => $this->mpid,
					'id' => $appointment['iid']
				])->find();
				$data[] = $appointment;
			}
		}
		
		$this->response(0, '获取成功', $data);
	}

    /**
	 * 我的收藏
	 */
    public function getMyFavList() {
    	$this->checkLogin();
    	
    	$page = I('page', 1, 'intval');
    	$fav = $this->favModel->getByOpenid($this->openid, $page);
    	$data = [];
    	foreach ($fav as $v) {
    		$info = $this->infoModel->getById($v['iid']);
    		if (!empty($info)) {
    			$info['fid'] = $v['id'];
    			$data[] = $info;
			}
		}
		
		$this->response(0, '获取成功', $data);
	}
}