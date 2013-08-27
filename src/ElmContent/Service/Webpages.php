<?php
namespace ElmContent\Service;
use ElmContent\Model\Webpage;
class Webpages
{
	public function createWebpage(Webpage $webpage)
	{
	    
		$this->someEvent()->trigger('webpage.create', $webpage);
		return $webpage;
	}
}