<?php
namespace ElmContent\View\Helper;
/*
 * @description 
 */

use Zend\View\Helper\AbstractHelper;

class Gravatar extends AbstractHelper
{
    
    protected $_url = 'http://www.gravatar.com/avatar.php';

    public function __invoke($email, $rating = 'G', $size = 48)
    {
        $params = array(
            'gravatar_id' => md5(strtolower($email)),
            'rating'      => $rating,
            'size'        => $size,
        );

        return $this->_url . '?' . http_build_query($params, '', '&amp;');
    }

    
}