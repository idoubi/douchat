<?php

namespace Addons\IdouDg\Controller;
use Mp\Controller\ApiController;

/**
 * 在线点歌响应控制器
 * @author 艾逗笔
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param $message array 微信消息数组
	 */
	public function wechat($message = array()) {
		if ($message['MsgType'] == 'voice') {		// 语音点歌
			$content = $message['Recognition'];		// 语音识别结果
			$arr = $this->getBaiDugqlist($content);
            $mygqinfoarr = $this->getBaiDugqinfo($arr['0']['song_id']);
		 	$songName = $mygqinfoarr['songName'];
		 	$artistName = $mygqinfoarr['artistName'];
		 	$songLink = $this->getBaiDugqurl($mygqinfoarr['songLink']);
		} else {									// 文字点歌
			$content = $message['Content'];
			preg_match('/^点歌(.+)/', $content, $m);
			if ($m[1]) {	// 按歌名点歌
				$arr = $this->getBaiDugqlist($m[1]);
	            $mygqinfoarr = $this->getBaiDugqinfo($arr['0']['song_id']);
			 	$songName = $mygqinfoarr['songName'];
			 	$artistName = $mygqinfoarr['artistName'];
			 	$songLink = $this->getBaiDugqurl($mygqinfoarr['songLink']);
			} else {	// 随机点歌
				$randgqinfoarr = $this->getBaiDurand();//随机歌曲
			 	$songName = $randgqinfoarr['songName'];
			 	$artistName = $randgqinfoarr['artistName'];
			 	$songLink = $this->getBaiDugqurl($randgqinfoarr['songLink']);	
			}
		}

		if (!$songLink) {
			reply_text("系统没有找到相关歌曲\n发送【点歌】随机听歌，\n发送【点歌+歌曲名】点歌");
		}

		$music['title'] = $songName;
		$music['description'] = $artistName;
		$music['musicurl'] = $songLink;
		reply_music($music);
	}

	//获取搜索歌名
	function getBaiDugqlist($gq) {
		$param ['from'] = 'qianqian';
		$param ['version'] = '2.1.0';
		$param ['method'] = 'baidu.ting.search.common';
		$param ['format'] = 'json';
		$param ['query'] = $gq;
		$param ['page_no'] = '1';
		$param ['page_size'] = '200';
		$url = 'http://tingapi.ting.baidu.com/v1/restserver/ting?'. http_build_query ( $param );
		$content = file_get_contents ( $url );
		$content = json_decode ( $content, true );
		if (!empty($content['song_list'])) {
			return $content['song_list'];
		}else{
			return reply_text("系统没有找到相关歌曲\n发送【点歌】随机听歌，\n发送【点歌+歌曲名】点歌");
		}

	}
	//获取ID歌曲信息
	function getBaiDugqinfo($songIds) {
		$param ['songIds'] = $songIds;
		$url = 'http://ting.baidu.com/data/music/links?'. http_build_query ( $param );
		$content = file_get_contents ( $url );
		$content = json_decode ( $content, true );
		if ($content['errorCode'] == 22000) {
			return $content['data']['songList']['0'];
		}else{
			return reply_text("系统没有找到相关歌曲\n发送【点歌】随机听歌，\n发送【点歌+歌曲名】点歌");
		}
	}

	//随机音乐
	function getBaiDurand() {
		$gqlist=$this->getBaiDudt('public_tuijian_suibiantingting');
		shuffle($gqlist);//调用现成的数组随机排列函数
		$gqlist = array_slice($gqlist,0,1);//截取前$limit个
		$gqinfo = $this->getBaiDugqinfo($gqlist['0']['songid']);
		return $gqinfo;
	}
	//随机音乐列表
	function getBaiDudt($dt) {
		$param ['from'] = 'qianqian';
		$param ['version'] = '2.1.0';
		$param ['method'] = 'baidu.ting.radio.getChannelSong';
		$param ['format'] = 'json';
		$param ['pn'] = '0';
		$param ['rn'] = '100';
		$param ['channelname'] = 'public_tuijian_rege';
		$url = 'http://tingapi.ting.baidu.com/v1/restserver/ting?'. http_build_query ( $param );
		$content = file_get_contents ( $url );
		$content = json_decode ( $content, true );
		if ($content['error_code'] == 22000) {
			return($content['result']['songlist']);
		}else{
			return reply_text("系统没有找到相关音乐\n发送【点歌】随机听歌，\n发送【点歌+歌曲名】点歌");
		}
	}

	//歌曲URL处理
	function getBaiDugqurl($songurl) {
		if(strrpos($songurl,'&')){
			$songurl = substr($songurl,0,strrpos($songurl,'&'));
		}
		return $songurl;
	}
}

?>