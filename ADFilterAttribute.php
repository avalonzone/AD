<?php
class ADFilterAttribute
{
    private string $_attributeName;
    private string $_value;
    
    public function __construct(string $attributeName, string $value)
    {
        $this->_attributeName = $attributeName;
        $this->_value = self::formatADFilterAttributeValue($attributeName, $value);
    }
    
    public static function formatADFilterAttributeValue(string $attributeName, string $value)
    {
        // TODO Numerical values should be handled.... 
        switch(strtolower($attributeName))
        {
            case "objectguid" :
                $value = ADGUID::getADEscapedHexGUID($value);
                break;
            case "distinguishedname" :
                $value = ldap_escape($value, null, LDAP_ESCAPE_FILTER);
                break;
            default:
                break;
        }
        return $value;
    }
    
    public function __tostring()
    {        
        return "(" . $this->_attributeName . "=" . $this->_value . ")";
    }
    
}