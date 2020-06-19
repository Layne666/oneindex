<?php
namespace jane;




class DavAuth{

	public $uid;

	function __construct($id) {
		$this->uid = 123456;
	}

	public function  __invoke($realm,$um){
	
		return "123456";
	}

}

?>