<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ElmContent\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

class FormPage extends AbstractHelper
{
    public function __invoke(ElementInterface $element)
    {
        $html = '
        <table class="table table-hover">
        <tbody>
        <tr>
            <th>Name</th>
            <th>Select<th>
        <tr>';
    
        if(count($element->getValueOptions())> 0) {

            foreach($element->getValueOptions() as $k => $v) {
                $checked = ($v['checked'] == 1) ? 'checked = "yes"' : '';
                $html .= '<tr>
                            <td>'.$v['name'].'</td>
                            <td><input type="checkbox" name="webpage_assoc_'.$v['id'].'" '.$checked.'></td>
                          </tr>';
            }
        }

        $html .= '
            </tbody>
        </table>';
        
        return $html;
        
    }
}