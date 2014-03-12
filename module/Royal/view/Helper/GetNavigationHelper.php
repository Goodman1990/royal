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

    public function __invoke($navigationType,$categories=null,$subcategories=null){
        $navigation = array();
        $generalHelper = new generalHelper();
        if($navigationType=='top'){

            $navigationInfo = \Royal\Models\CategoryPagesModel::model()->findAllOrder('number DESK ');
            $page = '';

        }else if($navigationType=='bottom'){


            $navigationInfo = \Royal\Models\CategoriesProductModel::model()->findAllOrder('number DESK ');
            $page='/page/categories';

        }else if($navigationType=='right'){

            $navigationInfo = \Royal\Models\SubcategoriesProductModel::model()->findByAttributes(array(
                'id_categories_product'=> $categories
            ));
            $page='/page/manufacturers';
            if($subcategories){
                $manufacturersData =  \Royal\Models\ManufacturersModel::model(array('asArray'=>true))->findByAttributes(array(
                    'id_subcategories_product'=>$subcategories,
                ));
                $page2='/page/product';
            }
            $i = -1;
            foreach ($navigationInfo as $key ) {
                $i++;
                $navigation[] =  array(
                    'label' => $key['title'],
                    'uri'=>$page.'/'.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
                    'resource'=>$key['id'],
                    'visible' => $key['visible'],
                );
                if($key['id']==$subcategories){
//                    echo'<pre>';
//                    var_dump($manufacturersData);
//                    exit;
                    foreach ($manufacturersData as $keys ) {

                        $navigation[$i]['pages'][] = array(
                            'label' => $keys['title'],
                            'uri'=>$page2.'/'.$keys['id'].'_'.$generalHelper->transliteration(trim($keys['title'])),
                            'resource'=>$keys['id'].'_manufacturers',
                            'visible' => $keys['visible'],
                        );
                        
                       

                    }
                }
            }

            $container = new \Zend\Navigation\Navigation($navigation);
            return $container;


        }else{
//            echo 123;
//            exit;
            $container = new \Zend\Navigation\Navigation($this->getAdminNavigation());
            return $container;
        }



        foreach ($navigationInfo as $key ) {
        	$navigation[] =  array(
                'label' => $key['title'],
                'uri'=>$page.'/'.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
                'resource'=>$key['id'],
                'visible' => $key['visible'],
                'pages'=>array(
//                    array(
//                        'label' => $key['title'],
//                        'uri'=>$page.'/'.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
//                        'resource'=>$key['id'],
//                        'visible' => $key['visible'],
//                    ),array(
//                        'label' => $key['title'],
//                        'uri'=>$page.''.$key['id'].'_'.$generalHelper->transliteration(trim($key['title'])),
//                        'resource'=>$key['id'],
//                        'visible' => $key['visible'],
//                    )
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
                        'label' => 'Под категории',
                        'uri'=>'/admin/editSubcategories/subcategories',
                        'resource'=>'1.2',
                        'visible' =>1,
                    ),
                    array(
                        'label' => 'Производители',
                        'uri'=>'/admin/editSubcategories/manufacturers',
                        'resource'=>'1.3',
                        'visible' =>1,
                    ),

                    array(
                        'label' => 'Группу',
                        'uri'=>'/admin/editGroup',
                        'resource'=>'1.4',
                        'visible' =>1,
                    ),

                ),
            ),
            array(
                'label' => 'Продукт',
                'uri'=>'/admin/index',
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

                ),
            ),
            array(
                'label' => 'Страници',
                'uri'=>'/admin/editPage/',
                'resource'=>'3',
                'visible' =>1,
                'pages'=>array(
                    array(
                        'label' => 'Редактировать Страницу',
                        'uri'=>'/admin/editPage',
                        'resource'=>'3.2',
                        'visible' =>1,
                    ),
                    array(
                        'label' => 'Добавить стнраницу',
                        'uri'=>'/admin/editCategory/page',
                        'resource'=>'3.1',
                        'visible' =>1,
                    ),
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