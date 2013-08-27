<?php
namespace ElmContent\Model;

use Zend\Db\TableGateway\TableGateway;

class CategoryAssociationsTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function findByPageId($pageId)
    {
        $resultSet = $this->tableGateway->select(array('pages_id' => $pageId));
        return $resultSet;
    }
    
    public function findByCategoryId($categoryId)
    {
        $resultSet = $this->tableGateway->select(array('category_id' => $categoryId));
        return $resultSet;
    }
	
	public function saveCategoryAssociation($categoryId, $pageId, $type, $parentId = null)
	{
	    $data = array(
	            'category_id' => $categoryId,
	            'pages_id' => $pageId,
	            'parent_id' => $parentId,
	            'type' => $type
	    );
	    
	    $this->tableGateway->insert($data);
	}
	
	public function deleteByCategoryId($categoryId)
	{
        $this->tableGateway->delete(array('category_id' => $categoryId));
	}
	
	public function deleteByPageId($pageId, $type)
	{   
        $this->tableGateway->delete(array('pages_id' => $pageId, 'type' => $type));
	}
}