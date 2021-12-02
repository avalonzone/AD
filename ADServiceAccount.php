<?php
class ADServiceAccount extends ADAccount
{
    public array $servicePrincipalNames;
    
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null)
    {
        parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
    }
}