<?php

$router = Core\Application::router();

$router->get('ldap/login',   'LdapController@showForm');
$router->post('ldap/login',  'LdapController@login');
$router->post('ldap/logout', 'LdapController@logout', 'Auth');
