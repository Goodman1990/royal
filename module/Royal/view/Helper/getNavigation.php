<?php
/**
 * Created by PhpStorm.
 * User: 1-lenovo
 * Date: 31.12.13
 * Time: 14:30
 */

namespace Royal\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Royal\helpers\generalHelper;

class getNavigation extends AbstractHelper{

    public function __invoke(){

    	$generalHelper = new generalHelper();
        $navigationInfo = \Royal\Models\CategoryPagesModel::model()->getAll();
        
        foreach ($navigationInfo as $key ) {
        
        	$navigation['navigation']['default'][] =  array(
                        'label' => $navigationInfo['title'],
                        'route' => 'Royal',
                        'action' => 'index',
                        'param'=>array('id_page'=>$navigationInfo['id'].'_'.$generalHelper->transliteration($navigationInfo['title']));
                    )
        }

        return $navigation;
    }

} 