<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
            'reCaptchaService' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $config = $sm->get('Config');
//               print_r();//new \Zend\Captcha\ReCaptcha($config['recaptcha']));
//                   exit;
                return new \Zend\Captcha\ReCaptcha(array('name' => 'recaptcha',
                    'privKey' => '6LdhNukSAAAAAPBIZ690griyTEkR1uQPc__bTDOF',
                    'pubKey' => '6LdhNukSAAAAAO8mfrbCfw4lz_PQi6kXIFy7k135'
                ) );
            },
        ),
    ),
);
