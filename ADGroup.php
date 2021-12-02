<?php
class ADGroup extends ADPrincipal
{
    public array $members;
    public string $groupCategory;
    public string $groupScope;
      
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null)
    {
        parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
    }
}