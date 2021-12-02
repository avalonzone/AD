<?php
class ADPropertyCollection extends ArrayObject
{        
    public function __construct($array = array())
    {
        //Get all instance public properties
        $classProperties = array_keys(get_class_vars(get_class($this)));
        
        //Cycle through properties and set them to lower case
        $indexedClassProperties = array();
        
        foreach ($classProperties as $key=>$value)
        {
            $indexedClassProperties[strtolower($value)] = $value;
        }
        
        // Cycle through the AD results and clean up the array
        foreach ($array as $key=>$value)
        {
            if(is_numeric($key))
            {
                unset($array[$key]);
            }
            else
            {
                if(isset($value["count"]) && $value["count"] > 0)
                { 
                    if($value["count"] == 1)
                    {
                        $array[$key] = $value[0];
                    }
                    else
                    {
                        unset($array[$key]["count"]);
                    }
                                        
                }
                
                switch($key)
                {
                    case "objectguid" :
                        $guid = new ADGUID($array[$key]);
                        $array[$key] = $guid->hexFormated_8_4_4_16;
                        $array["objectadguid"] = $guid->hexFormated_RFC4122;
                        break;
                    case "objectsid" :
                        $array[$key] = ADUtils::bin_to_str_sid($array[$key]);
                        break;
                    case "usercertificate" :
                        if(is_array($array[$key]))
                        {
                            foreach($array[$key] as $index=>$certificate)
                            {
                                $array[$key][$index] = bin2hex($certificate);
                            }
                        }
                        break;
                    case "ms-ds-consistencyguid" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "thumbnailphoto" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "msexchmailboxguid" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "msrtcsip-userroutinggroupid" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "msexchsafesendershash" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "msexchmailboxsecuritydescriptor" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "msexchblockedsendershash" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "msexchumpinchecksum" :
                        $array[$key] = bin2hex($array[$key]);
                        break;
                    case "dn":
                        unset($array[$key]);
                        break;
                    case "count":
                        unset($array[$key]);
                        break;
                    case "admindescription" :
                        try
                        {
                            $array[$key] = json_decode($array[$key]);
                        }
                        catch (Exception $e)
                        {
                            $array[$key] = array();
                        }
                        break;
                    // In case memberof get auto casted to string if only one element ^^
                    case "memberof" :
                        if(is_string($array[$key]))
                        {
                            $array[$key] = array($array[$key]);
                        }
                        break;
                    // In case members get auto casted to string if only one element ^^
                    case "members" :
                        if(is_string($array[$key]))
                        {
                            $array[$key] = array($array[$key]);
                        }
                        break;
                    case "useraccountcontrol" :
                        $array[$key] = sprintf( "%032d", decbin( $array[$key] ));
                        $array["enabled"] = !ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_ACCOUNTDISABLE);
                        $array["lockedout"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_LOCKOUT);
                        $array["cannotchangepassword"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_PASSWD_CANT_CHANGE);                       
                        $array["passwordneverexpires"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_DONT_EXPIRE_PASSWD);
                        $array["passwordexpired"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_PASSWORD_EXPIRED);
                        $array["passwordnotrequired"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_PASSWD_NOTREQD);                     
                        $array["smartcardLogonrequired"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_SMARTCARD_REQUIRED);   
                        $array["trustedfordelegation"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_TRUSTED_FOR_DELEGATION);
                        $array["trustedtoauthfordelegation"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_TRUSTED_TO_AUTHENTICATE_FOR_DELEGATION);
                        $array["usedeskeyonly"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_USE_DES_KEY_ONLY);
                        $array["mnslogonaccount"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_MNS_LOGON_ACCOUNT);
                        $array["homedirrequired"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_HOMEDIR_REQUIRED);
                        $array["doesnotrequirepreauth"] = ADUtils::isBinaryFlagSet($array[$key], ADConfig::ADS_UF_DONT_REQUIRE_PREAUTH);                         
                        break;
                }
                
                //Populate class public properties if there is a match            
                if(array_key_exists($key, $indexedClassProperties))
                {
                    // Is this necessary ????
                    if(isset($array[$key]))
                    {
                        $classProperty = $indexedClassProperties[$key];
                        $this->$classProperty = $array[$key];
                    }                
                }              
            }   
        }
                
        // Construct the ArrayObject with the cleaned up array
        parent::__construct($array, ArrayObject::ARRAY_AS_PROPS);
    }
    
    public static function getProperties(bool $strtoLower = true)
    {
        $classProperties = array_keys(get_class_vars(get_called_class()));
        
        if($strtoLower)
        {
            foreach ($classProperties as $key=>$value) {
                $classProperties[$key] = strtolower($value);
            }
        }
        
        return $classProperties;
              
    }
}