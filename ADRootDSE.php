<?php
class ADRootDSE extends ADEntity
{
    public string $configurationNamingContext;
    public string $defaultNamingContext;
    public string $dnsHostName;
       
    // Retrieve Domain controller DNS record from DNS server
    public static function getDomainController($domain = ADConfig::DOMAIN_NAME)
    {
        $hosts = dns_get_record("_ldap._tcp." . $domain , DNS_SRV);
        return is_array($hosts) ? array_column($hosts, "target") : [];
    }
    
    // TODO implements this : https://docs.microsoft.com/en-us/dotnet/api/microsoft.activedirectory.management.adrootdse?view=activedirectory-management-10.0
}