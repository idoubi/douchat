<?php 

/**
 * 微信支付异步通知处理程序
 * @author 艾逗笔<765532665@qq.com>
 */
ini_set('always_populate_raw_post_data',-1);
$xml = file_get_contents('php://input');

$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		// 将xml格式的数据转换为array数组

$attach = $arr['attach'];											// 获取通知中包含的附加参数
$params = json_decode($attach, true);								// 将附加参数转换为数组

if ($params['notify']) {
	$notify_url = $params['notify'];				// 将通知转发到插件控制器中进行处理
	$arr['mpid'] = $params['mpid'];
	$arr['notify_url'] = $notify_url;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $notify_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$return = curl_exec($ch);
	curl_close($ch);
}


?>