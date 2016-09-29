<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 加载编辑器
 * @author 艾逗笔<765532665@qq.com>
 */
class EditorBehavior extends Behavior {

	public function run(&$params) {
		$editor_type = isset($params['attr']) ? $params['attr'] : 'markdown';
		switch ($editor_type) {
			case 'markdown':
				$upload_url = U('Material/markdown_picupload');
				$html = '<script type="text/javascript" src="'.SITE_URL.'Public/Plugins/editormd/js/editormd.min.js"></script>
                        <script type="text/javascript">
                        var testEditor;
                        $(function() {
                            testEditor = editormd("test-editormd", {
                                width   : "99%",
                                height  : 640,
                                syncScrolling : "single",
                                emoji : true,
                                path    : "'.SITE_URL.'Public/Plugins/editormd/lib/",
                                imageUpload : true,
                                imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
                                imageUploadURL : "'.$upload_url.'",
                            });
                        });
                        </script>
                        <link rel="stylesheet" href="'.SITE_URL.'Public/Plugins/editormd/css/editormd.css" />
                        <div class="form-group">
                            <label class="col-sm-2 col-xs-3 control-label">'.$params['title'].':</label>
                            <div class="col-sm-9 col-xs-12">
                                <div id="test-editormd" style="z-index:999">
                                    <textarea style="display:none;" name="'.$params['name'].'">'.$params['value'].'</textarea>
                                </div>
                                <span class="help-block">'.$params['tip'].'</span>
                            </div>
                        </div>';
				break;
			default:
				# code...
				break;
		}
		return $html;
	}
}

 ?>