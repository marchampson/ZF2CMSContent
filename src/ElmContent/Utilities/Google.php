<?php
namespace ElmContent\Utilities;

/**
 * 
 * @author marchampson
 *
 */
class Google
{
    protected $mapsApiKey = 'ABQIAAAAWnmHZCgm38m_z5PsEe-YSBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxT7dAuLqNIKzBYypbbT_mraehVa4Q';
    
    public function setMapsApiKey($mapsApiKey)
    {
        $this->mapsApiKey = $mapsApiKey;
    } 
    
    public function __construct()
    {
    }
    
    public function GetLatLong($postcode){
        
        $postcode = str_replace(" ", "", trim($postcode));
    
        $query = "http://maps.google.com/maps/geo?q=".$postcode."&output=json&key=".$mapsApiKey;
    
        $data = file_get_contents($query);
    
        $obj = json_decode($data);
    
        if(is_object($obj)) {
            $long = $obj->Placemark[0]->Point->coordinates[0];
            $lat = $obj->Placemark[0]->Point->coordinates[1];
            return array('Latitude'=>$lat,'Longitude'=>$long);
        } else {
            return false;
        }
    }
}