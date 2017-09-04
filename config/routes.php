<?php

$route = \Core\Application::route();

$route->get('ldap/login',  'LdapController@showForm');
$route->post('ldap/login', 'LdapController@login');
