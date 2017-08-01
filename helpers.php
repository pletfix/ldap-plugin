<?php

use Core\Services\DI;

if (!function_exists('ldap')) {
    /**
     * Get the LDAP object.
     *
     * @return \Pletfix\Ldap\Services\Contracts\Ldap
     */
    function ldap()
    {
        return DI::getInstance()->get('ldap');
    }
}
