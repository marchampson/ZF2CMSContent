<?php
namespace ElmContent\Utilities;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter as Adapter;

/**
 *
 * @author marchampson
 *        
 */
class PageUtils implements ServiceLocatorAwareInterface
{

    private $namespace;
    protected $sortableParentId = 0;
    protected $indentSpace;
    protected $pagesTable;
    protected $contentNodesTable;
    protected $serviceLocator;

    public function getServiceLocator ()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __construct ()
    {}

    public function setNamespace ($namespace)
    {
        $this->namespace = $namespace;
    }
    
    public function getPagesTable ()
    {
        if (! $this->pagesTable) {
            $sm = $this->getServiceLocator();
            $this->pagesTable = $sm->get('ElmContent\Model\PagesTable');
        }
        return $this->pagesTable;
    }

    public function getContentNodesTable ()
    {
        if (! $this->contentNodesTable) {
            $sm = $this->getServiceLocator();
            $this->contentNodesTable = $sm->get(
                    'ElmContent\Model\ContentNodesTable');
        }
        return $this->contentNodesTable;
    }

    function buildSearchQuery ($parentId = 0)
    {
        // Retrieve or define the filter variables
        $this->sort = array(
                'position' => 'asc'
        );
        /*
         * if ($this->params()->fromPost('sort') != '') { $sortBits = explode('
         * ', $this->params()->fromPost('sort')); $this->sort = array(
         * $sortBits[0] => $sortBits[1] ); } // Search if
         * ($this->params()->fromPost('search') != '') { $search =
         * $this->params()->fromPost('search'); // Place keyword(s) in an array.
         * $this->keywords = explode(' ', $search); }
         */
        // Build Search
        // Pages table
        $sql = 'select t1.id, t1.name, t1.parent_id';
        
        // Additional selects and joins for each node
        // Get list nodes from config or default
        if (isset($config[$this->namespace]['list']['nodes']) &&
                 is_array($config[$this->namespace]['list']['nodes'])) {
            $this->nodes = $config[$this->namespace]['list']['nodes'];
        } else {
            $this->nodes = array(
                    'status'
            );
        }
        if (count($this->nodes) > 0) {
            $i = 2;
            foreach ($this->nodes as $node) {
                $sql .= ", t$i.$node";
                $i ++;
            }
        }
        
        $sql .= ' from (select id, name, parent_id, position from pages where namespace = "' .
                 $this->namespace . '" and parent_id = ' . $parentId;
        /*
         * if (isset($this->keywords) && count($this->keywords) > 0) { $sql .=
         * 'and ('; $i = 0; foreach ($this->keywords as $keyword) { if ($i > 0)
         * { $sql .= ' or '; } $sql .= ' name like "%' . $keyword . '%" '; $i
         * ++; } $sql .= ')'; }
         */
        $sql .= ') t1 ';
        
        // Inner Join for every node
        if (count($this->nodes) > 0) {
            $i = 2;
            foreach ($this->nodes as $node) {
                $sql .= ' inner join (select content as ' . $node .
                         ', page_id from content_nodes where node = "' . $node .
                         '") t' . $i . ' on t1.id = t' . $i . '.page_id';
                $i ++;
            }
        }
        
        if (count($this->sort) > 0) {
            $sql .= ' order by ';
            foreach ($this->sort as $k => $v) {
                $sql .= "$k $v ";
            }
        }
        return $sql;
    }
    
    function getPagesDataArray()
    {
        // Get parent pages
        $sql = $this->buildSearchQuery();
        // Run query
        //$adapter = new Adapter($config['db']);
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $pages = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        
        $pagesArray = array();
        foreach($pages as $page) {
            $pagesArray[] = array(
                    'rowId' => $page->id,
                    'heading' => array(
                            array('type' => 'state',
                                    'state' => array(
                                            array('value'=>strtolower($page->status),
                                                    'type' => 'select',
                                                    'options' => array('draft', 'live', 'private')
                                            ),
                                            array('value' => 'featured-home', 'type' => 'status')
                                    ),
                                    'span' => 2
        
                            ),
                            array('value' => $page->name, 'type' => 'string', 'span' => 5),
                            array('type' => 'actions',
                                    'span' => 3,
                                    'actions' => array(
                                            array('url' => '/elements/'.$this->namespace.'/edit/'.$page->id,
                                                    'type' => 'edit',
                                                    'text' => 'Edit'),
                                            array('url' => '/elements/'.$this->namespace.'/clone/'.$page->id,
                                                    'type' => 'clone',
                                                    'text' => 'Clone'),
                                            array('url' => '/elements/'.$this->namespace.'/delete/'.$page->id,
                                                    'type' => 'delete',
                                                    'text' => 'Delete')
                                    ),
        
                            )
                    ),
                    'sortableList' => $this->sortableList($page->id),
        
            );
             
        }
        return $pagesArray;
    }

    function sortableList ($id)
    {
        // Get child pages
        $sql = $this->buildSearchQuery($id);
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $pages = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        
        // Foreach child page check if it has children
        $sortable = array();
        //
        foreach ($pages as $page) {
            $this->indentSpace = '';
            if ($id == $page->parent_id) {
                $this->sortableParentId = $page->parent_id;
                $this->indentSpace .= '&nbsp;&nbsp;&nbsp;';
            }
            $sortable[] = array(
                    'rowId' => $page->id,
                    'heading' => array(
                            array(
                                    'type' => 'state',
                                    'state' => array(
                                            array(
                                                    'value' => strtolower(
                                                            $page->status),
                                                    'type' => 'select',
                                                    'options' => array(
                                                            'draft',
                                                            'live',
                                                            'private'
                                                    )
                                            ),
                                            array(
                                                    'value' => 'featured-home',
                                                    'type' => 'status'
                                            )
                                    ),
                                    'span' => 2
                            )
                            ,
                            array(
                                    'value' => $page->name,
                                    'type' => 'string',
                                    'indent' => $this->indentSpace,
                                    'span' => 5
                            ),
                            array(
                                    'type' => 'actions',
                                    'actions' => array(
                                            array(
                                                    'url' => '/elements/'.$this->namespace.'/edit/' .
                                                             $page->id,
                                                            'type' => 'edit',
                                                            'text' => 'Edit'
                                            ),
                                            array(
                                                    'url' => '/elements/'.$this->namespace.'/clone/' .
                                                     $page->id,
                                                    'type' => 'clone',
                                                    'text' => 'Edit'
                                            ),
                                            array(
                                                    'url' => '/elements/'.$this->namespace.'/delete/' .
                                                     $page->id,
                                                    'type' => 'delete',
                                                    'text' => 'Delete'
                                            )
                                    )
                                    ,
                                    'span' => 3
                            )
                    ),
                    'sortableList' => $this->sortableList($page->id)
            );
        }
        return $sortable;
    }
    
    
}