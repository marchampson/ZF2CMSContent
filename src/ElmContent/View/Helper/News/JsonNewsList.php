<?php
namespace ElmContent\View\Helper\News;

use Zend\View\Helper\AbstractHelper;


class JsonNewsList extends AbstractHelper
{
    protected $pagesTable;
    protected $contentNodesTable;
    
    public function __invoke()
    {
        $newsArticles = $this->pagesTable->fetch(array('namespace' => 'news'));
        $listArray = array();
        foreach($newsArticles as $article) {
            $contentNodes = $this->contentNodesTable->fetchAll($article->id, 'en');
            foreach($contentNodes as $node) {
                ${$node['node']} = $node['content'];
            }
            if(strtolower($status) == 'live') {
                $image = (isset($list_image)) ? $list_image : '';
                $url = (isset($clickthrough)) ? $clickthrough : '';
                $list_date = (isset($date)) ? $date : '';
                if($list_date != '') {
                    $datetime = strtotime($list_date);
                    $list_date = date('F',$datetime) . ' ' . date('jS',$datetime) . ' ' . date('Y',$datetime);
                }
                $text = (isset($brief)) ? $brief : '';
                $listArray[] = array(
                        'image' => $image,
                        'url' => $url,
                        'date' => $list_date,
                        'text' => $text
                );
                unset($list_image);
                unset($clickthrough);
                unset($date);
                unset($brief);
            }                       
        }
        return json_encode($listArray);
    }
    
    public function setPagesTable($sm, $tableModel)
    {
        $this->pagesTable = $sm->get($tableModel);
        return $this->pagesTable;
    }
    
    public function setContentNodesTable($sm, $tableModel)
    {
        $this->contentNodesTable = $sm->get($tableModel);
        return $this->contentNodesTable;
    }
}