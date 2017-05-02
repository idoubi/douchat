<?php 
namespace Mp\Controller;
use Mp\Controller\BaseController;

/**
 * 公众号粉丝管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class FansController extends BaseController {

	public function get_fans_lists()
	{
		if(isset($_POST['get']))
		{
			$wechatObj = get_wechat_obj();

			if(isset($_POST['next_openid']))
			{
				// 如果数据库存在最后一次openid 则以这个为标准
				$get_fans_lists = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'get_fans_lists'))->getField('value');
				if(null==$get_fans_lists)
				{
					M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'get_fans_lists'))->setField('get_fans_lists','');
				}
				
				if(null!==$get_fans_lists)
				{
					$we = $wechatObj->getUserList($get_fans_lists);
				}else{
					$we = $wechatObj->getUserList($_POST['next_openid']);
				}
			}
			else
			{
				$we = $wechatObj->getUserList();
			}

			// 如果粉丝小于1万，一次性拉取
			$list_openid = $we['data']['openid'];
			$next_openid = $we['next_openid'];
			if($we['total']<10000)
			{
				foreach ($list_openid as $key => $value) {
					$openid = $list_openid[$key];
					// 拉取成功返回 0
					$mp_fans = D('mp_fans');
					$re = $mp_fans->where("openid = '{$openid}'")->field('id,openid')->find();
					if(null!==$re['id'])
					{
						D('MpFans')->save_fans_info($openid);
					}
				}
				$data['result'] = 0;
				echo json_encode($data);
				exit();
			}
			else
			{
				// 继续拉取返回 1，记录拉取记录和最后一次拉取的openid
				// 返回next_openid
				// 记录当前拉取进度 记录最后一次next_openid
				if(null!==$next_openid)
				{
					foreach ($list_openid as $key => $value) {
						$openid = $list_openid[$key];
						// 拉取成功返回 0
						$mp_fans = D('mp_fans');
						$re = $mp_fans->where("openid = '{$openid}'")->field('id,openid')->find();
						if(!$re['id'])
						{
							D('MpFans')->save_fans_info($openid);
						}
					}

					$data['next_openid'] = $next_openid;
					//保存 next_openid
					M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'get_fans_lists'))->setField('get_fans_lists',$next_openid);

					$data['result'] = 1;
					echo json_encode($data);
					exit();
				}else
				{
					$data['result'] = 0;
					echo json_encode($data);
					exit();
				}
			}
		}
		$this->addCrumb('拉取粉丝', U('Mp/Fans/get_fans_lists'), '')
		     ->common_lists();
		$this->display();
	}

	/**
	 * 粉丝列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$this->addCrumb('公众号管理', U('Mp/Index/index'), '')

			 ->addButton('拉取粉丝', U('Mp/Fans/get_fans_lists'), 'btn btn-primary')

			 ->addCrumb('粉丝管理', U('Mp/Fans/lists'), '')
			 ->addCrumb('粉丝列表', '', 'active')
			 ->addNav('粉丝列表', '', 'active')
			 ->addNav('功能配置', U('Mp/Fans/setting'), '')
			 ->setModel('mp_fans')
			 ->setListMap(array('mpid'=>get_mpid()))
			 ->addListItem('openid', '粉丝OPENID', 'hidden')
			 ->addListItem('headimgurl', '头像', 'image', array('attr'=>'width=50 height=50','placeholder'=>__ROOT__ . '/Public/Admin/img/noname.jpg'))
			 ->addListItem('nickname', '昵称', '', array('placeholder'=>'匿名'))
			 ->addListItem('sex', '性别', 'enum', array('options'=>array(''=>'未知',0=>'未知',1=>'男',2=>'女')))
			 ->addListItem('is_subscribe', '是否关注', 'enum', array('options'=>array(0=>'未关注',1=>'已关注')))
			 ->addListItem('score', '积分')
			 ->addListItem('money', '金钱')
			 ->addListItem('id', '操作', 'custom', array('options'=>array('edit_fans'=>array('编辑粉丝资料', U('Mp/Fans/edit_fans', array('openid'=>'{openid}')),'btn btn-primary btn-sm icon-edit',''))))
		     ->common_lists();
	}
	
	/**
	 * 编辑粉丝信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function edit_fans() {
		$this->addCrumb('公众号管理', U('Mp/Index/index'), '')
			 ->addCrumb('粉丝管理', U('Mp/Fans/lists'), '')
			 ->addCrumb('编辑粉丝信息', '', 'active')
			 ->addNav('编辑粉丝信息', '', 'active')
		     ->setModel('mp_fans')
		     ->addFormField('headimgurl', '用户头像', 'image')
		     ->addFormField('nickname', '昵称', 'text')
		     ->addFormField('relname', '真实姓名', 'text')
		     ->addFormField('sex', '性别', 'radio', array('options'=>array(0=>'未知',1=>'男',2=>'女')))
		     ->addFormField('mobile', '手机号', 'text')
		     ->addFormField('signature', '个性签名', 'textarea')
		     ->setEditMap(array('openid'=>I('get.openid')))
		     ->common_edit();
	}

	/**
	 * 粉丝配置
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function setting() {
		C('TOKEN_ON', false);
		$MpSetting = D('MpSetting');
		if (IS_POST) {
			$settings = I('post.');
			if (!$MpSetting->add_settings($settings)) {
				$this->error('保存设置失败');
			} else {
				$this->success('保存设置成功');
			}
		} else {
			$this->addCrumb('公众号管理', U('Mp/Index/index'), '')
				 ->addCrumb('粉丝管理', U('Mp/Fans/list'))
				 ->addCrumb('功能配置', '', 'active')
				 ->addNav('粉丝列表', U('Mp/Fans/lists'), '')
				 ->addNav('功能配置', '', 'active')
				 ->setModel('mp_setting')
				 ->addFormField('fans_init_integral', '初始化积分', 'number', array('placeholder'=>100,'tip'=>'用户初次关注公众号时赠送的积分'))
				 ->addFormField('fans_init_money', '初始化金钱', 'number', array('placeholder'=>100,'tip'=>'用户初次关注公众号时赠送的金钱'))
				 ->addFormField('fans_bind_on', '是否开启粉丝绑定', 'radio', array('options'=>array(0=>'不开启',1=>'开启'),'value'=>0,'tip'=>'开启粉丝绑定后，对于未认证公众号可以通过粉丝在绑定页面填写的头像、昵称等来获取用户的基本资料'))
				 ->setFormData($MpSetting->get_settings())
				 ->common_edit();
		}
	}

}

?>
