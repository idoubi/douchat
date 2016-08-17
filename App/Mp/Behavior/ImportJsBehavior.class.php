<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 导入通用JS
 * @author 艾逗笔<765532665@qq.com>
 */
class ImportJsBehavior extends Behavior {

	public function run(&$params) {
		if (is_array($params)) {
			foreach ($params as $k => $v) {
				switch ($v) {
					case 'jquery':
						$res[] = "<script src='/Public/Common/js/jquery.js'></script>";
						break;
					case 'jquery.min':
						$res[] = "<script src='/Public/Common/js/jquery.min.js'></script>";
						break;
					case 'zepto.min':
						$res[] = "<script src='/Public/Common/js/zepto.min.js'></script>";
						break;
					case 'frozen':
						$res[] = "<script src='/Public/Common/js/frozen.js'></script>";
						break;
					default:
						# code...
						break;
				}
			}
		} else {
			switch ($params) {
				case 'jquery':
					$res[] = "<script src='/Public/Common/js/jquery.js'></script>";
					break;
				case 'jquery.min':
					$res[] = "<script src='/Public/Common/js/jquery.min.js'></script>";
					break;
				case 'zepto.min':
					$res[] = "<script src='/Public/Common/js/zepto.min.js'></script>";
					break;
				case 'frozen':
					$res[] = "<script src='/Public/Common/js/frozen.js'></script>";
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