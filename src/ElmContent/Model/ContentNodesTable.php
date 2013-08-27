<?php
namespace ElmContent\Model;

use Zend\Db\TableGateway\TableGateway;

class ContentNodesTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($pageId, $language = 'en')
    {
        $resultSet = $this->tableGateway->select(array('page_id' => $pageId, 'language' => $language));
        return $resultSet;
    }
    
    public function fetch($select)
    {
    	$result = $this->tableGateway->select($select);
    	return $result->toArray();
    }

    public function getContentNode($pageId, $node)
    {
        $pageId  = (int) $pageId;
        $rowset = $this->tableGateway->select(array('page_id' => $pageId, 'node' => $node));
        $row = $rowset->current();
        return $row;
    }

    public function saveContentNode($pageId, $node, $content, $language = 'en')
    {
        // Set values to data array
        $data = array(
                'page_id' => $pageId,
                'node' => $node,
                'content' => $content,
                'language' => $language,
        );

        $row = $this->getContentNode($pageId, $node);
        if ((int) $row['id']) {
            $this->tableGateway->update($data, array('id' => $row['id']));
        } else {
            $this->tableGateway->insert($data);
        }
        
    }    
}