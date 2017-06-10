<?php

namespace Pletfix\Ldap\Services;

use Pletfix\Ldap\Exceptions\LdapException;
use Pletfix\Ldap\Services\Contracts\Ldap as LdapContract;

/**
 * LDAP is the Lightweight Directory Access Protocol, and is a protocol used to access a Active Directory Servers.
 *
 * @see http://de2.php.net/manual/en/book.ldap.php for more details.
 */
class Ldap implements LdapContract
{
    /**
     * The LDAP link identifier.
     *
     * @var resource
     */
    private $ldap;

    /**
     * Connection parameters.
     *
     * @var array
     */
    private $config;

    /**
     * Create a new Ldap instance.
     */
    public function __construct()
    {
        $this->config = config('ldap');
    }

    /**
     * Get a LDAP link identifier.
     *
     * @return resource
     * @throws LdapException
     */
    private function getLdap()
    {
        if ($this->ldap === null) {
            $conn = $this->config['connection'];

            $protocol = isset($conn['use_ssl']) && $conn['use_ssl'] == true ? 'ldaps://' : 'ldap://';
            $port = isset($conn['port']) ? $conn['port'] : ($protocol == 'ldap://' ? 389 : 636);
            foreach ($conn['domain_controllers'] as $dc) {
                $host = $protocol . $dc;
                $resource = @ldap_connect($host, $port);
                if ($resource !== false && ldap_bind($resource)) {
                    $this->ldap = $resource;
                    break;
                }
            }

            if ($this->ldap === null) {
                throw new LdapException('Connect to LDAP server failed!');
            }

            ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, isset($conn['referrals']) ? $conn['referrals'] : false);
        }

        return $this->ldap;
    }

    /**
     * @inheritdoc
     */
    public function search($filter)
    {
        // Set the attributes that is to be received.
        $attributes = $this->config['attributes'];
        if (!in_array('userprincipalname', $attributes)) {
            $attributes[] = 'userprincipalname';
        }

        // Search LDAP tree.
        $ldap = $this->getLdap();
        $dn = $this->config['connection']['base_dn'];
        $sr = @ldap_search($ldap, $dn, $filter, $attributes);
        if ($sr === false) {
            throw new LdapException($ldap);
        }

        // Get the searching result
        $data = @ldap_get_entries($ldap, $sr);
        if ($data === false) {
            throw new LdapException($ldap);
        }

        // Convert the result data to a simple array. Each entry get each attribute that is defined.
        $entries = [];
        $count = $data['count'];
        for ($i = 0; $i < $count; $i++) {
            $entries[$i] = [];
            foreach ($attributes as $attribute) {
                if (!isset($data[$i][$attribute])) {
                    $entries[$i][$attribute] = null;
                    continue;
                }
                $n = $data[$i][$attribute]['count'];
                if ($n > 1 || in_array($attribute, ['memberof'])) {
                    $entries[$i][$attribute] = [];
                    for ($j=0; $j < $n; $j++) {
                        $entries[$i][$attribute][$j] = $data[$i][$attribute][$j];
                    }
                }
                else {
                    $entries[$i][$attribute] = $data[$i][$attribute][0];
                }
            }
            // Determine the user role
            if (in_array('memberof', $attributes)) {
                $entries[$i]['role'] = $this->getRole($entries[$i]['memberof'] ?: []);
            }
        }

        return $entries;
    }

    /**
     * @inheritdoc
     */
    public function getUsers($filter = '*')
    {
        return $this->search('userprincipalname=' . $filter);
    }

    /**
     * @inheritdoc
     */
    public function getUser($username)
    {
        $entries = $this->search('userprincipalname=' . $this->principal($username));

        return !empty($entries) ? $entries[0] : [];
    }

    /**
     * @inheritdoc
     */
    public function authenticate($username, $password)
    {
        $ok = @ldap_bind($this->getLdap(), $this->principal($username), $password);

        return $ok;
    }

    /**
     * Convert the username into the userPrincipalName attribute.
     * @param string $username
     * @return string
     */
    private function principal($username)
    {
        if (strpos($username, '@') === false) {
            $username .= $this->config['connection']['account_suffix'];
        }

        return $username;
    }

    /**
     * Return the applications user role by given memberof attribute.
     *
     * @param array $memberOf
     * @return array
     */
    private function getRole(array $memberOf)
    {
        if (empty($memberOf)) {
            return $this->config['role']['default'];
        }

        // Get the Common Names (CN) of the members.
        $cns = [];
        foreach ($memberOf as $member) {
            foreach (explode(',', $member) as $item) {
                if (substr($item, 0, 3) == 'CN=') {
                    $cns[substr($item, 3)] = true;
                }
            }
        }

        // Find the first matching role in the mapping table.
        foreach ($this->config['role']['mapping'] as $cn => $role) {
            if (isset($cns[$cn])) {
                return $role;
            }
        }

        return $this->config['role']['default'];
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode()
    {
        $errorCode = ldap_errno($this->ldap);

        return $errorCode;
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage()
    {
        $error = ldap_error($this->ldap);

        return $error;
    }

//    private function bindControlUser()
//    {
//        // Control User an Server binden.
//        $account = $this->config['operator_account'];
//        $success = @ldap_bind($this->getLdap(), $account['username'], $account['password']);
//        if (!$success) {
//            throw new LdapException('LDAP bind failed.');
//        }
//
//        return $success;
//    }
}
