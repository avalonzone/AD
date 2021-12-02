<?php
class ADAccount extends ADPrincipal
{
    public bool $enabled;
    public string $userPrincipalName;
    public string $userAccountControl;
    public bool $lockedOut;
    public bool $cannotChangePassword;
    public bool $passwordNeverExpires;
    public bool $passwordExpired;
    public bool $passwordNotRequired;
    public bool $smartcardLogonRequired;
    
    public bool $trustedForDelegation;
    public bool $trustedToAuthForDelegation;
    public bool $useDESKeyOnly;
    public bool $mNSLogonAccount;
    public bool $homedirRequired;
    public bool $doesNotRequirePreAuth;
        
    // Constructor $identity parameter takes ADObject, GUID, SID, DN, SAM, ADFilter
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null)
    {
        parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
    }
    
}