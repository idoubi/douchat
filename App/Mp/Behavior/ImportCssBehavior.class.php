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
						$res[] = '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Plugins/weui-master/dist/style/weui.css">
						          <link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Plugins/weui-master/dist/example/example.css">';
						break;
					case 'frozen':
						$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/FrozenUI/1.3.0/css/frozen.css">';
						break;
					case 'bootstrap':
						$res[] = '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Common/css/bootstrap.css">';
						break;
					case 'font-awesome':
						$res[] = '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Plugins/font-awesome-4.6.3/css/font-awesome.min.css">';
						break;
					default:
						# code...
						break;
				}
			}
		} else {
			switch ($params) {
				case 'weui':
					$res[] = '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Plugins/weui-master/dist/style/weui.css">
						      <link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Plugins/weui-master/dist/example/example.css">';
					break;
				case 'frozen':
					$res[] = '<link rel="stylesheet" type="text/css" href="//cdn.bootcss.com/FrozenUI/1.3.0/css/frozen.css">';
					break;
				case 'bootstrap':
					$res[] = '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Common/css/bootstrap.css">';
					break;
				case 'font-awesome':
						$res[] = '<link rel="stylesheet" type="text/css" href="'.SITE_URL.'Public/Plugins/font-awesome-4.6.3/css/font-awesome.min.css">';
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