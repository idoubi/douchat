<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 引入JSSDK行为类
 * @author 艾逗笔<765532665@qq.com>
 */
class JssdkBehavior extends Behavior {

	public function run(&$params) {
		$debug = $params;
		$signPackage = get_jssdk_sign_package();
	    return "<script>
	    var JSON_PAY = '".U('Mp/MobileBase/json_pay')."';
	    var API_CALL = '".U('Mp/ApiBase/commonRequest')."';
	    </script>
	    <script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
	    <script>
	    wx.config({
	        debug: '".$debug."',
	        appId: '".$signPackage["appId"]."',
	        timestamp: '".$signPackage["timestamp"]."',
	        nonceStr: '".$signPackage["nonceStr"]."',
	        signature: '".$signPackage["signature"]."',
	        jsApiList: [
	        'checkJsApi',
	            'onMenuShareTimeline',
	            'onMenuShareAppMessage',
	            'onMenuShareQQ',
	            'onMenuShareWeibo',
	            'hideMenuItems',
	            'showMenuItems',
	            'hideAllNonBaseMenuItem',
	            'showAllNonBaseMenuItem',
	            'translateVoice',
	            'startRecord',
	            'stopRecord',
	            'onRecordEnd',
	            'playVoice',
	            'pauseVoice',
	            'stopVoice',
	            'uploadVoice',
	            'downloadVoice',
	            'chooseImage',
	            'previewImage',
	            'uploadImage',
	            'downloadImage',
	            'getNetworkType',
	            'openLocation',
	            'getLocation',
	            'hideOptionMenu',
	            'showOptionMenu',
	            'closeWindow',
	            'scanQRCode',
	            'chooseWXPay',
	            'openProductSpecificView',
	            'addCard',
	            'chooseCard',
	            'openCard'
	        ]
	      });
		</script>
		<script src='/Public/Common/js/jssdk.js'></script>
		";
	}
}

 ?>