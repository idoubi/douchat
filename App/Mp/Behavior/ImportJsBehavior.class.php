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
						if (strpos($k, '.') === false) {
							$res[] = '<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>';
						} else {
							$res[] = '<script src="//cdn.bootcss.com/jquery/'.$k.'/jquery.min.js"></script>';
						}
						break;
					case 'zepto':
						if (strpos($k, '.') === false) {
							$res[] = '<script src="//cdn.bootcss.com/zepto/1.0rc1/zepto.min.js"></script>';
						} else {
							$res[] = '<script src="//cdn.bootcss.com/zepto/'.$k.'/zepto.min.js"></script>';
						}
						break;
					case 'frozen':
						if (strpos($k, '.') === false) {
							$res[] = '<script src="//cdn.bootcss.com/FrozenUI/1.3.0/js/frozen.js"></script>';
						} else {
							$res[] = '<script src="//cdn.bootcss.com/FrozenUI/'.$k.'/js/frozen.js"></script>';
						}
						break;
					case 'vue':
						if (strpos($k, '.') === false) {
							$res[] = '<script src="//cdn.bootcss.com/vue/2.1.3/vue.min.js"></script>';
						} else {
							$res[] = '<script src="//cdn.bootcss.com/vue/'.$k.'/vue.min.js"></script>';
						}
						break;
					default:
						if (strpos($v, '.js') === false) {
							$res[] = '<script src="//cdn.bootcss.com/'.$v.'/'.$k.'/'.$v.'.min.js"></script>';
						} else {
							$mv = str_replace('.js', '.min.js', $v);
							$res[] = '<script src="//cdn.bootcss.com/'.$v.'/'.$k.'/'.$mv.'"></script>';
						}
						break;
				}
			}
		} else {
			switch ($params) {
				case 'jquery':
					$res[] = '<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>';
					break;
				case 'zepto':
					$res[] = '<script src="//cdn.bootcss.com/zepto/1.0rc1/zepto.min.js"></script>';
					break;
				case 'frozen':
					$res[] = '<script src="//cdn.bootcss.com/FrozenUI/1.3.0/js/frozen.js"></script>';
					break;
				case 'vue':
					$res[] = '<script src="//cdn.bootcss.com/vue/2.1.3/vue.min.js"></script>';
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