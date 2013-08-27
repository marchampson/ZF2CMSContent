<?php
namespace ElmContent\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class ElmListFilter extends AbstractHelper
{
	protected $request;
	
	protected $namespace;
	protected $sort;
	protected $search;
	
	public function __construct(Request $request, $namespace = 'webpage')
	{
		$this->request = $request;
		
		$this->namespace = $namespace;
		
		if($this->request->getPost('sort') != '') {
			$this->sort = $sort;
		}

		if($this->request->getPost('search') != '') {
			$this->search = $search;
		}
	}
	
    public function __invoke()
    {
        $html = '<div>
					<form class="navbar-search pull-left form-inline" method="post"
						action="">
						<select id="sort" name="sort">
							<option value="name asc">Name A-Z</option>
							<option value="name desc">Name Z-A</option>
						</select> <input type="text" class="search-query" name="search"
							placeholder="Search">
						<button type="submit" class="btn btn-primary" id="filter">Submit</button>
        				<a href="/elements/content/item/list/'.$this->namespace.'"><button
							class="btn" type="button">Reset</button></a>
					</form>
					<a href="/elements/content/item/add/'.$this->namespace.'"><button
							class="btn btn-primary pull-right" type="button">Add New</button></a>
				</div>';
        
        return $html;
    }
}