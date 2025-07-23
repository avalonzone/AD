<?php
class ADRecipient extends ADObject
{
    public const REGEX_MAIL_ADDRESS = "/^[a-zA-Z0-9.!#$%&_-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
    
    public string $mail;
    public array $proxyAddresses;
    
    public function __construct($identity, $searchBase = ADConfig::DEFAULT_DN, $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $properties = array(), $ldapConnection = null)
    {
        if(is_string($identity) && self::isEmail($identity))
        {
            $mailADFilterAttribute = new ADFilterAttribute("mail", $identity);
            $proxyADAddressesFilterAttribute = new ADFilterAttribute("proxyAddresses", "smtp:" . $identity);
            $mailADFilter = new ADFilter(array($mailADFilterAttribute,$proxyADAddressesFilterAttribute));
            parent::__construct($mailADFilter, $searchBase, $searchScope, $properties, $ldapConnection);
        }
        else
        {
            // Not working yet, class inheritances is missing steps
            parent::__construct($identity, $searchBase, $searchScope, $properties, $ldapConnection);
        }
    }
    
    public static function isEmail(string $mailAddress) : bool
    {
        if(preg_match(self::REGEX_MAIL_ADDRESS, $mailAddress))
        {
           return true; 
        }
        return false;
    }
    
    public static function isInUse(string $mailAddress) : bool
    {
            $mailADFilterAttribute = new ADFilterAttribute("mail", $mailAddress, ADConfig::SEARCH_FILTER_TYPE_OR);
            $proxyADAddressesFilterAttribute = new ADFilterAttribute("proxyAddresses", "smtp:" . $mailAddress, ADConfig::SEARCH_FILTER_TYPE_OR);
            $mailADFilter = new ADFilter(array($mailADFilterAttribute,$proxyADAddressesFilterAttribute));
                        
            if(count(ADUtils::getADObject($mailADFilter)) > 0 )
            {
                return true;
            }
            return false;
    }
}