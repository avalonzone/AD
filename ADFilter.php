<?php
class ADFilter
{
    private array $_ADFilterAttributes;
    
    public function __construct($ADFilterAttributes)
    {
        $this->addADFilterAttributes($ADFilterAttributes);   
    }
        
    private function addADFilterAttribute(ADFilterAttribute $ADFilterAttribute)
    {
        $this->_ADFilterAttributes[] = $ADFilterAttribute;
    }
    
    public function addADFilterAttributes($ADFilterAttributes)
    {
        if(is_array($ADFilterAttributes))
        {
            foreach ($ADFilterAttributes as $ADFilterAttribute)
            {
                if(get_class($ADFilterAttribute) == 'ADFilterAttribute')
                {
                    $this->addADFilterAttribute($ADFilterAttribute);
                }
            }
        }
        else
        {
            if(get_class($ADFilterAttributes) == 'ADFilterAttribute')
            {
                $this->addADFilterAttribute($ADFilterAttributes);
            }
        }
    }
        
    public function __tostring()
    {
        $FilterString = "(&";
        
        foreach ($this->_ADFilterAttributes as $ADFilterAttribute)
        {
            $FilterString .= $ADFilterAttribute->__tostring();
        }
        
        $FilterString .= ")";
        
        return $FilterString;
    }
}