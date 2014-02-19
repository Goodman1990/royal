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
use Zend\Db\ResultSet\ResultSet;
use Royal\view\Helper\GetNavigation;
use Royal\helpers\generalHelper;


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
                    'Page' => __DIR__ . '/../../vendor/page',
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
//            'factories' => array(
//                'GetNavigationHelper' => function($sm) {
//                        $this->getServiceConfig();
//                        $helper =new \Helper\GetNavigationHelper();
////                        $helper->sm = $sm;
////                         var_dump($sm->get('router'));
////                        exit;
//
//                        return $helper;
//                    },
//
//            ),
            'factories' => array(
                'nav' => 'Navigation\MyNavigationFactory',
//                'nav1' => function($sm) {
//                        $helper =new \Navigation\MyNavigation($sm);
//                        return $helper;
//                    },
            )
//            'navigation' => array(
//                    'default' => array(
//                        array(
//                            'label' => 'Home',
//                            'route' => 'home',
//                        ),
//                        array(
//                            'label' => 'Album',
//                            'route' => 'Royal',
//                            'pages' => array(
//                                array(
//                                    'label' => 'Add',
//                                    'route' => 'Royal',
//                                    'action' => 'Index',
//                                ),
//
//                            ),
//                        ),
//                    ),
//                ),
        );
    }

}
