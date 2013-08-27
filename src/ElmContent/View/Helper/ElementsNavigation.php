<?php
namespace ElmContent\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

class ElementsNavigation extends AbstractHelper
{
    public function __invoke()
    {
        // Get current authenticated user
        $authService = new AuthenticationService();
        if ($authService->hasIdentity()) {
          $identity = $authService->getIdentity();
          $role = strtolower($identity->role);
        }
        $html = '<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">WEBSITE <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="/elements/page">Pages</a></li>
                      <li><a href="/elements/news">News</a></li>
                      <li><a href="/elements/event">Events</a></li>
                      <li><a href="/elements/banner">Banner Ads</a></li>
                      <li><a href="/elements/category">Categories</a></li>
                    </ul>
                  </li>';
        if(isset($role) && ($role == 'admin' || $role == 'hal9000')) {
          $html .= '<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">SETTINGS <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="/elements/admin/user">Users</a></li>
                      <li><a href="/elements/admin/group">Groups</a></li>';
                      if($role == 'hal9000') {
                        $html .= '<li><a href="/elements/admin/form">Forms</a></li>';
                      }
          $html .= '                      
                    </ul>
                  </li>'; 
        }
        
        return $html;
    }
}