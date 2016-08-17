<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 导入通用CSS
 * @author 艾逗笔<765532665@qq.com>
 */
class ImportCssBehavior extends Behavior {

	public function run(&$params) {
		if (is_array($params)) {
			foreach ($params as $k => $v) {
				switch ($v) {
					case 'weui':
						$res[] = '<link rel="stylesheet" type="text/css" href="/Public/Common/css/weui.css">
						          <link rel="stylesheet" type="text/css" href="/Public/Common/css/weui.min.css">
						          <link rel="stylesheet" type="text/css" href="/Public/Common/css/weui.example.css">';
						break;
					case 'bootstrap':
						$res[] = '<link rel="stylesheet" type="text/css" href="/Public/Common/css/bootstrap.css">';
						break;
					default:
						# code...
						break;
				}
			}
		} else {
			switch ($params) {
				case 'weui':
					$res[] = '<link rel="stylesheet" type="text/css" href="/Public/Common/css/weui.css">
					          <link rel="stylesheet" type="text/css" href="/Public/Common/css/weui.min.css">
					          <link rel="stylesheet" type="text/css" href="/Public/Common/css/weui.example.css">';
					break;
				case 'bootstrap':
					$res[] = '<link rel="stylesheet" type="text/css" href="/Public/Common/css/bootstrap.css">';
					break;
				default:
					# code...
					break;
			}
		}
		return implode('', $res);
	}
}

 ?>