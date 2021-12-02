<?php
abstract class ADUtils
{
    /**
     * @return resource|boolean
     */
    public static function getConnection()
    {
        $ldapConnection = ldap_connect("ldap://" . ADConfig::DOMAIN_NAME);
        
        if($ldapConnection)
        {
            ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);
            
            if(ldap_bind($ldapConnection, ADConfig::SAM_ACCOUNT_NAME . "@" . ADConfig::DOMAIN_NAME, ADConfig::PASSWORD))
            {
                return $ldapConnection;
            }
        }
        return false;
    }
    
    /**
     * Get domain contoller from DNS entries
     *
     * @param string $domain
     * @return Array
     */
    public static function getDomainController($domain = ADConfig::DOMAIN_NAME) : Array
    {
        return ADRootDSE::getDomainController($domain);
    }
    
    /**
     * Get an ADObject
     *
     * @param mixed $identity (ADFilter, ADGUID, GUID, DN)
     * @param string $searchBase
     * @param string $searchScope
     * @param array $properties
     * @param mixed $ldapConnection
     * @return ADObject | Array
     */
    public static function getADObject($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {            
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            return $ADObjects;
        }
        else
        {
            $ADObject = new ADObject($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            return array($ADObject);
        }
    }
        
    /**
     * Get an ADUser
     *
     * @param mixed $identity (ADFilter, ADGUID, GUID, DN, SID, SAM)
     * @param string $searchBase
     * @param string $searchScope
     * @param array $properties
     * @param mixed $ldapConnection
     * @return ADUser | Array
     */
    public static function getADUser($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {
            
            if(is_null($ldapConnection))
            {
                $ldapConnection = self::getConnection();
            }
                       
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            $ADUsers = array();
            
            foreach($ADObjects as $ADObject)
            {
                $ADUsers[] = new ADUser($ADObject, $searchBase, $searchScope, $properties, $ldapConnection);
            }
            
            return $ADUsers;
        }
        else
        {
            $ADUser = new ADUser($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            return array($ADUser);
        }

        
    }
    
    /**
     * Get an ADGroup (ADFilter, ADGUID, GUID, DN, SID, SAM)
     *
     * @param mixed $identity
     * @param string $searchBase
     * @param string $searchScope
     * @param array $properties
     * @param mixed $ldapConnection
     * @return ADGroup | Array
     */
    public static function getADGroup($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {
            
            if(is_null($ldapConnection))
            {
                $ldapConnection = self::getConnection();
            }
            
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            $ADGroups = array();
            
            foreach($ADObjects as $ADObject)
            {
                $ADGroups[] = new ADGroup($ADObject, $searchBase, $searchScope, $properties, $ldapConnection);
            }
            
            return $ADGroups;
        }
        else
        {
            $ADGroup = new ADGroup($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            return array($ADGroup);
        }
    }
    
    /**
     * Get an ADComputer
     *
     * @param mixed $identity (ADFilter, ADGUID, GUID, DN, SID, SAM)
     * @param string $searchBase
     * @param string $searchScope
     * @param array $properties
     * @param mixed $ldapConnection
     * @return ADComputer  | Array
     */
    public static function getADComputer($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {
            
            if(is_null($ldapConnection))
            {
                $ldapConnection = self::getConnection();
            }
            
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            $ADComputers = array();
            
            foreach($ADObjects as $ADObject)
            {
                $ADComputers[] = new ADComputer($ADObject, $searchBase, $searchScope, $properties, $ldapConnection);
            }
            
            return $ADComputers;
        }
        else
        {
            $ADComputer = new ADComputer($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            return array($ADComputer);
        }
    }
    
    /**
     * Get an ADOrganizationalUnit
     *
     * @param mixed $identity (ADFilter, ADGUID, GUID, DN)
     * @param string $searchBase
     * @param string $searchScope
     * @param array $properties
     * @param mixed $ldapConnection
     * @return ADObject
     */
    public static function getADOrganizationalUnit($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : ADOrganizationalUnit
    {
        //Implement ADEntity search ? Return Arrays if ?
        $ADOrganizationalUnit = new ADOrganizationalUnit($identity, $searchBase, $searchScope, $properties, $ldapConnection);
        return $ADOrganizationalUnit;
    }
    
    // Domain GUID is something appart and out of scope
    // so this function is not working even with the reversed byte GUID...
    public static function getADDomain($domainGUID = ADConfig::DOMAIN_GUID)
    {
        $ADGuid = new ADGUID($domainGUID);
        $ADDomain = new ADObject($ADGuid->hexADFilterAttributeGUID);
        return $ADDomain;
    }
        
    /**
     * Get the nesded memberof of an object
     * If $recurse is set to false, only the first level up is retreived.
     *
     * @param string $distinguishedName
     * @param array $groupList
     * @param array $searchBase
     * @param $ldapConnection
     * @param bool $recurse
     * @return NULL|array
     */
    public static function getMemberOf(string $distinguishedName,array &$groups, string $searchBase = ADConfig::DEFAULT_DN, $ldapConnection = null, $recurse = true)
    {
        return self::getADGenericBidirectionalMembership("memberof", $distinguishedName, $groups, $searchBase, $ldapConnection, $recurse);
    }
    
    /**
     * Get the group membership count for a given ADObject
     *
     * @param ADPrincipal $identity
     * @param return group membership count
     *
     */
    public static function getGroupCount(ADPrincipal $identity) : int
    {
        $groups = array();
        $groupMembership = self::getMemberOf($identity->distinguishedName, $groups);
        return count($groupMembership);
    }
    
    /**
     * Check if the $searchedGroup is in the array $groupList
     *
     * @param ADPrincipal $identity
     * @param array $groups
     * @return boolean
     */
    public static function isMemberOf(ADPrincipal $identity, array $groups = array()) : bool
    {
        if(in_array($identity->distinguishedName, $groups))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Check if there is a group CN matching the given Regex pattern
     *
     * @param ADPrincipal $identity
     * @param string $regexPattern
     * @param array $groups
     * @return boolean
     */
    public static function isMemberOfPaternMatchedGroupName(ADPrincipal $identity, string $regexPattern, array $groups = array()) : bool
    {
        if(count($groups) <= 0)
        {
            self::getMemberOf($identity->distinguishedName, $groups);
        }
        
        foreach($groups as $group)
        {
            $matches = array();
            if(preg_match(Config::REGEX_DN, $group, $matches))
            {
                if(isset($matches["name"]))
                {
                    $name = $matches["name"];
                    if(preg_match($regexPattern, $name))
                    {
                        return true;
                    }
                }
            }   
        }
        return false;
    }
    
    /**
     * Get the nesded member of an object
     * If $recurse is set to false, only the first level down is retreived.
     *
     * @param string $distinguishedName
     * @param array $groupList
     * @param array $searchBase
     * @param $ldapConnection
     * @param bool $recurse
     * @return NULL|array
     */
    public static function getMembers(string $distinguishedName, array &$groupList, string $searchBase = ADConfig::DEFAULT_DN, $ldapConnection = null, $recurse = true)
    {
        return self::getADGenericBidirectionalMembership("member", $distinguishedName, $groupList, $searchBase, $ldapConnection, $recurse);
    }
    
    /**
     * Generic memberOf/members function
     * If $recurse is set to false, only the first level up/down is retreived.
     *
     * @param string $memberAttribute (member | memberOf)
     * @param string $distinguishedName
     * @param array $groupList
     * @param array $searchBase
     * @param $ldapConnection
     * @param bool $recurse
     * @return NULL|array
     */
    public static function getADGenericBidirectionalMembership(string $memberAttribute, string $distinguishedName, array &$groups, string $searchBase = ADConfig::DEFAULT_DN, $ldapConnection = null, $recurse = true)
    {
        if(!$ldapConnection){
            $ldapConnection = self::getConnection();
        }
        
        $distinguishedName = ldap_escape($distinguishedName);
        $searchString = "(distinguishedname={$distinguishedName})";
        
        $result = ldap_search($ldapConnection, $searchBase, $searchString , array("distinguishedname", $memberAttribute));
        
        if (!$result)
        {
            echo "No Results !";
            return null;
        }
        
        $entries = ldap_get_entries($ldapConnection, $result);
        
        if ($entries['count'] > 0)
        {
            if(array_key_exists(strtolower($memberAttribute), $entries[0]))
            {
                
                $items = $entries[0][strtolower($memberAttribute)];
                for($i=0; $i < $items['count'];$i++)
                {
                    if(!in_array($items[$i], $groups))
                    {
                        array_push($groups, $items[$i]);
                        if($recurse)
                        {
                            self::getADGenericBidirectionalMembership($memberAttribute, $items[$i], $groups, $searchBase, $ldapConnection);
                        }
                    }
                }
            }
        }
        sort($groups);
        return $groups;
    }
        
    /**
     * Get adminDescription for a given object
     *
     * @param object|string $identity
     * @param array $properties
     * @return NULL|ADPrincipal
     */
    public static function getADAdminDescription($identity)
    {
        $ADPrincipal = new ADPrincipal($identity, ADConfig::DEFAULT_SEARCH_DN, ADConfig::SEARCH_SCOPE_SUBTREE, array("admindescription"));
        if (property_exists($ADPrincipal, "admindescription"))
            return $ADPrincipal->admindescription;
        return null;
    }
    
    
    /**
     * Get ADObject filtered count limited to a max value
     *
     * @param mixed $identity (ADFilter)
     * @param string $searchBase
     * @param string $searchScope
     * @param mixed $ldapConnection
     * @return int
     */
    public static function getADObjectSearchResultCount(ADFilter $ADFilter, string $searchBase = ADConfig::DEFAULT_DN, int $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $ldapConnection = null, $properties = array("objectguid"))
    {
        if(is_null($ldapConnection))
        {
            $ldapConnection = self::getConnection();
        }
        
        switch($searchScope)
        {
            case ADConfig::SEARCH_SCOPE_ONELEVEL :
                $searchResult = @ldap_list($ldapConnection, $searchBase, $ADFilter->__tostring(), $properties, null, 150);
                break;
            case ADConfig::SEARCH_SCOPE_SUBTREE :
                $searchResult = @ldap_search($ldapConnection, $searchBase, $ADFilter->__tostring(), $properties, null, 150);
                break;
        }
        
        if (!$searchResult)
        {
            throw new Exception("No match for filter '" . $ADFilter . "' in " . $searchBase . " with search scope option " . $searchScope );
        }
        
        return ldap_count_entries($ldapConnection,$searchResult);
        
    }
        
    public static function bin_to_str_sid($binsid)
    {
        $hex_sid = bin2hex($binsid);
        $rev = hexdec(substr($hex_sid, 0, 2));
        $subcount = hexdec(substr($hex_sid, 2, 2));
        $auth = hexdec(substr($hex_sid, 4, 12));
        $result    = "$rev-$auth";
        
        for ($x=0;$x < $subcount; $x++)
        {
            $subauth[$x] = hexdec(ADGUID::reverse_bytes(substr($hex_sid, 16 + ($x * 8), 8)));
            $result .= "-" . $subauth[$x];
        }
        
        return 'S-' . $result;
    }
    
    public static function isUserAccountControlFlagSet(ADAccount $identity, $userAccountControlFlag)
    {
        return isBinaryFlagSet($identity->userAccountControl, $userAccountControlFlag);
    }
    
    public static function isBinaryFlagSet($flags,$flag)
    {
        return ($flags & $flag) == $flag ? true : false;
    }
}
