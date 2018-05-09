<?php

/**
 * 小程序SDK
 * @author 艾逗笔<http://idoubi.cc>
 */

namespace WechatSdk;

class Wxapp {
	const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com'; //以下API接口URL需要使用此前缀
	const SNS_SESSION_KEY = '/sns/jscode2session?';
	
	private $appid;
	private $appsecret;
	public $errCode = 40001;
	public $errMsg = "no access";
	
	public function __construct($options) {
		$this->appid = isset($options['appid'])?$options['appid']:'';
		$this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
	}
	
	/**
	 * 通过JSCODE获取SessionKey
	 */
	public function getSessionKey($jsCode){
		if (!$jsCode) {
			return false;
		}
		$result = $this->http_get(self::API_BASE_URL_PREFIX.self::SNS_SESSION_KEY.'appid='.$this->appid.'&secret='.$this->appsecret.'&js_code='.$jsCode.'&grant_type=authorization_code');
		if ($result) {
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				
				return false;
			}

			return $json;
		}
		
		return false;
	}
	
	/**
	 * GET 请求
	 * @param string $url
	 */
	private function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
	
	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @param boolean $post_file 是否文件上传
	 * @return string content
	 */
	private function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
			$is_curlFile = true;
		} else {
			$is_curlFile = false;
			if (defined('CURLOPT_SAFE_UPLOAD')) {
				curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
			}
		}
		if (is_string($param)) {
			$strPOST = $param;
		}elseif($post_file) {
			if($is_curlFile) {
				foreach ($param as $key => $val) {
					if (substr($val, 0, 1) == '@') {
						$param[$key] = new \CURLFile(realpath(substr($val,1)));
					}
				}
			}
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
}