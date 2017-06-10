<?php

$di = \Core\Services\DI::getInstance();

$di->set('ldap', \Pletfix\Ldap\Services\Ldap::class, true);
