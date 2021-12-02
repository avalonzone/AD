<?php
class ADUser extends ADAccount
{
    public string $givenName;
    public string $surname;
    
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null)
    {
        parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
    }
}