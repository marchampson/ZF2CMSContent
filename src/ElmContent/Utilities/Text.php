<?php
namespace ElmContent\Utilities;

class Text
{
    public function __construct()
    {
        
    }
    public function prettyUrl($str)
    {
        $str = str_replace('ë','e', $str); // Remove ë
        $str = str_replace('á','a', $str); // Remove á
        $str = str_replace('.','',$str); // Remove full-stop
        $str = str_replace("'","",$str); // Remove apostrophes
        $str = str_replace(":","",$str); // Remove colons
        $str = str_replace(";","",$str); // Remove semi-colons
        $str = str_replace(",","",$str); // Remove commas
        $str = strtolower(str_replace(' ','-',$str)); // lowercase
        return $str;
    }
    
    
}