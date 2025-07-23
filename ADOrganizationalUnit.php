<?php
class ADOrganizationalUnit extends ADObject
{
    public string $city;
    public string $country;
    public string $postalCode;
    public string $state;
    public string $streetAddress;
    
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null)
    {
        parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
    }
}