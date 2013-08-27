<?php
namespace ElmContent\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use ElmContent\Model\Pages;

class PagesTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
   
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function fetch($select)
    {
    	$result = $this->tableGateway->select($select);
    	return $result;
    }
    
    public function getPage($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
            
        }
        return $row;
    }
    
    public function getPageByPrettyUrl($prettyurl, $namespace = 'null')
    {
        $selectArray = array('prettyurl' => $prettyurl);
        if($namespace != null) {
            $selectArray['namespace'] = $namespace;
        }
        
        $rowset = $this->tableGateway->select($selectArray);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find page via prettyurl: $prettyurl");
        }
        return $row;
    }
    
    public function getPageByPrettyUrlAndStartDate($prettyurl, $namespace = null, $start_date = null)
    {
        $selectArray = array('prettyurl' => $prettyurl);
        if($namespace != null) {
            $selectArray['namespace'] = $namespace;
        }
        if($start_date != null) {
            $selectArray['start_date'] = $start_date;
        }
        
        $rowset = $this->tableGateway->select($selectArray);
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find page via prettyurl: $prettyurl");
        }
        return $row;
    }
    
    public function getPageByName($name)
    {
        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        if (!$row) {
            //throw new \Exception("Could not find page via name: $name");
            echo "Could not find page via name: $name";
        }
        return $row;
    }
    
    public function savePage($item, $namespace = 'webpage')
    {
        // Set known page fields into data
        $data = array(
                'parent_id' => $item->parent_id,
                'namespace' => $namespace,
                'name' => $item->name,
                'prettyurl' => $item->prettyurl,
        		'start_date' => $item->start_date,
        		'end_date' => $item->end_date
                
        );

        $id = (int)$item->id;
        if ($id == 0) {
            $data['date_created'] = time();
            $data['created_by'] = 'hal9000';
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getPage($id)) {
                $data['date_modified'] = time();
                $data['modified_by'] = 'hal9000';
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        
        return $id;
    }

    public function deletePage($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}