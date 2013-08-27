<?php
namespace ElmContent\Model;

use Zend\Db\TableGateway\TableGateway;

class WebpageAssociationsTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function findByWebpageId($pageId, $type)
    {
        $resultSet = $this->tableGateway->select(array('webpage_id' => $pageId, 'type' => $type));
        return $resultSet;
    }
    
    public function findByContentId($contentId)
    {
        $resultSet = $this->tableGateway->select(array('content_id' => $contentId));
        return $resultSet;
    }
	
	public function saveWebpageAssociation($webpageId, $contentId, $type)
	{
	    $data = array(
	            'webpage_id' => $webpageId,
	            'content_id' => $contentId,
	            'type' => $type
	    );
	    
	    $this->tableGateway->insert($data);
	}
	
	public function deleteByContentId($contentId, $type)
	{
        $this->tableGateway->delete(array('content_id' => $contentId, 'type' => $type));
	}
	
	public function deleteByWebpageId($webpageId, $type)
	{   
        $this->tableGateway->delete(array('webpage_id' => $webpageId, 'type' => $type));
	}
}