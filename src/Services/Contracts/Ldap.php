<?php

namespace Pletfix\Ldap\Services\Contracts;

interface Ldap
{
    /**
     * Search LDAP tree and get all result entries.
     *
     * @param string $filter
     * @return array
     */
    public function search($filter);

    /**
     * Get the user entries.
     *
     * @param string $filter
     * @return array
     */
    public function getUsers($filter = '*');

    /**
     * Get the user attributes by given username (userPrincipalName or samAccountName).
     *
     * @param string $username
     * @return array
     */
    public function getUser($username);

    /**
     * Authenticate the user through the Active Directory.
     *
     * @param string $username userPrincipalName or samAccountName attribute
     * @param string $password
     * @return bool
     */
    public function authenticate($username, $password);

    /**
     * Return the LDAP error code of the last LDAP command.
     *
     * <pre>
     *   0: "Success"
     *  -1: "Can't contact LDAP server"
     *  49: "Invalid credentials"
     * </pre>
     *
     * @return int
     */
    public function getErrorCode();

    /**
     * Return the LDAP error message of the last LDAP command.
     *
     * @return string
     */
    public function getErrorMessage();
}
