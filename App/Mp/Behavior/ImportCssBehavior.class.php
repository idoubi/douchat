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
						if (strpos($k, '.') === false) {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/weui/1.1.0/style/weui.min.css">';
						} else {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/weui/'.$k.'/style/weui.min.css">';
						}
						break;
					case 'frozen':
						if (strpos($k, '.') === false) {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/FrozenUI/1.3.0/css/frozen-min.css">';
						} else {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/FrozenUI/'.$k.'/css/frozen-min.css">';
						}
						break;
					case 'bootstrap':
						if (strpos($k, '.') === false) {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css">';
						} else {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/bootstrap/'.$k.'/css/bootstrap.min.css">';
						}
						break;
					case 'font-awesome':
						if (strpos($k, '.') === false) {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">';
						} else {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/font-awesome/'.$k.'/css/font-awesome.min.css">';
						}
						break;
					default:
						if (strpos($k, '.min') === false) {
							$v = str_replace('.min', '', $v);
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/'.$v.'/'.$k.'/css/'.$v.'.min.css">';
						} else {
							$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/'.$v.'/'.$k.'/css/'.$v.'.css">';
						}
						break;
				}
			}
		} else {
			switch ($params) {
				case 'weui':
					$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/weui/1.1.0/style/weui.min.css">';
					break;
				case 'frozen':
					$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/FrozenUI/1.3.0/css/frozen-min.css">';
					break;
				case 'bootstrap':
					$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css">';
					break;
				case 'font-awesome':
					$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">';
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