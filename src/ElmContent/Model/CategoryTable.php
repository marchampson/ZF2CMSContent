<?php
namespace ElmContent\Model;

use Zend\Db\TableGateway\TableGateway;

class CategoryTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetch($select)
    {
        $result = $this->tableGateway->select($select);
        return $result;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
	
	public function getCategory($id)
	{
	    $id = (int) $id;
	    $rowset = $this->tableGateway->select(array('id' => $id));
	    $row = $rowset->current();
	    if(!$row) {
	        throw new \Exception("Could not find row $id");
	    }
	    return $row;
	}
	
	public function getCategoryByName($name)
	{
	    $rowset = $this->tableGateway->select(array('name' => $name));
	    $row = $rowset->current();
	    if(!$row) {
	        //throw new \Exception("Could not find row $name");
	    }
	    return $row;
	}
	
	public function getCategoryByPrettyUrl($prettyurl)
	{
	    $rowset = $this->tableGateway->select(array('prettyurl' => $prettyurl));
	    $row = $rowset->current();
	    return $row;
	}
	
	public function saveCategory(Category $category)
	{
	    $data = array(
	            'name' => $category->name,
	            'title' => $category->title,
	            'prettyurl' => $category->prettyUrl,
	            'parent_id' => $category->parent_id,
	            'image' => $category->image,
	            'description' => $category->description,
	            'url' => $category->url,
                    'status' => $category->status
	    );
	    
	    $id = (int)$category->id;
	    if($id == 0) {
	        $this->tableGateway->insert($data);
	    } else {
	        if($this->getCategory($id)) {
	            $this->tableGateway->update($data, array('id' => $id));
	        } else {
	            throw new \Exception('Form id does not exist');
	        }
	    }
	}
	
	public function deleteCategory($id)
	{
	    $this->tableGateway->delete(array('id' => $id));
	}
}