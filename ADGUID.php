<?php
class ADGUID
{
    public string $binaryGUID;
    public string $hexGUID;
    public ADFilterAttribute $hexADFilterAttributeGUID;
    public ADFilter $hexADFilterGUID;
    public string $hexFormated_8_4_4_16;
    public string $hexFormated_8_4_4_4_12;
    public string $hexFormated_RFC4122;
            
    public const REGEX_MATCH_OBJECT_GUID_RAW_HEX = "/^([0-9a-fA-F]){32}$/";
    public const REGEX_FORMAT_OBJECT_GUID_8_4_4_16 = "/^([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{16})$/";
    public const REGEX_FORMAT_OBJECT_GUID_8_4_4_4_12 = "/^([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})$/";
    
    public function __construct(string $guid)
    {     
        if(strlen($guid) == 16)
        {
            $this->binaryGUID = $guid;
            $this->hexGUID = bin2hex($guid);
        }
        elseif(strlen($guid) == 32 || strlen($guid) == 35 || strlen($guid) == 36)
        {
            $guid = preg_replace("/-/", "", $guid);
            
            if(preg_match(self::REGEX_MATCH_OBJECT_GUID_RAW_HEX, $guid))
            {
                    $this->hexGUID = $guid;
                    $this->binaryGUID = hex2bin($guid);
            }
            else
            {
                throw new Exception("No valid GUID provided : '" . $guid . "' must be binary or hex");
            }
        }
        else
        {
            throw new Exception("No valid GUID provided : '" . $guid . "' must be binary or hex");
        }
        
        $this->hexADFilterAttributeGUID = new ADFilterAttribute("objectguid", $this->hexGUID);
        $this->hexADFilterGUID = new ADFilter($this->hexADFilterAttributeGUID);
        $this->hexFormated_8_4_4_16 = self::formatGUID_8_4_4_16($this->hexGUID);
        $this->hexFormated_8_4_4_4_12 = self::formatGUID_8_4_4_4_12($this->hexGUID);
        $this->hexFormated_RFC4122 = self::formatGUID_RFC4122($this->hexGUID); // Not yet implemented
    }
    
    public static function getADEscapedHexGUID($guid)
    {
        $guid = preg_replace("/-/", "", $guid);
        
        $escapedHexGUID = "\\" . substr(chunk_split(strtoupper($guid),2,"\\"),0,-1);
        return $escapedHexGUID;
    }
        
    public static function reverse_bytes($hexNumber)
    {
        $reversedBytes = "";
        
        for ($x = strlen($hexNumber) - 2; $x >= 0; $x = $x - 2)
        {
            $reversedBytes .= substr($hexNumber, $x, 2);
        }
        return $reversedBytes;
    }
    
    public static function formatGUID_RFC4122($guid)
    {
        if(self::isGUID($guid))
        {
            $guid = preg_replace("/-/", "", $guid);          
            $unsigned32_time_low = self::reverse_bytes(substr($guid, 0, 8));
            $unsigned16_time_mid = self::reverse_bytes(substr($guid, 8, 4));
            $unsigned16_time_hi_and_version = self::reverse_bytes(substr($guid, 12, 4));
            $unsigned8_clock_seq_hi_and_reserved = substr($guid, 16, 2);
            $unsigned8_clock_seq_low = substr($guid, 18, 2);
            $byte_6_node = substr($guid, 20, 12);
            return $unsigned32_time_low . "-" . $unsigned16_time_mid . "-" . $unsigned16_time_hi_and_version . "-" . $unsigned8_clock_seq_hi_and_reserved . $unsigned8_clock_seq_low . "-" . $byte_6_node;
        }
        else
        {
            throw new Exception("No valid GUID provided : '" . $guid . "' must be binary or hex");
        }  
    }
    
    public static function isGUID($guid)
    {
        if(is_string($guid) && preg_match(self::REGEX_MATCH_OBJECT_GUID_RAW_HEX, preg_replace("/-/", "", $guid)))
        {
            return true;
        }
        if(is_a($guid, __CLASS__, true))
        {
            return true;
        }
        return false;
    }
    
    public function __toString()
    {
        return $this->hexFormated_8_4_4_16;
    }
    
    public static function formatGUID($guid)
    {
        return self::formatGUID_8_4_4_16($guid);
    }
    
    public static function formatGUID_8_4_4_16($guid)
    {
        if(self::isGUID($guid))
        {
            $matches = array();
            preg_match(self::REGEX_FORMAT_OBJECT_GUID_8_4_4_16, $guid, $matches);
            return $matches[1] . "-" . $matches[2] . "-" . $matches[3] . "-" . $matches[4];
        }
        else
        {
            throw new Exception("No valid GUID provided : '" . $guid . "' must be binary or hex");
        }  
    }
    
    public static function formatGUID_8_4_4_4_12($guid)
    {
        if(self::isGUID($guid))
        {
            $matches = array();
            preg_match(self::REGEX_FORMAT_OBJECT_GUID_8_4_4_4_12, $guid, $matches);
            return $matches[1] . "-" . $matches[2] . "-" . $matches[3] . "-" . $matches[4] . "-" . $matches[5];
        }
        else
        {
            throw new Exception("No valid GUID provided : '" . $guid . "' must be binary or hex");
        }
    }
}