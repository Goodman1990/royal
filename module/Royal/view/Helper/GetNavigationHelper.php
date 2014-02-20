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

    public function __invoke($navigationType){

        if($navigationType=='top'){

            $navigationInfo = \Royal\Models\CategoryPagesModel::model()->findAllOrder('number DESK ');

        }else if($navigationType=='bottom'){

            $navigationInfo = \Royal\Models\CategoriesProductModel::model()->findAllOrder('number DESK ');

        }else{
//            echo 123;
//            exit;
            $container = new \Zend\Navigation\Navigation($this->getAdminNavigation());
            return $container;
        }

        $navigation = array();
    	$generalHelper = new generalHelper();

        foreach ($navigationInfo as $key ) {
        	$navigation[] =  array(
                'label' => $key['title'],
                'uri'=>'    /'.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
                'resource'=>$key['id'],
                'visible' => $key['visible'],
                'pages'=>array(
                    array(
                        'label' => $key['title'],
                        'uri'=>'    /'.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
                        'resource'=>$key['id'],
                        'visible' => $key['visible'],
                    ),array(
                        'label' => $key['title'],
                        'uri'=>'    /'.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
                        'resource'=>$key['id'],
                        'visible' => $key['visible'],
                    )
                )
            );
        }

        $container = new \Zend\Navigation\Navigation($navigation);

        return $container;
    }

    public function getAdminNavigation(){

        return array(
            array(
                'label' => 'Категории',
                'uri'=>'/admin/editCategory/product',
                'resource'=>'1',
                'visible' =>1,
                'pages'=>array(
                    array( 'label' => 'Товара',
                        'uri'=>'/admin/editCategory/product',
                        'resource'=>'1.1',
                        'visible' =>1,
                    ),

                    array(
                        'label' => 'Страниц',
                        'uri'=>'/admin/editCategory/page',
                        'resource'=>'1.2',
                        'visible' =>1,
                    ),
                    array(
                        'label' => 'Под категории',
                        'uri'=>'/admin/editSubcategories/subcategories',
                        'resource'=>'1.3',
                        'visible' =>1,
                    ),
                    array(
                        'label' => 'Производители',
                        'uri'=>'/admin/editSubcategories/manufacturers',
                        'resource'=>'1.4',
                        'visible' =>1,
                    ),
                ),
            ),
            array(
                'label' => 'Продукт',
                'uri'=>'/admin/addProduct/',
                'resource'=>'2',
                'visible' =>1,
                'pages'=>array(
                    array( 'label' => 'Добавить продукт',
                        'uri'=>'/admin/addProduct',
                        'resource'=>'2.1',
                        'visible' =>1,
                    ),
                    array( 'label' => 'Список продуктов',
                        'uri'=>'/admin/getAllproduct',
                        'resource'=>'2.2',
                        'visible' =>1,
                    ),
//                    array( 'label' => 'Список продуктов',
//                        'uri'=>'/admin/editProduct',
//                        'resource'=>'2.2',
//                        'visible' =>1,
//                    ),
                ),
            ),

//            array(
//                'label' => 'категории',
//                'uri'=>'/admin/editCategory/product',
//                'resource'=>'1',
//                'visible' =>1,
//                'pages'=>array(
//                    array( 'label' => 'dasdsad',
//                        'uri'=>'/admin/editCategory/product',
//                        'resource'=>'2',
//                        'visible' =>1,
//                    ),
//
//                    array(
//                        'label' => 'asdasd',
//                        'uri'=>'/admin/editCategory/page',
//                        'resource'=>'3',
//                        'visible' =>1,
//                    ),
//                    array(
//                        'label' => 'Под asdsa',
//                        'uri'=>'/admin/editCategory/page',
//                        'resource'=>'4',
//                        'visible' =>1,
//                    ),
//                ),
//            ),

        );

    }

} 