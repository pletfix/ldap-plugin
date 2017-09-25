<?php

$route = \Core\Application::route();

$route->get('ldap/login',   'LdapController@showForm');
$route->post('ldap/login',  'LdapController@login');
$route->post('ldap/logout', 'LdapController@logout', 'Auth');
