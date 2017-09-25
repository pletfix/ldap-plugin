# LDAP Plugin for Pletfix

## About This

This plugin provides a LDAP service to authenticate the user through the Active Directory.

## Installation 

Fetch the package by running the following terminal command under the application's directory:

    composer require pletfix/ldap-plugin

After downloading, enter this command in your terminal to register the plugin:

    php console plugin pletfix/ldap-plugin 

## Environment and Configuration
    
Add the following environment variables in your `.env` file:
  
    LDAP_ACCOUNT_SUFFIX=YourYccountSuffix
    LDAP_DOMAIN_CONTROLLER_1=YourPrimaryDC
    LDAP_DOMAIN_CONTROLLER_2=YourSecondDC
    LDAP_BASE_DN=YouBaseDN

At the next, open the configuration file `./config/ldap.php` under the application's directory and override the 
defaults if you wish.
   
## Customize
    
If you would like to modified the views of the plugin, create a folder `ldap` under the view directory of the application, 
and copy the views there. Here you can edit the views as you like:
    
    mkdir ./resources/views/ldap 
    cp -R ./vendor/pletfix/ldap-plugin/views/* ./resources/views/ldap
    
If you like to use another route paths, copy the route entries from `./vendor/pletfix/ldap-plugin/config/routes.php` 
into the application's routing file `./config/boot/routes.php`, where you can modify them as you wish:

    $route->get('ldap/login',  'LdapController@showForm');
    $route->post('ldap/login', 'LdapController@login');
 
## Usage

### User Authentication

Enter the following URL into your Browser to open the login form:

    https://<your-application>/ldap/login

![Screenshot1](https://raw.githubusercontent.com/pletfix/ldap-plugin/master/screenshot1.png)

#### User Role

The "memberof" attribute is used to determine the user role. You may edit the member mapping in the configuration file 
`config/ldap`.

#### User Model

If you have defined a user model in the configuration, the user attributes are stored in the database.
By default, the user model from the [Pletfix Application Skeleton](https://github.com/pletfix/app) is used and no 
further migration is required.

#### Logout

You may invoke just the following command to logout the user: 
 
    auth()->logout();
 
### LDAP Service

#### Accessing the LDAP service

You can get an instance of the LDAP Service from the Dependency Injector:

    /** @var Pletfix\Ldap\Services\Contracts\Ldap $ldap */
    $ldap = DI::getInstance()->get('ldap');
    
You can also use the `ldap()` function to get the LDAP service, it is more comfortable:
       
    $ldap = ldap();

#### Available Methods

#### `search()`

Search LDAP tree and get all result entries.

    $users = $ldap->search('userprincipalname=Fr*');

#### `getUsers()`

Get the user entries.

    $users = $ldap->getUsers();
    
You may also set a filter for the `userprincipalname` attribute:
    
    $users = $ldap->getUsers('Fr*');

#### `getUser()`

Get the user attributes by given username (userPrincipalName or samAccountName).

    $user = $ldap->getUser('FrankR');
    
You may define the attributes of the user in the configuration file `config/ldap`.            

#### `authenticate()`

Authenticate the user through the Active Directory.

    $isAuthenticated = $ldap->authenticate($username, $password);

#### `getErrorCode()`

Return the LDAP error code of the last LDAP command.

    $errorCode = $ldap->getErrorCode();

#### `getErrorMessage()`

Return the LDAP error message of the last LDAP command.

    $errorMessage = $ldap->getErrorMessage();