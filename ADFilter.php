<?php
class ADFilter
{
    //private array $_ADFilterAttributes = array();
    private array $ADFilterAttributeTypeOR = array();
    private array $ADFilterAttributeTypeAND = array();
    private array $ADFilterAttributeTypeNOT = array();
    
    public function __construct($ADFilterAttributes)
    {
        $this->addADFilterAttributes($ADFilterAttributes);   
    }
        
    private function addADFilterAttribute(ADFilterAttribute $ADFilterAttribute)
    {
        
        //$this->_ADFilterAttributes[] = $ADFilterAttribute;
        switch($ADFilterAttribute->attributeType)
        {
            case ADConfig::SEARCH_FILTER_TYPE_AND :
                $this->ADFilterAttributeTypeAND[] = $ADFilterAttribute;
                break;
            case ADConfig::SEARCH_FILTER_TYPE_OR :
                $this->ADFilterAttributeTypeOR[] = $ADFilterAttribute;
                break;
            case ADConfig::SEARCH_FILTER_TYPE_NOT :
                $this->ADFilterAttributeTypeNOT[] = $ADFilterAttribute;
                break;
        }
    }
    
    private function isMultiTypeFilter()
    {
      $typeCount = 0;
        $typeCount += count($this->ADFilterAttributeTypeAND) > 0 ? 1 : 0;
        $typeCount += count($this->ADFilterAttributeTypeOR) > 0 ? 1 : 0;
        $typeCount += count($this->ADFilterAttributeTypeNOT) > 0 ? 1 : 0;
                
        if($typeCount > 1)
        {
            return true;
        }
        return false;
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
        /*
        foreach ($this->_ADFilterAttributes as $ADFilterAttribute)
        {
            switch($ADFilterAttribute->attributeType)
            {
                case ADConfig::SEARCH_FILTER_TYPE_AND :
                    $this->ADFilterAttributeTypeAND[] = $ADFilterAttribute;
                    break;
                case ADConfig::SEARCH_FILTER_TYPE_OR :
                    $this->ADFilterAttributeTypeOR[] = $ADFilterAttribute;
                    break;
                case ADConfig::SEARCH_FILTER_TYPE_NOT :
                    $this->ADFilterAttributeTypeNOT[] = $ADFilterAttribute;
                    break;
            }
        }
        */
        
        $FilterString = "";
        
        /*
        if($this->isMultiTypeFilter())
        {
            $FilterString .= "(";
        }
        */
                          
        if(count($this->ADFilterAttributeTypeAND) > 0)
        {
            $FilterString .= "(&";
            foreach ($this->ADFilterAttributeTypeAND as $ADFilterAttribute)
            {
                $FilterString .= $ADFilterAttribute->__tostring();
            }
                        
            if(!$this->isMultiTypeFilter())
            {
                $FilterString .= ")";
            }
        }
           
        if(count($this->ADFilterAttributeTypeOR) > 0)
        {
            $FilterString .= "(|";
            foreach ($this->ADFilterAttributeTypeOR as $ADFilterAttribute)
            {
                $FilterString .= $ADFilterAttribute->__tostring();
            }
            $FilterString .= ")";
        }
        
        if(count($this->ADFilterAttributeTypeNOT) > 0)
        {
            $FilterString .= "";
            //$FilterString .= "(!"; // Modifcation 30/09/24
            foreach ($this->ADFilterAttributeTypeNOT as $ADFilterAttribute)
            {
                $FilterString .= $ADFilterAttribute->__tostring();
            }
            $FilterString .= "";
            //$FilterString .= ")";
        }
          
        if($this->isMultiTypeFilter())
        {
            $FilterString .= ")";
        }
                   
        return $FilterString;
    }
}