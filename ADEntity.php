<?php
class ADEntity extends ADPropertyCollection
{           
    // Constructor $identity parameter takes GUID, ADFilter, Array (ldap result array)
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null, bool $defaultProperties = true)
    {   
        if($defaultProperties){
            $properties = self::joinProperties($properties, $defaultProperties);
        }else{
            $properties = array("objectguid");
        }
        if(is_string($identity))
        {
            if(ADGUID::isGUID($identity))
            {
                $identity = new ADGUID($identity);
                parent::__construct(self::getADEntity($identity->hexADFilterGUID, $searchBase, $searchScope, $properties, $ldapConnection));
            }
        }
        else
        {
            if(is_array($identity))
            {
                parent::__construct($identity);
            }
            elseif(is_a($identity, "ADGUID"))
            {
                parent::__construct(self::getADEntity($identity->hexADFilterGUID, $searchBase, $searchScope, $properties, $ldapConnection));
            }
            elseif(is_a($identity, "ADFilter"))
            {
                parent::__construct(self::getADEntity($identity, $searchBase, $searchScope, $properties, $ldapConnection));
            }
            else
            {
                throw new Exception("No identity matching the given identity '" . strval($identity) . "'");
            }
        }     
    }
        
    public static function joinProperties($customProperties, $defaultProperties = true)
    {
        
        if($defaultProperties){
            $properties = self::getProperties();
        }else{
            $properties = array("objectguid");
        }
        
        foreach($customProperties as $property)
        {
            if(!in_array(strtolower($property), $properties))
            {
                array_push($properties, $property);
            }
        }
        return $properties;
    }
       
    /*
     *  $pageSize = 100;
        $cookie = '';
        $results = array();
        do {
          ldap_control_paged_result($ds, $pageSize, true, $cookie);
        
          $result  = ldap_search($ds, $dn, $filter, $justthese);
          $entries = ldap_get_entries($ds, $result);
        
          foreach ($entries as $e) {
            $results[] = $e;
          }
        
          ldap_control_paged_result_response($ds, $result, $cookie);
        
        } while($cookie !== null && $cookie != '');
    */
    
    public static function getADEntity(ADFilter $ADFilter, string $searchBase = ADConfig::DEFAULT_DN, int $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null)
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
            
            return $entries[0];
        }
        else
        {
            throw new Exception("No identity matching filter '" . $ADFilter . "'");
        }
    }
    
    public function getJSON() : string
    {
        return json_encode($this);
    }
}