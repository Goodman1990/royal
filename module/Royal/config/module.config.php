<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonRoyal for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'generateModel' => array(
                    //'type'    => 'segment',
                    'options' => array(
                        // add [ and ] if optional ( ex : [<doname>] )
                        'route' => 'console [--generate] <name> <password>',
                        'defaults' => array(
                            '__NAMESPACE__' => 'Royal\Controller\console',
                            'controller' => 'console',
                            'action' => 'generateModel'
                        ),
                    ),
                ),
            ),
        )
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Royal\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /Royal/:controller/:action
            'Royal' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:controller][/:action][/:param1][/:param2]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'=>'[a-zA-Z-0-9_]*',
                        'param2'=>'[a-zA-Z-0-9_]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Royal\Controller',
                        'controller'    => 'Page',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',

        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
//        'factories' => array(
//            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
//        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Royal\Controller\Page' => 'Royal\Controller\PageController',
            'Royal\Controller\console\console' => 'Royal\Controller\console\consoleController',
            'Royal\Controller\admin' => 'Royal\Controller\adminController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'Royal/index/index' => __DIR__ . '/../view/Royal/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
//        'invokables'=> array(
//            'test_helper' => 'Test\View\Helper\getNavigation'
//        )
    ),
    // Placeholder for console routes

);
