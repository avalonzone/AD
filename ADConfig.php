<?php
abstract class ADConfig
{
    const DEFAULT_DN     = Config::DEFAULT_SEARCH_DN; //Adapt these value to value that suits your environment
    const DEFAULT_ORG_DN = Config::DEFAULT_ORG_DN; //Adapt these value to value that suits your environment
   
    const OBJECT_CATEGORY_PERSON   = 'person';
    const OBJECT_CATEGORY_GROUP    = 'group';
    const OBJECT_CATEGORY_COMPUTER = 'computer';
    const OBJECT_CATEGORY_OU = 'Organizational-Unit';
    
    const SEARCH_SCOPE_BASE = 0;
    const SEARCH_SCOPE_ONELEVEL = 1;
    const SEARCH_SCOPE_SUBTREE = 2;
     
    const SEARCH_FILTER_TYPE_AND = 0;
    const SEARCH_FILTER_TYPE_OR = 1;
    const SEARCH_FILTER_TYPE_NOT = 2;
    
    const SEARCH_FILTER_OPERATOR_EQUAL = "=";
    const SEARCH_FILTER_OPERATOR_GREATER = ">=";
    const SEARCH_FILTER_OPERATOR_LESS = "<=";
    const SEARCH_FILTER_OPERATOR_APPROXIMATE = "~=";
    
    const REGEX_MATCH_SID  = "/^S-\d-\d+-(\d+-){1,14}\d+$/";
    const REGEX_MATCH_DN   = '/^(?:(?<cn>CN=(?<name>[^,]*)),)?(?:(?<path>(?:(?:CN|OU)=[^,]+,?)+),)?(?<domain>(?:DC=[^,]+,?)+)$/';
        
    const ADS_UF_SCRIPT =                                   "00000000000000000000000000000001"; // 1
    const ADS_UF_ACCOUNTDISABLE =                           "00000000000000000000000000000010"; // 2
    const ADS_UF_HOMEDIR_REQUIRED =                         "00000000000000000000000000001000"; // 8
    const ADS_UF_LOCKOUT =                                  "00000000000000000000000000010000"; // 16
    const ADS_UF_PASSWD_NOTREQD =                           "00000000000000000000000000100000"; // 32
    const ADS_UF_PASSWD_CANT_CHANGE =                       "00000000000000000000000001000000"; // 64
    const ADS_UF_ENCRYPTED_TEXT_PASSWORD_ALLOWED =          "00000000000000000000000010000000"; // 128
    const ADS_UF_TEMP_DUPLICATE_ACCOUNT =                   "00000000000000000000000100000000"; // 256
    const ADS_UF_NORMAL_ACCOUNT =                           "00000000000000000000001000000000"; // 512   
    const ADS_UF_INTERDOMAIN_TRUST_ACCOUNT =                "00000000000000000000100000000000"; // 2048
    const ADS_UF_WORKSTATION_TRUST_ACCOUNT =                "00000000000000000001000000000000"; // 4096
    const ADS_UF_SERVER_TRUST_ACCOUNT =                     "00000000000000000010000000000000"; // 8192    
    const ADS_UF_DONT_EXPIRE_PASSWD =                       "00000000000000010000000000000000"; // 65536
    const ADS_UF_MNS_LOGON_ACCOUNT =                        "00000000000000100000000000000000"; // 131072
    const ADS_UF_SMARTCARD_REQUIRED =                       "00000000000001000000000000000000"; // 262144
    const ADS_UF_TRUSTED_FOR_DELEGATION =                   "00000000000010000000000000000000"; // 524288 
    const ADS_UF_NOT_DELEGATED =                            "00000000000100000000000000000000"; // 1048576 
    const ADS_UF_USE_DES_KEY_ONLY =                         "00000000001000000000000000000000"; // 2097152 
    const ADS_UF_DONT_REQUIRE_PREAUTH =                     "00000000010000000000000000000000"; // 4194304 
    const ADS_UF_PASSWORD_EXPIRED =                         "00000000100000000000000000000000"; // 8388608 
    const ADS_UF_TRUSTED_TO_AUTHENTICATE_FOR_DELEGATION =   "00000001000000000000000000000000"; // 16777216 
    const ADS_UF_PARTIAL_SECRETS_ACCOUNT =                  "00000100000000000000000000000000"; // 67108864
        

    const DOMAIN_GUID = "xxxxxxxx-xxxx-xxxx-xxx-xxxxxxxxxxxx"; // Should be working but none of these guid works
    
    const PASSWORD = Config::PASSWORD; //Adapt these value to value that suits your environment
    const DOMAIN_NAME = Config::DOMAIN_NAME; //Adapt these value to value that suits your environment
    const DOMAIN_CONTROLLER = Config::DOMAIN_CONTROLLER; //Adapt these value to value that suits your environment
    const DEFAULT_SEARCH_DN = Config::DEFAULT_SEARCH_DN; //Adapt these value to value that suits your environment
    const SAM_ACCOUNT_NAME = Config::SAM_ACCOUNT_NAME; //Adapt these value to value that suits your environment
    const PRIMARY_DOMAIN_CONTROLLER = Config::PRIMARY_DOMAIN_CONTROLLER; //Adapt these value to value that suits your environment

}