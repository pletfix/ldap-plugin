<?php

return [

    /**
     * ----------------------------------------------------------------
     * Active Directory Connection Parameters
     * ----------------------------------------------------------------
     *
     * Connection parameters with control user account for the active directory.
     *
     * See <https://github.com/adldap/adLDAP/wiki/Configuration-Settings> for setting options.
     */

    'connection' => [
        'account_suffix' => env('LDAP_ACCOUNT_SUFFIX'), // The full account suffix for your domain, e.g. '@mydomain.local'.
        'domain_controllers' => [                       // An array of domains may be provided for load balancing.
            env('LDAP_DOMAIN_CONTROLLER_1'),
            env('LDAP_DOMAIN_CONTROLLER_2'),
        ],
        'port'      => 389,                             // Default: 389 (636 for SSL).
        'base_dn'   => env('LDAP_BASE_DN'),             // The base dn for your domain, e.g. 'DC=mydomain,DC=local'.
        'use_ssl'   => false,                           // Use SSL? Default: false.
        'referrals' => false,                           // Follow a referral to another server on your network if the server queried knows the information your asking for exists. Default: false.
    ],

    /*
     * ----------------------------------------------------------------
     * Active Directory User Attributes
     * ----------------------------------------------------------------
     *
     * This attributes will be received from the AD.
     *
     * The "userprincipalname" attribute is always received, even if it is commented out here.
     */

    'attributes' => [
        'userprincipalname',    // Full Identifier, e.g. "FrankR@result.ads"
        'samaccountname',       // Username, e.g. "FrankR"
        'sn',                   // Lastname, e.g. "Rohlfing"
        'givenname',            // Firstname, e.g. "Frank"
        'displayname',          // e.g. "Frank Rohlfing"
        'description',          // Description of the entry
        'mail',                 // Email, e.g. "F-Rohlfing@sokrates.de"
        'telephonenumber',      // Phone, e.g. "+49 173 2438127"
        'whencreated',          // Created at, e.g. "20140116115143.0Z"
        'memberof',             // Array of groups, e.g. [0 => "CN=Clientadmins,OU=DienstekontenundGruppen,DC=result,DC=ads", 1 => "CN=Domainverwaltung_NC,OU=Empf�nger,DC=result,DC=ads"]
        'distinguishedname',    // e.g. "CN=Frank Rohlfing,OU=EDV-OG4,DC=result,DC=ads"
//        'objectclass',
//        'cn',
//        'instancetype',
//        'whenchanged',
//        'usncreated',
//        'usnchanged',
//        'proxyaddresses',
//        'name',
//        'objectguid',
//        'useraccountcontrol',
//        'badpwdcount',
//        'codepage',
//        'countrycode',
//        'badpasswordtime',
//        'lastlogoff',
//        'lastlogon',
//        'scriptpath',
//        'pwdlastset',
//        'primarygroupid',
//        'objectsid',
//        'admincount',
//        'accountexpires',
//        'logoncount',
//        'samaccounttype',
//        'showinaddressbook',
//        'legacyexchangedn',
//        'lockouttime',
//        'objectcategory',
//        'msnpallowdialin',
//        'dscorepropagationdata',
//        'lastlogontimestamp',
//        'msds-supportedencryptiontypes',
//        'textencodedoraddress',
//        'homemdb',
//        'msexchuseraccountcontrol',
//        'msexchmailboxguid',
//        'mailnickname',
//        'msexchmailboxsecuritydescriptor',
//        'homemta',
//        'msexchpoliciesincluded',
//        'msexchhomeservername',
//        'extensionattribute4',
//        'msexchalobjectversion',
//        'mdbusedefaults',
    ],

    /**
     * ----------------------------------------------------------------
     * Active Directory Member Mapping
     * ----------------------------------------------------------------
     *
     * Here you may map the Common Name (CN) of the "memberof" attribute to the applications user role.
     *
     * The role is added to the attributes automatically if the "memberof" attribute is listed above.
     *
     * Note, that the first matching will be used.
     *
     * See in config/auth.php which user roles are possible.
     */

    'role' => [
        'mapping' => [
            // CN        => role
            'Admins'     => 'admin',
            //'Backoffice' => 'editor',

        ],
        'default' => 'user',
    ],

    /*
     * ----------------------------------------------------------------
     * Database Model
     * ----------------------------------------------------------------
     *
     * Define a model here, if you want to store the attributes of the current user in your database.
     * Typical it is the same model as you has it defined in config/auth.php.
     *
     * Make sure that the columns are added to this table that you have specify in the attribute list above.
     * Note that the "userprincipalname" attribute is required for matching.
     *
     * If no model class is defined, the user attributes are not stored in the database.
     */

    'model' => [
        'class'   => 'App\Models\User',
        'mapping' => [
            //AD Attribue       => Model Attribute
            'userprincipalname' => 'principal',
            'displayname'	    => 'name',
            'mail'				=> 'email',
            'role'              => 'role',
        ],
    ],
];
