<?php

namespace App;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {	
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
		$sharedManager = $eventManager->getSharedManager();	
		
		// Create a low priority dispatch event 'postDispatch'
		$sharedManager->attach(__NAMESPACE__, 'dispatch', function($e) {
			$controller = $e->getTarget();
			if(method_exists($controller, 'postDispatch')) {
				$controller->postDispatch();
			}
		});
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'App\Model\Account' => function($sm) {
                	if(!$sm->has('Account')) {
	                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	                    $account = new = Model\Account($dbAdapter, new \Mlib\Model\Session($dbAdapter));
						$sm->setService('Account', $account);
					} else {
						$account = $sm->get('Account');
					}
                    return $account;
                },
            ),
        );
    }	

    public function getAutoloaderConfig()
    {
        return array(
	        'Zend\Loader\ClassMapAutoloader' => array(
	            __DIR__ . '/autoload_classmap.php',
	        ),        
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
