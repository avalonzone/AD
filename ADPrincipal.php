<?php
class ADPrincipal extends ADObject
{
    public string $samAccountName;
    public string $objectSID;
    public string $SID;
    public array $memberOf = [];
    
    // Constructor $identity parameter takes ADObject, GUID, SID, DN, SAM, ADFilter
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = [], $ldapConnection = null, bool $defaultProperties = true)
    {    
        if(is_a($identity, "ADObject", true))
        {
            parent::__construct($identity->objectGUID, $searchBase, $searchScope, $properties, $ldapConnection, $defaultProperties);
        }
        elseif(is_string($identity) && preg_match(ADConfig::REGEX_MATCH_SID, $identity))
        {
            $ADFilterAttributeObjectSID = new ADFilterAttribute("objectsid", $identity);
            $ADFilterObjectSID = new ADFilter($ADFilterAttributeObjectSID);
            $identity = $ADFilterObjectSID;
            parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection, $defaultProperties);
        }
        elseif(is_string($identity) && !preg_match(ADConfig::REGEX_MATCH_DN, $identity) && !ADGUID::isGUID($identity))
        {
            $ADFilterAttributeSamAccountName = new ADFilterAttribute("samaccountname", $identity);
            $ADFilterAttributeSamAccountName = new ADFilter($ADFilterAttributeSamAccountName);
            $identity = $ADFilterAttributeSamAccountName;
            parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection, $defaultProperties);
        }
        else
        {
            parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection, $defaultProperties);
        }
    }
}