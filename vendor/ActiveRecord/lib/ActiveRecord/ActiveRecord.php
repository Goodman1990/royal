<?php
namespace ActiveRecord;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;

/**
 * ActiveRecord is zend light ORM class.
 */
abstract class ActiveRecord extends AbstractTableGateway
{
    protected $attributes = array();
    protected $nativeAttributes = array();

    protected $asArray = false;

    protected $table;
    protected $tablePrimaryKey;
    protected $scenario;
    protected $_select;
    protected $_criteria = array();



    /**
     * @return string
     */
    abstract public function getTableName();

    /**
     * @return array of default settings
     */
    public function getARDefaultSettings()
    {
        return array(
            'asArray'          => false,
            'scenario'         => false,
            'attributes'       => array(),
            'nativeAttributes' => array(),
            '_select'          => null,
            '_criteria'        => array(),
            'tablePrimaryKey'  => 'id',
        );
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $feature = new Feature\GlobalAdapterFeature();
        $this->adapter = $feature->getStaticAdapter();
        $this->table = $this->getTableName();
        $this->tablePrimaryKey = 'id';

        $this->initialize();
    }

    /**
     * __call is "magic" method for generating live attributes
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (preg_match('/(find|exec)/', $method)) {
            if (method_exists($this, $method)) {
                $answer = call_user_func_array(array($this,$method), $args);
                //$answer = $this->$method($args);
                if (count($answer) == 1) {
                    if ($this->asArray) {
//                        var_dump($answer);
//                        exit;
                        $result = $answer;
                    } else {
                        $this->scenario = 'update';
                        $result = $this->setAttributes(array_pop($answer), true);
                    }
                } else {
                    $result = $answer;
                }

                return $result;
            }
        }
    }

    /**
     * Get model attribute
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            if (isset($this->attributes[$name])) {
                return $this->attributes[$name];
            }
        }
    }

    /**
     * Set model attribute
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            return $this->$name = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * Set scenario
     * @param string $scenario
     * @return ActiveRecord
     */
    public function setScenario($scenario)
    {
        $this->scenario = $scenario;
        return $this;
    }

    /**
     * Get scenario
     * @return string
     */
    public function getScenario()
    {
        $primaryKey = $this->tablePrimaryKey;
        $this->scenario = ($this->$primaryKey !== null) ?  'update' : 'insert';
        return $this->scenario;
    }

    /**
     * Get scenario
     * @return string
     */
    public function getScenarioByAttributes()
    {
        $nativeAttributes = array_filter($this->nativeAttributes);
        return empty($nativeAttributes) ? 'insert' : 'update';
    }

    /**
     * Set model attributes from array
     * @param array $attributes
     * @param boolean $fromDataBase if attributes from database
     * @return ActiveRecord
     */
    public function setAttributes($attributes, $fromDataBase = false)
    {
        $arrayOfAttributes = (array) $attributes;
        if ($fromDataBase) {
            $this->attributes = $attributes;
            $this->nativeAttributes = $arrayOfAttributes;
        } else {
            foreach ($arrayOfAttributes as $key => $value) {
                if (in_array($key, $this->attributeNames())) {
                    if ($arrayOfAttributes[$key] === null) {
                        throw new \Exception('Attribute ' . $key . ' in model ' . get_class($this) . ' is NULL');
                    }
                    $this->attributes[$key] = $value;
                }
            }
            //$this->attributes = array_merge($this->attributes, $arrayOfAttributes);
        }

        return $this;
    }

    /**
     * @return array of model attributes
     */
    public function getAttributes()
    {
        return array_filter($this->attributes, function($attribute){ return ($attribute !== null); });
    }

    /**
     * Fetch all records raw format (as object)
     * @return object
     */
    protected function findAll()
    {
        return $this->select()->toArray();
    }

    public function customSelect($where){

        $this->customSelect['where'] = $where;//$this->sql->select()->where($where);

        return $this;
    }

    public function addOrder($order){

        $this->customSelect['order'] = $order;

        return $this;
    }

    public  function findAllOrder($order){

        $this->customSelect =$this->sql->select()->order($order);

        return $this->customExecute();
    }

    public function findAllGroup($group){
        $this->customSelect = $this->sql->select()->group($group);

        return $this->customExecute();
    }

