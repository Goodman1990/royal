<?php
namespace Application\Models;


use Application\modelEntity\UsersModelEntity;

class UsersModel extends UsersModelEntity
{
    const STATUS_ACTIVE     = 1;
    const STATUS_NOT_ACTIVE = 0;

    /**
     * Create statically model
     * @return ActiveRecordModel
     */
    public static function model($options = null, $className=__CLASS__)
    {
        return parent::model($options, $className);
    }

    public function rules()
    {
        return array(
//            array('username, surname', 'match', 'pattern' => '/^[-_0-9a-zA-Z]*$/iu'),
//            array('username', 'required'),
           array('email', 'email'),
        );
    }

    /**
     * Before validate function
     * @return boolean
     */
    public function beforeValidate()
    {
        // @TODO !!!!!!!password must be encrypted here.
        /*if (isset($this->password) && $this->password != '') {
            $this->password = self::hashPassword($password);
        }*/

        // @TODO deprecated see ACL
        if ($this->role !== null) {
            if (in_array($this->role, self::getAvailableUserRoleNames()) || is_numeric($this->role)) {
                if (!is_numeric($this->role)) {
                    $oldRole = null;
                    if (isset($this->nativeAttributes['role'])) {
                        $oldRole = $this->nativeAttributes['role'];
                    }
                    $roleModel  = new \Training\Model\helpEntities\Role($oldRole);
                    $this->role = $roleModel->AddRole($this->role);
                }
            } else {
                $this->addError('role', 'Wrong role value!');
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Get formated data
     * @return array
     */
    public function getFormatedData()
    {
        $results = $this->getAttributes();
        if (isset($results['authorized_date'])) {
            $temp_date = new \DateTime($results['authorized_date']);
            $results['authorized_date'] = $temp_date->format('H:i d.m.Y');
        }
        $temp_date = new \DateTime($results['registration_date']);
        $results['registration_date'] = $temp_date->format('H:i d.m.Y');
        $role = new \Training\Model\helpEntities\Role((int)$results['role']);
        $results['role_set'] = $role->GetRoles();

        return $results;
    }

    /**
     * @return string of hashed password
     */
    public static function hashPassword($password)
    {
        $salt = sha1(md5($password));
        return md5($password . $salt);
    }

    /**
     * Get all users
     * @param string $orderBy
     * @param string $order
     * @param boolean $deleted
     * @param integer $typeUser
     * @param date $dateBegin
     * @param date $dateEnd
     * @param date $dateRange
     * @param string $searchString
     * @param integer $limit
     * @param integer $page
     * @return Paginator
     */
    public function getAllUsers($orderBy, $order, $deleted, $typeUser, $dateBegin, $dateEnd, $dateRange, $searchString, $limit, $page, $roleName = 'all' )
    {
        $this->scopeUserByParams($orderBy, $order, $deleted, $searchString, $roleName);
        if ($dateBegin) {
            $this->setCriteria(array('where' => array("`users`.`authorized_date` >= '" . $dateBegin . " 00:00:00'
                                        AND `users`.`authorized_date` <= '" . $dateEnd   . " 23:59:59'")));
        }

        $paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect($this->getSelectByCriteria(), $this->adapter, null,'users.id');

        $statement = $this->sql->getSqlStringForSqlObject( $this->getSelectByCriteria() );

        $paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
        $paginator->setDefaultItemCountPerPage($limit);
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }

    /**
     * Scope user by params
     * @param string $orderBy
     * @param string $order
     * @param boolean $deleted
     * @param string $searchString
     * @return UsersModel
     */
    public function scopeUserByParams($orderBy, $order, $deleted, $searchString, $roleName)
    {
        $this->deleted = (int) $deleted;

        $select = $this->sql->select();

        if (empty($orderBy)) {
            $orderBy = 'authorized_date';
        }
        if (empty($orderBy)) {
            $orderBy = 'asc';
        }

        $orderString = $orderBy . ' ' . $order;

        // This is needed, don't touch
        /* if ($orderBy == "username")
            $orderString .= ', surname' . ' ' . $order; */

        $this->setCriteria(array(
            'columns' =>array(array(
                'username' => 'username',
                'email' => 'email',
                'surname' => 'surname',
                'status',
                'deleted' => 'deleted',
                'authorized_date',
                'id_a' => 'id'

            )),

            'multijoin' => array(
                array(
                    array( 'c' => 'course' ),
                    "users.id = c.id_author",
                    array(
                        'count_course' => new \Zend\Db\Sql\Expression('(SELECT count(*) FROM  course WHERE  id_author = users.id) '),
                    ),
                    $select::JOIN_LEFT . ' ' . $select::JOIN_OUTER
                ),
                array(
                    array( 'p' => 'progress' ),
                    'p.id_course = c.id',
                    array(
                        'id' => 'id'
                    ),
                    $select::JOIN_LEFT . ' ' . $select::JOIN_OUTER
                ),
                array(
                    array( 's' => 'users' ),
                    's.id = p.id_student',
                    array(
                        'stud_date' => new \Zend\Db\Sql\Expression('MAX(s.authorized_date)')
                    ),
                    $select::JOIN_LEFT . ' ' . $select::JOIN_OUTER
                )

            ),

            'group' => 'users.id',
            'order' => $orderString
        ));

        if ($roleName !== 'all') {
            $where = array("`users`.`role`&" . $this->getBinaryCodeByRoleName($roleName));
        } else {
            $where = array();
        }

        // If the search input string was specified
        if ( !empty($searchString) ) {
            $where[] = $this->getSearchByAuthWhere($searchString);
        }

        return $this->setCriteria( array('where' => $where) );
    }

    /**
     * Get search by auth where
     * @return string
     */
    public function getSearchByAuthWhere($searchString)
    {
        return "( `users`.`username` LIKE '%".mysql_real_escape_string($searchString)."%' OR
                  `users`.`surname`  LIKE '%".mysql_real_escape_string($searchString)."%' OR
                  `users`.`email`    LIKE '%".mysql_real_escape_string($searchString)."%' OR
                   CONCAT_WS(' ',`users`.`username`,`users`.`surname`) LIKE '%".mysql_real_escape_string($searchString)."%')";
    }

    public function scopeGetStudentOnCourse($id_course,$id_curator=0){

        $select = $this->sql->select();

           $this->setCriteria(array(
            'columns' =>array(array(
                'name' => $this->scopeConcatName(),
                'username'=>'username',
                'surname'=>'surname',
                'email' => 'email',
                'status',
                'deleted' => 'deleted',
                'authorized_date',
                'id_a' => 'id'
            )),

            'multijoin' => array(
                array(
                    array( 'p' => 'progress' ),
                    'p.id_student= users.id',
                    array(
                        'status_p' => 'status',
                        'id_student'
                    ),
                    $select::JOIN_LEFT . ' ' . $select::JOIN_OUTER
                ),
            ),
            'where'=>array(array(
                'p.id_course'=>$id_course,
            ))
        ));
        if($id_curator!=0){
            $this->setCriteria(array(
                'multijoin'=>array(
                    array(
                        array('sc'=>'student_curator'),
                        new \Zend\Db\Sql\Expression('sc.id_student=p.id_student AND sc.id_curator='.$id_curator.' AND sc.id_course=p.id_course'),
                        array()
                    )
                )
            ));
        }

        return $this->getCriteria();

    }

    public function getStudentOnCourseForSend($id_course,$statusProgress='0',$id_curator=0){

        if($statusProgress==0){

           $statusProgress2 =10;
           $status = 'p.status!='.$statusProgress2.'';

        }else{

            $statusProgress2 =0;
            $status = 'p.status='.$statusProgress2.'';

        }
        $pstatus = 'p.status in (1,'.$statusProgress.')';

        if($id_curator==0)
            $criteria = $this->scopeGetStudentOnCourse($id_course);
        else
            $criteria = $this->scopeGetStudentOnCourse($id_course,$id_curator);
       $criteria['where'][] = array(
            'users.deleted'=>'0',
            'users.status'=>'1',
            $pstatus,'p.deleted=0 OR ('.$status.' AND users.deleted=0 AND users.status=1 AND p.deleted=0 AND p.id_course = '.$id_course.')'
        );

        unset($criteria['columns']['id_a']);
        $criteria['columns'][0]['id_student_p'] ='id';

//        echo '<pre>';
//        print_r($criteria['columns']);
//        exit;
        $this->setCriteria($criteria,false);

//        print_r($this->getSQLByCriteria());
//        exit;

        return $this->findByCriteria();


    }

    public function  scopeConcatName(){

       return new \Zend\Db\Sql\Expression('CONCAT_WS(" ",`users`.`username`,`users`.`surname`)');


    }

    public  function getStudentOnCourseForProgress($id_course,$paginationOption,$progressDeleted,$id_curator = false){



        $criteria = $this->scopeGetStudentOnCourse($id_course);

        $criteria['multijoin'][] = array(
                    array( 'l' => 'lesson' ),
                    'p.id_lesson= l.id',
                    array(
                        'number' => 'number',
                        'id_lesson'=>'id'
                    ),
            );
        if($id_curator){

            $criteria['multijoin'][] =   array(
                array('sc'=>'student_curator'),
                'sc.id_student = p.id_student AND sc.id_course = p.id_course',
                array()
            );
            $criteria['where'][]  = array(
                'users.deleted'=>'0',
                'p.deleted'=>$progressDeleted,
                'sc.id_curator'=>$id_curator
            );
        }else{

            $criteria['where'][]  = array(
                'users.deleted'=>'0',
                'p.deleted'=>$progressDeleted,

            );

        }
        if(!empty($paginationOption->search)){
            $criteria['where'][] = $this->getSearchByAuthWhere($paginationOption->search);
        }

        $criteria['order'] = $paginationOption->orderBy.' '. $paginationOption->order;

        $this->setCriteria($criteria, false);

//        print_r($this->getSQLByCriteria());
//        exit;
        $paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect($this->getSelectByCriteria(), $this->adapter);

        $statement = $this->sql->getSqlStringForSqlObject( $this->getSelectByCriteria() );

        $paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
        $paginator->setDefaultItemCountPerPage($paginationOption->limit);
        $paginator->setPageRange(10);
        $paginator->setCurrentPageNumber($paginationOption->page);

        return $paginator;


    }




    /**
     * Get binary code by role name
     * @param string $roleName
     * @return integer
     */
    public function getBinaryCodeByRoleName($roleName)
    {
        switch ($roleName) {
            case 'student':
                return 1;
            case 'curator':
                return 2;
            case 'author':
                return 4;
        }
        return 1;
    }

    /**
     * Get available user role names
     * @return array
     */
    public static function getAvailableUserRoleNames()
    {
        return array(
            'author',
            'curator',
            'student',
        );
    }

    /**
     * Get available user role labels
     * @return array
     */
    public static function getAvailableUserRoleLabels()
    {
        return array(
            'author'  => 'автор',
            'curator' => 'наставник',
            'student' => 'ученик',
        );
    }

    /**
     * Get old email
     * @return string
     */
    public function getOldEmail()
    {
        return isset($this->nativeAttributes['email']) ? $this->nativeAttributes['email'] : null;
    }

    /**
     * Generate password
     * @return string
     */
    public static function generatePassword()
    {
        $secretKey = '';
        $keySet  = "qwe1r2t3y4u5i6o7p8a9s0dfghjklzxcvbbbnmQ1W2E3R4T5Y6U7I8O9P0ASDFGHJKLZXCVBNM";//генерируем ключ для шифрования
        // @TODO edit length
        for ($i=0; $i < 8; $i++)
            $secretKey.= substr($keySet, rand(0, strlen($keySet)-1), 1);

        return $secretKey;
    }

    /**
     * @return boolean if user is deleted
     */
    public function isDeleted()
    {
        return (boolean) ($this->deleted == 1);
    }

}
