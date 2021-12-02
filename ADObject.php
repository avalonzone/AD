<?php
class ADObject extends ADEntity
{
    public array $objectClass;
    public string $objectGUID;
    public string $objectADGUID;
    public string $name;
    public string $distinguishedName;
        
    // Constructor $identity parameter takes ADGUID, GUID, DN, ADFilter
    public function __construct($identity, $searchBase = LDAP_Config::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null)
    {                
        if(is_string($identity) && preg_match(Config::REGEX_DN, $identity))
        {
            $ADFilterAttributeDistinguishedName = new ADFilterAttribute("distinguishedname", $identity);
            $ADFilter = new ADFilter($ADFilterAttributeDistinguishedName);
            $identity = $ADFilter;
            parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
        }
        else
        {
            parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
        }      
    }
    
    public function __toString()
    {
        return $this->name;
    }
}