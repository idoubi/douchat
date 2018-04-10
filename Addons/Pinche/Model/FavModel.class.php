<?php

/**
 * 收藏模型
 */
namespace Addons\Pinche\Model;

use Think\Model;

class FavModel extends Model {
	
	protected $tableName = 'pinche_fav';
	
	public function getByOpenid($openid, $page = 1, $per = 20) {
		$data = $this->where([
			'mpid' => get_mpid(),
			'openid' => $openid
		])->page($page, $per)->order('time desc')->select();
		
		return $data;
	}
}