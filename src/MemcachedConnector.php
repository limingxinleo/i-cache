<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Illuminate\Cache;

use Memcached;

class MemcachedConnector
{
    /**
     * Create a new Memcached connection.
     *
     * @param null|string $connectionId
     * @return \Memcached
     */
    public function connect(array $servers, $connectionId = null, array $options = [], array $credentials = [])
    {
        $memcached = $this->getMemcached(
            $connectionId,
            $credentials,
            $options
        );

        if (! $memcached->getServerList()) {
            // For each server in the array, we'll just extract the configuration and add
            // the server to the Memcached connection. Once we have added all of these
            // servers we'll verify the connection is successful and return it back.
            foreach ($servers as $server) {
                $memcached->addServer(
                    $server['host'],
                    $server['port'],
                    $server['weight']
                );
            }
        }

        return $memcached;
    }

    /**
     * Get a new Memcached instance.
     *
     * @param null|string $connectionId
     * @return \Memcached
     */
    protected function getMemcached($connectionId, array $credentials, array $options)
    {
        $memcached = $this->createMemcachedInstance($connectionId);

        if (count($credentials) === 2) {
            $this->setCredentials($memcached, $credentials);
        }

        if (count($options)) {
            $memcached->setOptions($options);
        }

        return $memcached;
    }

    /**
     * Create the Memcached instance.
     *
     * @param null|string $connectionId
     * @return \Memcached
     */
    protected function createMemcachedInstance($connectionId)
    {
        return empty($connectionId) ? new Memcached() : new Memcached($connectionId);
    }

    /**
     * Set the SASL credentials on the Memcached connection.
     *
     * @param \Memcached $memcached
     * @param array $credentials
     */
    protected function setCredentials($memcached, $credentials)
    {
        [$username, $password] = $credentials;

        $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);

        $memcached->setSaslAuthData($username, $password);
    }
}
