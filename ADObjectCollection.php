<?php
class ADObjectCollection extends ArrayObject
{    
        
    public function __construct(ADFilter $ADFilter, string $searchBase = ADConfig::DEFAULT_DN, int $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null, string $objectClass = "", bool $defaultProperties = true)
    {
        
        $ldapConnection = ADUtils::getConnection();
                
        $ADObjects = array();
        
        if(empty($objectClass))
        {
            $ADEntities = self::getADObjectCollection($ADFilter, $searchBase, $searchScope, array("objectguid"), $ldapConnection);
            
            foreach ($ADEntities as $ADEntity)
            {
                if(isset($ADEntity['objectguid']))
                {
                    $identity = new ADGUID($ADEntity['objectguid'][0]);
                    $ADObjects[$identity->hexGUID] = new ADObject($identity, $searchBase, $searchScope, $properties, $ldapConnection);
                }
            }
        }
        else
        {            
            if(class_exists($objectClass))
            {
                $properties = $objectClass::joinProperties($properties, $defaultProperties);
                
                $ADEntities = self::getADObjectCollection($ADFilter, $searchBase, $searchScope, $properties, $ldapConnection);
                
                foreach ($ADEntities as $ADEntity)
                {
                    if(isset($ADEntity['objectguid']))
                    {
                        $identity = new ADGUID($ADEntity['objectguid'][0]);
                        $ADObjects[$identity->hexGUID] = $ADEntity;
                    }
                }
            }
            else
            {
                throw new Exception("No class found matching '" . $objectClass . "'");
            }
                      
        }
                
        parent::__construct($ADObjects);
    }
    
    public static function getADObjectCollection(ADFilter $ADFilter, string $searchBase = ADConfig::DEFAULT_DN, int $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null)
    {
                
        if(is_null($ldapConnection))
        {
            $ldapConnection = ADUtils::getConnection();
        }
        
        switch($searchScope)
        {
            case ADConfig::SEARCH_SCOPE_ONELEVEL :
                $searchResult = ldap_list($ldapConnection, $searchBase, $ADFilter->__tostring(), $properties);
                break;
            case ADConfig::SEARCH_SCOPE_SUBTREE :
                //"(&(name=*)(objectcategory=group)(!(name=*-SG-File-*))(!(name=*-RG-MBX-*))(!(name=*-RG-*))(!(name=*-SG-File-*))(!(name=*-SG-GP-*)))" Must Be
                $searchResult = ldap_search($ldapConnection, $searchBase, $ADFilter->__tostring(), $properties);
                break;
        }
        
        if (!$searchResult)
        {
            //throw new Exception("No match for filter '" . $ADFilter . "' in " . $searchBase . " with search scope option " . $searchScope );
            return array();
        }
        
        $entries = ldap_get_entries($ldapConnection, $searchResult);
                
        if ($entries['count'] > 0)
        {
            return $entries;
        }
        else
        {
            //throw new Exception("No identity matching filter '" . $ADFilter . "'");
            return array();
        }
    } 
}