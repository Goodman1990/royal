<?php
/**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:37
 */

namespace Royal\Controller\console;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\TableGateway\Feature;
use Zend\View\Model\JsonModel;


class consoleController extends  AbstractActionController{


protected $adapter;

    public function generateModelAction(){

        $dataBaseName = array();
        $tablesInfo = array();

        $feature = new Feature\GlobalAdapterFeature();
        $this->adapter = $feature->getStaticAdapter();

        $statement = $this->adapter->query('SELECT DATABASE() as name_database');
        $results = $statement->execute();

        foreach ($results as $result){
            $dataBaseName =$result;
        }

        $statement = $this->adapter->query('
            SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_TYPE = "BASE TABLE" AND TABLE_SCHEMA="'.$dataBaseName["name_database"].'"'
        );


        $results = $statement->execute();
        foreach ($results as $result){
            $tablesInfo[$result['TABLE_NAME']] =$result;

        }


        foreach($tablesInfo as $key=>$value){

            $statement = $this->adapter->query('
                SHOW columns FROM '.$value['TABLE_NAME'].'');

            $results = $statement->execute();

            $tablesInfo[$value['TABLE_NAME']]['fields']  =  array();
            foreach ($results as $result){

                $tablesInfo[$key]['fields'][] = $result['Field'];
            }
            $statement = $this->adapter->query('
                SELECT COLUMN_NAME as foreign_key,
                       REFERENCED_TABLE_NAME as referenced_table_name,
                       REFERENCED_COLUMN_NAME as  referenced_key
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_NAME ="'.$value['TABLE_NAME'].'" AND
                CONSTRAINT_NAME <>"PRIMARY" AND REFERENCED_TABLE_NAME is not null AND TABLE_SCHEMA="'.$dataBaseName["name_database"].'"'
            );

            $results = $statement->execute();

            $tablesInfo[$key]['foreign_keys']  =  array();
            foreach ($results as $result){

                $tablesInfo[$key]['foreign_keys'][] = $result;

            }
        }

        foreach($tablesInfo as $key=>$value){

            $tpl = '';
            $stringFieldsToArray = '';
            $stringFields = '';
            $modelName = '';
            $modelName = str_replace('_',' ',$key);
            $modelName = preg_replace('/\s/', '', ucwords($modelName));
            $tpl = file_get_contents(__DIR__.'/tplphp/tplModelEntity.ptpl',"w+");

            for($i = 0 ; $i<count($value['fields']); $i++){
                $stringFieldsToArray.='"'.$value['fields'][$i].'",'.PHP_EOL;
            }

            $stringFields = implode(',',$value['fields']);
            $tpl = str_replace('[:table:]',$key,$tpl);
            $tpl = str_replace('[:nameModel:]',$modelName,$tpl);
            $tpl = str_replace('[:array:]',$stringFieldsToArray,$tpl);
            $tpl = str_replace('[:string:]',$stringFields,$tpl);
//            echo __DIR__.'/../../modelEntity/';
//            exit;
            $tpl = '<?php '.$tpl;
            $handel = fopen(__DIR__.'/../../modelEntity/'.$modelName.'ModelEntity'.'.php',"w+");
            fwrite($handel, $tpl);
            fclose($handel);

        }

        foreach($tablesInfo as $key=>$value){

            $tpl = '';
            $stringFieldsToArray = '';
            $stringFields = '';
            $modelName = '';
            $modelName = str_replace('_',' ',$key);
            $modelName = preg_replace('/\s/', '', ucwords($modelName));
            if(!file_exists(__DIR__.'/../../Models/'.$modelName.'Model.php')){
                echo $modelName.PHP_EOL;

                $tpl = file_get_contents(__DIR__.'/tplphp/tplModel.ptpl',"w+");
                $tpl = str_replace('[:nameModel:]',$modelName,$tpl);

                $tpl = '<?php '.$tpl;
                $handel = fopen(__DIR__.'/../../Models/'.$modelName.'Model'.'.php',"w+");
                fwrite($handel, $tpl);
                fclose($handel);
            }

        }

        echo 'generate complete';
        exit;


    }

} 