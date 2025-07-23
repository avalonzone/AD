<?php
class ADFilterAttribute
{
    private string $_attributeName;
    private string $_value;
    private string $_operator;
    public int $attributeType;
    
    public function __construct(string $attributeName, string $value, int $attributeType = ADConfig::SEARCH_FILTER_TYPE_AND, string $operator = ADConfig::SEARCH_FILTER_OPERATOR_EQUAL)
    {
        $this->_attributeName = $attributeName;
        $this->_operator = $operator;
        $this->attributeType = $attributeType;
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
                $value = ldap_escape($value, '', LDAP_ESCAPE_FILTER);
                break;
            default:
                break;
        }
        return $value;
    }
    
    public function __tostring()
    {   
        // Patch ! Hard Modification for special case NOT. 30/09/24
        if($this->attributeType == ADConfig::SEARCH_FILTER_TYPE_NOT){
            return "(!(" . $this->_attributeName . $this->_operator . $this->_value . "))";
        }
        return "(" . $this->_attributeName . $this->_operator . $this->_value . ")";
    }
    
}