<?php
namespace Helper;
/**
 * Created by PhpStorm.
 * User: 1-lenovo
 * Date: 31.12.13
 * Time: 14:30
 */

use Zend\View\Helper\AbstractHelper;
use Royal\helpers\generalHelper;
use Zend\Navigation\Navigation;

class GetNavigationHelper extends AbstractHelper {

    public function __invoke(){

        $navigation = array();
    	$generalHelper = new generalHelper();
        $navigationInfo = \Royal\Models\CategoryPagesModel::model()->getAll();
        foreach ($navigationInfo as $key ) {
        
        	$navigation[] =  array(

                'label' => $key['title'],
                'uri'=>'/index/'.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
                'resource'=>$key['id'],

            );
        }
        $container = new \Zend\Navigation\Navigation($navigation);

        return $container;
    }

} 