    public function  customExecute(){

        $statement = $this->sql->prepareStatementForSqlObject($this->customSelect);
        $results = $statement->execute();
        $data = array();
        foreach($results as $result)
            $data[] = $result;

        return $data;
    }
    /**
     * Find record by primary key
     * @return object
     */
    protected function findByPk($Pk)
    {
        return $this->select(array($this->tablePrimaryKey => $Pk))->toArray();
    }

    /**
     * Find by attributes
     * @return  object
     */
    protected function findByAttributes($attributes)
    {
//        var_dump($this->select($attributes)->toArray());
//        exit;
        return $this->select($attributes)->toArray();

    }

    /**
     * Exec raw
     * @return object
     */
    protected function findByCriteria()
    {
        $select = $this->getSelectByCriteria();
        $statement = $this->getSql()->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = clone $this->resultSetPrototype;
        $resultSet->initialize($result);
        return $resultSet->toArray();
    }

    /**
     * Set count criteria
     * @return ActiveRecord
     */
    public function setCountCriteria()
    {
        $criteria = $this->getCriteria();
        $criteria['columns'] = array(array(
            'count_records' => new \Zend\Db\Sql\Expression('COUNT(*)')
        ));
        $this->setCriteria($criteria, false);
        return $this;
    }

    /**
     * Count of recorder
     * @return integer
     */
    public function count()
    {
        $this->setCountCriteria();
        $result = $this->findByCriteria();
        $resultArray = array_pop($result);
        return (int) $resultArray['count_records'];
    }

    /**
     * Write model to database
     * @return boolean
     */
    public function write()
    {

        if ($this->getScenario() == 'insert') {
//            var_dump($this->getAttributes());
//            exit;
            $status = $this->insert($this->getAttributes());
        } else {
            return $this->update($this->getAttributes(), array(
            'id'=>$this->id
            ));
        }
        
        if ($status) {
			$id = $this->getInsertId();
			if (preg_match('/^\d+$/', $id) && $id != '0') {
//				$this->setAttributes(compact('id'));
			}
		} else {
			$this->addError($this->$this->tablePrimaryKey, 'User not created!');
		}
        
        return !$this->hasErrors();
    }

    /**
     * Get select by criteria
     * @param array $criteria
     * @return Select
     */
    public function getSelectByCriteria($criteria = null)
    {
        if ($criteria === null) {
            $criteria = $this->getCriteria();
        }
        $select = $this->getSql()->select();
        foreach ($criteria as $operation => $options) {
            if ($operation == 'join') {

                call_user_func_array(array($select,$operation), $options);

            } else {

                if (is_array($options)) {

                    foreach ($options as $option) {

                        if ($operation == 'multijoin') {
                            call_user_func_array(array($select, 'join'), $option);
                        } else {
                            $select->$operation($option);
                        }

                    }
                } else {
                    $select->$operation($options);
                }
            }
        }
        return $select;
    }


    /**
     * Get SQL string by criteria
     * @return string
     */
    public function getSQLByCriteria()
    {
        $select = $this->getSelectByCriteria($this->getCriteria());
        return $select->getSqlString($this->getAdapter()->platform);
    }

    /**
     * Get base criteria by attributes
     * @return array
     */
    public function getCriteria()
    {
        if (empty($this->_criteria)) {
            $criteria = array();
            foreach ($this->attributeNames() as $attribute) {
                if ($this->$attribute !== null) {
                    $criteria['where'][] = array($this->getTableName() . '.' . $attribute => $this->$attribute);
                }
            }
            $this->_criteria = $criteria ;
        }
        return $this->_criteria;
    }

    /**
     * Set criteria
     * @param array $criteria
     * @param boolean $merge
     * @return ActiveRecord
     */
    public function setCriteria($criteria, $merge = true)
    {
        if ($merge) {
            $this->_criteria = array_merge_recursive($this->getCriteria(), $criteria);
        } else {
            $this->_criteria = $criteria;
        }
        return $this;
    }

    /**
     * Get sub select
     * @param string $expression
     * @return Expression
     */
    public function getSubSelect($expression)
    {
        return new \Zend\Db\Sql\Expression("($expression)");
    }

    /**
     * @return string of id or zero
     */
    public function getInsertId()
    {
        return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
    }
}
