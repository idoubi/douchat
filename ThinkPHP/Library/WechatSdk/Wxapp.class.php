<?php

/**
 * 小程序SDK
 * @author 艾逗笔<http://idoubi.cc>
 */

namespace WechatSdk;

class Wxapp {
	const API_URL_PREFIX = 'https://api.weixin.qq.com/wxa';
	const AUTH_URL = '/cgi-bin/token?grant_type=client_credential&';
	const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com'; //以下API接口URL需要使用此前缀
	const SNS_SESSION_KEY = '/sns/jscode2session?';
	const QRCODE_GET_URL_A = '/getwxacode?';
	const QRCODE_GET_URL_B = '/getwxacodeunlimit?';
	const QRCODE_GET_URL_C = '/cgi-bin/wxaapp/createwxaqrcode?';
	
	private $token;
	private $encodingAesKey;
	private $appid;
	private $appsecret;
	private $access_token;
	public $debug =  false;
	public $errCode = 40001;
	public $errMsg = "no access";
	public $logcallback;
	
	public function __construct($options) {
		$this->token = isset($options['token'])?$options['token']:'';
		$this->encodingAesKey = isset($options['encodingaeskey'])?$options['encodingaeskey']:'';
		$this->appid = isset($options['appid'])?$options['appid']:'';
		$this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
		$this->debug = isset($options['debug'])?$options['debug']:false;
		$this->logcallback = isset($options['logcallback'])?$options['logcallback']:false;
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
	
	// 获取小程序码
    // 修改说明: ermao<ermaotech@163.com>
    // 增加对获取小程序码后返回值的判断，获取出错时会返回json，成功会返回string
    // 修改前，只要有返回值就写入文件，如果获取出错，会生成一个无法打开的jpg文件，并返回成功
    // 获取小程序码请求参数中is_hyaline表示是否透明，在透明情况下，生成的文件格式应为png
	public function getQrcode($path,$type=1,$options=[],$filename=''){
		if (!$this->access_token && !$this->checkAuth()) return false;
		if (!isset($path)) return false;
		$api = '';
		$params = [];
		switch ($type) {
			case 1:
				if (strlen($path) > 128)
					return false;
				$api = self::API_URL_PREFIX . self::QRCODE_GET_URL_A . 'access_token=' . $this->access_token;
				$params['path'] = $path;
				break;
			case 2:
				$api = self::API_URL_PREFIX . self::QRCODE_GET_URL_B . 'access_token=' . $this->access_token;
				$params['scene'] = $path;
				break;
			case 3:
				if (strlen($path) > 128)
					return false;
				$api = self::API_BASE_URL_PREFIX . self::QRCODE_GET_URL_C . 'access_token=' . $this->access_token;
				$params['path'] = $path;
				break;
			default:
				return false;
		}
		
		$data = array_merge($params, $options);
		$result = $this->http_post($api, self::json_encode($data));
        if (!is_object(json_decode($result))) { //判断返回值是否为json，是否生成小程序码成功
            if (empty($filename)) {            // 如果没有指定要保存到的文件
                $path = UPLOAD_PATH . 'WxaQrcode/';
                if (!is_dir($path)) {
                    @mkdir($path, 0777);
                }
                if ($data['is_hyaline'] === true) { //如果透明小程序码生成png
                    $filename = $path . time() . '.png';
                } else {
                    $filename = $path . time() . '.jpg';
                }

            }
            $res = file_put_contents($filename, $result);
            if (!$res) {
                return false;
            }
            return $filename;
        } else {
            return json_decode($result);
        }
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
	
	/**
	 * log overwrite
	 * @see Wechat::log()
	 */
	protected function log($log){
		if ($this->debug) {
			if (function_exists($this->logcallback)) {
				if (is_array($log)) $log = print_r($log,true);
				return call_user_func($this->logcallback,$log);
			}elseif (class_exists('Log')) {
				Log::write('wechat：'.$log, Log::DEBUG);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 设置缓存，按需重载
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		return S($cachename,$value,$expired);
	}
	
	/**
	 * 获取缓存，按需重载
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		return S($cachename);
	}
	
	/**
	 * 清除缓存，按需重载
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return S($cachename,null);
	}
	
	/**
	 * 获取access_token
	 * @param string $appid 如在类初始化时已提供，则可为空
	 * @param string $appsecret 如在类初始化时已提供，则可为空
	 * @param string $token 手动指定access_token，非必要情况不建议用
	 */
	public function checkAuth($appid='',$appsecret='',$token=''){
		if (!$appid || !$appsecret) {
			$appid = $this->appid;
			$appsecret = $this->appsecret;
		}
		if ($token) { //手动指定token，优先使用
			$this->access_token=$token;
			return $this->access_token;
		}
		
		$authname = 'wxapp_access_token_'.$appid;
		if ($rs = $this->getCache($authname))  {
			$this->access_token = $rs;
			return $rs;
		}
		$result = $this->http_get(self::API_BASE_URL_PREFIX.self::AUTH_URL.'appid='.$appid.'&secret='.$appsecret);
		if ($result)
		{
			$json = json_decode($result,true);
			if (!$json || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			$this->access_token = $json['access_token'];
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			$this->setCache($authname,$this->access_token,$expire);
			return $this->access_token;
		}
		return false;
	}
	
	/**
	 * 微信api不支持中文转义的json结构
	 * @param array $arr
	 */
	static function json_encode($arr) {
		if (count($arr) == 0) return "[]";
		$parts = array ();
		$is_list = false;
		//Find out if the given array is a numerical array
		$keys = array_keys ( $arr );
		$max_length = count ( $arr ) - 1;
		if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
			$is_list = true;
			for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
				if ($i != $keys [$i]) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}
		foreach ( $arr as $key => $value ) {
			if (is_array ( $value )) { //Custom handling for arrays
				if ($is_list)
					$parts [] = self::json_encode ( $value ); /* :RECURSION: */
				else
					$parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
			} else {
				$str = '';
				if (! $is_list)
					$str = '"' . $key . '":';
				//Custom handling for multiple data types
				if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
					$str .= $value; //Numbers
				elseif ($value === false)
					$str .= 'false'; //The booleans
				elseif ($value === true)
					$str .= 'true';
				else
					$str .= '"' . addslashes ( $value ) . '"'; //All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
				$parts [] = $str;
			}
		}
		$json = implode ( ',', $parts );
		if ($is_list)
			return '[' . $json . ']'; //Return numerical JSON
		return '{' . $json . '}'; //Return associative JSON
	}
}