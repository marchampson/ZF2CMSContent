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

class FormImageList extends AbstractHelper
{
    public function __invoke(ElementInterface $element)
    {
        //$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $pageId = $element->getPageId();

        $html = '
        <style>
        tr {
            border-bottom: 1px solid #e0e0e0;
        }
        </style>
        <table class="spreadsheet" cellspacing="0" cellpadding="0">
        <tr><td></td><td></td><td></td><td><strong>Alt text</strong></td><td><strong>Delete</strong></td></tr>
        <tbody id="imagelist" class="ui-sortable" style="">
        
        ';
                        
        /**
         * Get multiple images
         */
        $imageDir = ($element->getImageDir() != '') ? $element->getImageDir() . '/' : '';

        $imageDirPath = dirname(__DIR__).'/../../../../../../public/images/upload/'. $imageDir . $pageId;

        $images = $element->getImages();

        if (count($images) > 0) {
            $imagesArr = array();
            foreach($images as $image) {
                $imagesArr[] = array('image' => $image->image, 'alt_text' => $image->alt_text);
            }
            $jsonImageArr = array();
            if(count($imagesArr)>0) {
                foreach($imagesArr as $image) {
                    
                    $imagePath = dirname(__DIR__).'/../../../../../../public/images/upload/'.$imageDir . $pageId.'/'.$image['image'];
                    list($width, $height, $type, $attr)= getimagesize($imagePath);
                    $html .= '
                    <tr class="row">
                    <td width="70">
                    <span class="id hide">'.$pageId.'</span>
                    <img width="50" height="50" src="/images/upload/'.$imageDir . $pageId.'/'.$image['image'].'">
                    </td>
                    <td>
                    <span class="edit">'.$image['image'].'</span>
                    </td>
                    <td>'.$image['alt_text'].'</td>
                    <td width="45">
                    <input type="checkbox" name="rm_img<>'.$image['image'].'" value="'.$image['image'].'" />
                    </td>
                    </tr>
                    ';
                    //$jsonImageArr[] = '<img src="/images/upload/'.$id.'/'.$image.'" alt="'.$image.'" width="'.$width.'" height="'.$height.'" />';
                }
            }
        }  
            $html .= '
                      </tbody>
                      </table>
                      <br />
        ';

        return $html;
        
    }
}