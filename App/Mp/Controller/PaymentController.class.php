<?php 

namespace Mp\Controller;
use Mp\Controller\BaseController;

/**
 * 公众号支付控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class PaymentController extends BaseController {

	/**
	 * 微信支付
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function wechat() {
		global $_G;
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
			$mp_info = get_mp_info();
			$this->addCrumb('公众号管理', U('Mp/Index/index'), '')
				 ->addCrumb('微信支付', U('Mp/Payment/wechat'), '')
				 ->addCrumb('支付配置', '', 'active')
				 ->addNav('支付配置', '', 'active')
				 ->addNav('支付记录', U('record'), '')
				 ->setTip('使用微信支付前你需要配置支付授权目录：'.$_G['site_url'])
				 ->setModel('mp_payment')
				 ->addFormField('appid', '公众号APPID', 'text', array('value'=>$mp_info['appid']))
				 ->addFormField('appsecret', '公众号APPSECRET', 'text', array('value'=>$mp_info['appsecret']))
				 ->addFormField('mchid', '微信商户号', 'text')
				 ->addFormField('paysignkey', '微信支付秘钥', 'text')
				 ->addFormField('sslcert', '支付证书cert', 'textarea', array('tip'=>'请在微信商户后台下载支付证书，用记事本打开apiclient_cert.pem，并复制里面的内容粘贴到这里'))
				 ->addFormField('sslkey', '支付证书key', 'textarea', array('tip'=>'请在微信商户后台下载支付证书，用记事本打开apiclient_key.pem，并复制里面的内容粘贴到这里'))
				 ->setFormData($MpSetting->get_settings())
				 ->common_edit();
		}
	}

	/**
	 * 支付记录
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function record() {
		$this->setMetaTitle('交易记录')
             ->addCrumb('公众号管理', U('Index/index'), '')
			 ->addCrumb('微信支付', U('Payment/wechat'), '')
			 ->addCrumb('支付记录', '', 'active')
			 ->addNav('支付配置', U('Payment/wechat'), '')
			 ->addNav('支付记录', '', 'active')
			 ->setModel('mp_payment')
			 ->setListOrder('create_time desc')
             ->setListMap(array('mpid'=>get_mpid()))
			 ->addListItem('orderid', '商户订单号')
			 ->addListItem('id', '微信支付订单号', 'callback', array('callback_name'=>'get_transaction_id','params'=>'###'))
			 ->addListItem('create_time', '支付时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->addListItem('id', '支付金额（元）', 'callback', array('callback_name'=>'get_total_fee','params'=>'###'))
			 ->addListItem('id', '支付方式', 'callback', array('callback_name'=>'get_trade_type','params'=>'###'))
			 ->addListItem('id', '交易结果', 'callback', array('callback_name'=>'get_result_code','params'=>'###'))
			 ->addListItem('openid', '支付者头像', 'function', array('function_name'=>'get_fans_headimg'))
			 ->addListItem('openid', '支付者昵称', 'function', array('function_name'=>'get_fans_nickname'))
			 ->common_lists();
	}

	/**
	 * 获取支付信息详情
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_payment_detail($id, $field = '') {
		$payment = M('mp_payment')->find($id);
		$detail = json_decode($payment['detail'], true);
		return $field ? $detail[$field] : $detail;
	} 

	/**
	 * 获取支付金额
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_total_fee($id) {
		$total_fee = $this->get_payment_detail($id, 'total_fee');
		return floatval($total_fee)/100;
	}

	/**
	 * 获取微信支付订单号
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_transaction_id($id) {
        $detail = $this->get_payment_detail($id);
        if (isset($detail['transaction_id'])) {                 // JSAPI支付
            return $detail['transaction_id'];       
        } elseif (isset($detail['payment_no'])) {         // 企业付款
            return $detail['payment_no'];
        } elseif (isset($detail['send_listid'])) {               // 现金红包
            return $detail['send_listid'];           
        } else {
            return '';
        }
	}

	/**
	 * 获取支付方式
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_trade_type($id) {
		$detail = $this->get_payment_detail($id);
        if (isset($detail['transaction_id'])) {                 // JSAPI支付
            return $detail['trade_type'];       
        } elseif (isset($detail['payment_no'])) {         // 企业付款
            return '企业付款';
        } elseif (isset($detail['send_listid'])) {               // 现金红包
            return '现金红包';           
        } else {
            return '';
        }
	}

	/**
	 * 获取交易结果
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_result_code($id) {
		return $this->get_payment_detail($id, 'result_code');
	}

}

?>