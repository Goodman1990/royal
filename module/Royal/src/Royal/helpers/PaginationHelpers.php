<?php
namespace Royal\helpers;

class PaginationHelpers
{
    public $search = '';
    public $page = 1;
    public $limit = 10;
    public $order = 'ASC';
    public $orderBy;

    public function __construct($data = null) {
        if ($data != null) {
            foreach ($data as $key => $value)
                $this->$key = $value;
            if (!is_int($this->page) && $this->page <= 0) $this->page = 1;
            if (!is_int($this->limit) && $this->limit <= 0) $this->limit = 10;
            if (!in_array($this->order,array('ASC','asc','DESC','desc'))) $this->order = 'ASC';
        }
//        echo $this->search;
//        exit;
    }
}
