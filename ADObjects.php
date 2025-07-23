<?php
class ADObjects extends ArrayObject
{    
    public function __construct(ADFilter $ADFilter, string $searchBase = ADConfig::DEFAULT_DN, int $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null)
    {
        $ldapConnection = ADUtils::getConnection();
        
        $ADEntities = self::getADObjects($ADFilter, $searchBase, $searchScope, array("objectguid"), $ldapConnection);
   
        $ADObjects = array();
   
        foreach ($ADEntities as $ADEntity)
        {
            if(isset($ADEntity['objectguid']))
            {
                $identity = new ADGUID($ADEntity['objectguid'][0]);        
                $ADObjects[$identity->hexGUID] = new ADObject($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            }          
        }

        parent::__construct($ADObjects);
    }
    
    public static function getADObjects(ADFilter $ADFilter, string $searchBase = ADConfig::DEFAULT_DN, int $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null)
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
                $searchResult = ldap_search($ldapConnection, $searchBase, $ADFilter->__tostring(), $properties);
                break;
        }
        
        if (!$searchResult)
        {
            throw new Exception("No match for filter '" . $ADFilter . "' in " . $searchBase . " with search scope option " . $searchScope );
        }
        
        $entries = ldap_get_entries($ldapConnection, $searchResult);
        
        if ($entries['count'] > 0)
        {
            return $entries;
        }
        else
        {
            throw new Exception("No identity matching filter '" . $ADFilter . "'");
        }
    } 
}