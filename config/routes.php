<?php

$route = \Core\Application::route();

// Authentication LDAP Routes
$route->get('auth/ldap',  'Auth\LdapController@showForm');
$route->post('auth/ldap', 'Auth\LdapController@login');
