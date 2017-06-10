# LDAP Plugin for Pletfix

## About This

This plugin provides a LDAP service to authenticate the user through the Active Directory.

## Installation 

Fetch the package by running the following terminal command under the application's directory:

    composer require pletfix/ldap

After downloading, enter this command in your terminal to register the plugin:

    php console plugin pletfix/ldap 

## Environment and Configuration
    
Add the following environment variables in your `.env` file:
  
    LDAP_ACCOUNT_SUFFIX=YourYccountSuffix
    LDAP_DOMAIN_CONTROLLER_1=YourPrimaryDC
    LDAP_DOMAIN_CONTROLLER_2=YourSecondDC
    LDAP_BASE_DN=YouBaseDN

At the next, open the configuration file `./config/lap` under the application's directory and override the defaults if 
you wish.
   
## Customize
    
If you would like to modified the views of the plugin, copy them to the application's view directory, where you can edit 
the views as you wish:
     
    cp -R ./vendor/pletfix/ldap/views/* ./resources/views/
    
If you like to use an another root path, have a look in the plugin's route entries in `./vendor/pletfix/ldap/config/routes.php`. 
You can override  or modify the route entries in the application's route file `./config/boot/routes.php` like you wish:

    $route->get('auth/ldap',  'Auth\LdapController@showForm');
    $route->post('auth/ldap', 'Auth\LdapController@login');
 
## Usage

Enter the following URL into your Browser to open the login form:

    https://<your-application>/auth/ldap

![Screenshot1](https://raw.githubusercontent.com/pletfix/ldap/master/screenshot1.png)


### `ldap()` {.method}

You can access the LDAP service via the `ldap` function:

    $ldap = ldap();
