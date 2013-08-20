<?php

namespace App\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

class BaseController extends AbstractActionController {
	protected $account;
	protected $minify;
	protected $require_login = false;
	
	public function dispatch(Request $request, Response $response = null) {
		
		$this->account = $this->getServiceLocator()->get('App\Model\Account');
		$this->minify = $this->getServiceLocator()->get('Minify\Core');
		
		$cookie = $request->getCookie();

		// Authenticate session
		$access_token = false;
		
		if(isset($cookie[$this->account->access_token_name])) {
			$access_token = $cookie[$this->account->access_token_name];
		}
		
		if($access_token) {
			$this->account->authenticate($access_token);
		}
		
		if($this->require_login) {
			if(!$this->account->logged_in()) {
				$this->plugin('redirect')->toUrl('/?r='.$request->getUri()->getPath());
			}
		}
		
		$this->layout()->path = $request->getUri()->getPath();

        return parent::dispatch($request, $response);
    }
	
	public function postDispatch() {
		$this->layout()->account = $this->account->flat();
		$this->layout()->js = $this->minify->js_html();
		$this->layout()->css = $this->minify->css_html();
	}	
}
