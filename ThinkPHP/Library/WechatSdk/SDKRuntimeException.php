<?php

namespace WechatSdk;

class  SDKRuntimeException extends \Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>