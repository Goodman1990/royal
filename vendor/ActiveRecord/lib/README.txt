Include in module


public function getAutoloaderConfig()
{

    return array(
        'Zend\Loader\ClassMapAutoloader' => array(
            __DIR__ . '/autoload_classmap.php',
        ),
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                'ActiveRecord' =>  __DIR__ . '/../../vendor/ActiveRecord/lib/ActiveRecord',
            ),
        ),
    );
}


Include to config
/config/autoload/namespaces.local.php

<?php
return array(
    Zend\Loader\AutoloaderFactory::factory(array(
         'Zend\Loader\StandardAutoloader' => array(
             'namespaces' => array(
                 'ActiveRecord' =>  __DIR__ . '/../../vendor/ActiveRecord/lib/ActiveRecord',
             ),
         )
    ))
);



Add string to     /vendor/composer/autoload_namespaces.php
'ActiveRecord\\' => array($vendorDir . '/ActiveRecord/lib'),
