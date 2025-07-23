<?php
abstract class ADUtils
{
    /**
     * @return resource|boolean
     */
    public static function getConnection()
    {
        $ldapConnection = ldap_connect("ldap://" . ADConfig::DOMAIN_CONTROLLER);
        
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
     * @return Array
     */
    public static function getADObject($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {            
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection);
            return $ADObjects->getArrayCopy();
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
     * @return Array
     */
    public static function getADUser($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        $subscope = null;
        if(($searchScope != 1 && $searchScope != 2)){
            $subscope = $searchScope;
            $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE;
        }
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {           
            $ADUserFilterAttibuteObjectCategory = new ADFilterAttribute("objectcategory", "person");
            $ADUserFilterAttributeObjectClass = new ADFilterAttribute("objectclass", "user");
            $identity->addADFilterAttributes(array($ADUserFilterAttibuteObjectCategory, $ADUserFilterAttributeObjectClass));
            
            if(is_null($ldapConnection))
            {
                $ldapConnection = self::getConnection();
            }
                       
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection, "ADUser");
            $ADUsers = array();
            
            foreach($ADObjects as $ADObject)
            {
                if($subscope){
                    if(str_contains($ADObject['distinguishedname'][0], $subscope)){
                        $ADUsers[] = new ADUser($ADObject, $searchBase, $searchScope, $properties, $ldapConnection);
                    }
                }else{
                    $ADUsers[] = new ADUser($ADObject, $searchBase, $searchScope, $properties, $ldapConnection);
                }
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
     * @param bool $defaultProperties show default properties such as objectClass, objectGuid, objectSID, member, SAM, DN, ...
     * @return Array
     */
    public static function getADGroup($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null, $responseObject = null, bool $defaultProperties = true) : Array
    {
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {
            $ADGroupFilterAttibuteObjectCategory = new ADFilterAttribute("objectcategory", "group");
            $identity->addADFilterAttributes(array($ADGroupFilterAttibuteObjectCategory));
            
            if(is_null($ldapConnection))
            {
                $ldapConnection = self::getConnection();
            }
            
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection, "ADGroup", $defaultProperties);
            $ADGroups = array();
            
            foreach($ADObjects as $ADObject)
            {
                $ADGroups[] = new ADGroup($ADObject, $searchBase, $searchScope, $properties, $ldapConnection, $defaultProperties);
            }
            return $ADGroups;
        }
        else
        {
            $ADGroup = new ADGroup($identity, $searchBase, $searchScope, $properties, $ldapConnection, $defaultProperties);
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
     * @return Array
     */
    public static function getADComputer($identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        // If TRUE, multiple search results are expected...
        if(is_a($identity, "ADFilter", true))
        {
            $ADComputerFilterAttibuteObjectCategory = new ADFilterAttribute("objectcategory", "computer");
            $identity->addADFilterAttributes(array($ADComputerFilterAttibuteObjectCategory));
            
            if(is_null($ldapConnection))
            {
                $ldapConnection = self::getConnection();
            }
            
            $ADObjects = new ADObjectCollection($identity, $searchBase, $searchScope, $properties, $ldapConnection, "ADComputer");
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
    
    /**
     * Get an ADUser
     *
     * @param string $identity (ADFilter, ADGUID, GUID, DN, SID, SAM, Email)
     * @param string $searchBase
     * @param string $searchScope
     * @param array $properties
     * @param mixed $ldapConnection
     * @return Array
     */
    public static function getADRecipient(string $identity, string $searchBase = ADConfig::DEFAULT_DN, string $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, array $properties = array(), $ldapConnection = null) : Array
    {
        if(is_null($ldapConnection))
        {
            $ldapConnection = self::getConnection();
        }
        
        $ADRecipient = new ADRecipient($identity, $searchBase, $searchScope, $properties, $ldapConnection);
        return array($ADRecipient);
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
     * Get the nested memberof of an object
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
     * Get the nested memberof of an object
     * If $recurse is set to false, only the first level up is retreived.
     *
     * @param string $distinguishedName
     * @param array $groupList
     * @param array $searchBase
     * @param $ldapConnection
     * @param bool $recurse
     * @return NULL|array
     */
    public static function getMemberOfAsADObject(string $distinguishedName,array &$groups, string $searchBase = ADConfig::DEFAULT_DN, $ldapConnection = null, $recurse = true)
    {
        return self::getADGenericBidirectionalMembershipAsADObject("memberof", $distinguishedName, $groups, $searchBase, $ldapConnection, $recurse);
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
     * Get the nesded members of an object
     * If $recurse is set to false, only the first level down is retreived.
     *
     * @param string $distinguishedName
     * @param array $groupList
     * @param array $searchBase
     * @param $ldapConnection
     * @param bool $recurse
     * @return NULL|array
     */
    public static function getADGenericBidirectionalMembershipAsADObject(string $memberAttribute, string $distinguishedName, array &$groupList, string $searchBase = ADConfig::DEFAULT_DN, $ldapConnection = null, $recurse = true)
    {
        $distinguishedNames = self::getADGenericBidirectionalMembership($memberAttribute, $distinguishedName, $groupList, $searchBase, $ldapConnection, $recurse);
        
        $ADObjects = array();
        
        foreach ($distinguishedNames as $distinguishedName)
        {
            $ADObject = self::getADObject($distinguishedName);
            
            if(count($ADObject) == 1)
            {
                $ADObjects[] = (self::getADObject($distinguishedName))[0];
            }
            
        }
        return $ADObjects;
    }
    
    /**
     * Get the nesded members of an object
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
     * Get the nesded members of an object
     * If $recurse is set to false, only the first level down is retreived.
     *
     * @param string $distinguishedName
     * @param array $groupList
     * @param array $searchBase
     * @param $ldapConnection
     * @param bool $recurse
     * @return NULL|array
     */
    public static function getMembersAsADObject(string $distinguishedName, array &$groupList, string $searchBase = ADConfig::DEFAULT_DN, $ldapConnection = null, $recurse = true)
    {
        return self::getADGenericBidirectionalMembershipAsADObject("member", $distinguishedName, $groupList, $searchBase, $ldapConnection, $recurse);
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
    public static function getADObjectSearchResultCount(ADFilter $ADFilter, string $searchBase = ADConfig::DEFAULT_DN, int $searchScope = ADConfig::SEARCH_SCOPE_SUBTREE, $ldapConnection = null, $properties = array("objectguid"), $sizeLimit = 150)
    {
        if(is_null($ldapConnection))
        {
            $ldapConnection = self::getConnection();
        }
        
        switch($searchScope)
        {
            case ADConfig::SEARCH_SCOPE_ONELEVEL :
                $searchResult = @ldap_list($ldapConnection, $searchBase, $ADFilter->__tostring(), $properties, null, $sizeLimit);
                break;
            case ADConfig::SEARCH_SCOPE_SUBTREE :
                $searchResult = @ldap_search($ldapConnection, $searchBase, $ADFilter->__tostring(), $properties, null, $sizeLimit);
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
    
    public static function getParentOrganizationalUnit($identity, int $level = 0)
    {
        $matches = array();
        preg_match_all("/(OU=['\w\s\-]+)/", $identity->distinguishedName, $matches);
        
        if($level >= 0 && count($matches) == 2 && $level < count($matches[1]))
        {           
            return str_replace("OU=", "", $matches[1][$level]);
        }

        return "No parent organizational unit at level " . $level;
    }
    
    public static function setADFastMode(bool $isActivated = true)
    {
        $_SESSION["ADFastMode"] = $isActivated;
        /*
        if(isset($_SESSION["ADFastMode"]))
        {
            $_SESSION["ADFastMode"] = $isActivated;
        }
        */
    }
    
    public static function removeAccentedCharacters(string $string) : string
    {
            $table = array(
                'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
                'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
                'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
                'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
                'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
                'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
            );
            return strtr($string, $table);
    }
    
    public static function getRecipient()
    {
        
    }
}
