<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;

class IndexController extends BaseController {
	public function indexAcion() {
		$view = array();
		return new ViewModel($view);
	}	
}