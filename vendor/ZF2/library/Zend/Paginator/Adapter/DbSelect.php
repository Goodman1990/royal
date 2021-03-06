<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;

class DbSelect implements AdapterInterface
{

    protected $distinct = false;
    /**
     * @var Sql
     */
    protected $sql = null;

    /**
     * Database query
     *
     * @var Select
     */
    protected $select = null;

    /**
     * @var ResultSet
     */
    protected $resultSetPrototype = null;

    /**
     * Total item count
     *
     * @var int
     */
    protected $rowCount = null;

    /**
     * Constructor.
     *
     * @param Select $select The select query
     * @param Adapter|Sql $adapterOrSqlObject DB adapter or Sql object
     * @param null|ResultSetInterface $resultSetPrototype
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(Select $select, $adapterOrSqlObject, ResultSetInterface $resultSetPrototype = null,$distinct=FALSE)
    {
        $this->distinct = $distinct;
        $this->select = $select;

        if ($adapterOrSqlObject instanceof Adapter) {
            $adapterOrSqlObject = new Sql($adapterOrSqlObject);
        }

        if (!$adapterOrSqlObject instanceof Sql) {
            throw new Exception\InvalidArgumentException(
                '$adapterOrSqlObject must be an instance of Zend\Db\Adapter\Adapter or Zend\Db\Sql\Sql'
            );
        }

        $this->sql                = $adapterOrSqlObject;
        $this->resultSetPrototype = ($resultSetPrototype) ?: new ResultSet;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  int $offset           Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $select = clone $this->select;
        $select->offset($offset);
        $select->limit($itemCountPerPage);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();

        $resultSet = clone $this->resultSetPrototype;
        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return int
     */
    public function count()
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }

        $select = clone $this->select;
        $select->reset(Select::COLUMNS);
        $select->reset(Select::LIMIT);
        $select->reset(Select::OFFSET);
        $select->reset(Select::ORDER);
        $select->reset(Select::GROUP);

        // get join information, clear, and repopulate without columns
        $joins = $select->getRawState(Select::JOINS);
        $select->reset(Select::JOINS);
//       print_r($joins);
//            exit;
        foreach ($joins as $join) {
            
            $select->join($join['name'], $join['on'], array(), $join['type']);
        }

         if($this->distinct){
              $select->columns(array('c' => new Expression('COUNT(DISTINCT('.$this->distinct.'))')));
         }else{ $select->columns(array('c' => new Expression('COUNT(1)')));}
       
//           echo   $this->sql->getSqlStringForSqlObject($select);
//        exit;
       
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        $row       = $result->current();

        $this->rowCount = $row['c'];
//         echo $row['c'];
//        exit;
        return $this->rowCount;
    }
}
