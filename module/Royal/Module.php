<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonRoyal for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Royal;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Db\TableGateway\Feature;
use Royal\view\Helper\GetNavigation;
use Zend\Session\SessionManager;
use Zend\Session\Container;




use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ViewHelperProviderInterface;


class Module implements ServiceProviderInterface,
                        AutoloaderProviderInterface,
                        ConfigProviderInterface,
                        ViewHelperProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');


        Feature\GlobalAdapterFeature::setStaticAdapter($dbAdapter);
        $this->bootstrapSession($e);
       

    }



    public function bootstrapSession($e) {

        $session = $e->getApplication()
            ->getServiceManager()
            ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {

            $session->regenerateId(true);
            $container->init = 1;
        }
    }




    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {

        return array(
            'Zend\Loader\StandardAutoloader' => array(
                    'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'ActiveRecord' =>  __DIR__ . '/../../vendor/ActiveRecord/lib/ActiveRecord',
                    'Helper' => __DIR__ . '/view/Helper/',
                    'Navigation' => __DIR__ . '/Navigation',
                    'Page' => __DIR__ . '/../../vendor/Page',
                    'watermark' => __DIR__ . '/../../vendor/watermark',
                ),
            ),
        );
    }
    public function getViewHelperConfig()
    {

        //$some = new \ActiveRecord\ActiveRecord();
        //var_dump(array_filter(get_declared_classes(), function($className){ return $className == 'ActiveRecord'; }));die;
        return array(
            'factories' => array(
                'GetNavigationHelper' => function($sm) {
                        $helper =new \Helper\GetNavigationHelper();
//                        $helper->sm = $sm;
//                         var_dump($sm->get('router'));
//                        exit;

                        return $helper;
                    }
            )
        );
    }

    public function getServiceConfig() {
//        echo 123;
//        exit;
        return array(

            'factories' => array(
                'nav' => 'Navigation\MyNavigationFactory',
                'Zend\Session\SessionManager' => function ($sm) {
                        $config = $sm->get('config');
//                       var_dump($config);
//                        exit;
                        if (isset($config['session'])) {
                            $session = $config['session'];
                            $sessionConfig = null;
                            if (isset($session['config'])) {
                                $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                                $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                                $sessionConfig = new $class();
                                $sessionConfig->setOptions($options);
                            }

                            $sessionStorage = null;
                            if (isset($session['storage'])) {
                                $class = $session['storage'];
                                $sessionStorage = new $class();
                            }

                            $sessionSaveHandler = null;
                            if (isset($session['save_handler'])) {
                                // class should be fetched from service manager since it will require constructor arguments
                                $sessionSaveHandler = $sm->get($session['save_handler']);
                            }

                            $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                            if (isset($session['validators'])) {
                                $chain = $sessionManager->getValidatorChain();

                                foreach ($session['validators'] as $validator) {
                                    $validator = new $validator();
                                    $chain->attach('session.validate', array($validator, 'isValid'));
                                }
                            }
                        } else {
                            $sessionManager = new SessionManager();
                        }
                        Container::setDefaultManager($sessionManager);
                        return $sessionManager;
                    },
            ),


        );
    }

}
