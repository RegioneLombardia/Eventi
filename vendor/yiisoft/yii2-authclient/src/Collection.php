<?php
/**
 */

namespace yii\authclient;

use yii\base\Component;
use yii\base\InvalidParamException;
use Yii;

/**
 * Collection is a storage for all auth clients in the application.
 *
 * Example application configuration:
 *
 * ```php
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'google' => [
 *                 'class' => 'yii\authclient\clients\Google',
 *                 'clientId' => 'google_client_id',
 *                 'clientSecret' => 'google_client_secret',
 *              ],
 *             'facebook' => [
 *                 'class' => 'yii\authclient\clients\Facebook',
 *                 'clientId' => 'facebook_client_id',
 *                 'clientSecret' => 'facebook_client_secret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ```
 *
 * @property-read ClientInterface[] $clients List of auth clients.
 *
 * @since 2.0
 */
class Collection extends Component
{
    /**
     * @var \yii\httpclient\Client|array|string HTTP client instance or configuration for the [[clients]].
     * If set, this value will be passed as 'httpClient' config option while instantiating particular client object.
     * This option is useful for adjusting HTTP client configuration for the entire list of auth clients.
     */
    public $httpClient;

    /**
     * @var array list of Auth clients with their configuration in format: 'clientId' => [...]
     */
    private $_clients = [];


    /**
     * @param array $clients list of auth clients
     */
    public function setClients(array $clients)
    {
        $this->_clients = $clients;
    }

    /**
     * @return ClientInterface[] list of auth clients.
     */
    public function getClients()
    {
        $clients = [];
        foreach ($this->_clients as $id => $client) {
            $clients[$id] = $this->getClient($id);
        }

        return $clients;
    }

    /**
     * @param string $id service id.
     * @return ClientInterface auth client instance.
     * @throws InvalidParamException on non existing client request.
     */
    public function getClient($id)
    {
        if (!array_key_exists($id, $this->_clients)) {
            throw new InvalidParamException("Unknown auth client '{$id}'.");
        }
        if (!is_object($this->_clients[$id])) {
            $this->_clients[$id] = $this->createClient($id, $this->_clients[$id]);
        }

        return $this->_clients[$id];
    }

    /**
     * Checks if client exists in the hub.
     * @param string $id client id.
     * @return bool whether client exist.
     */
    public function hasClient($id)
    {
        return array_key_exists($id, $this->_clients);
    }

    /**
     * Creates auth client instance from its array configuration.
     * @param string $id auth client id.
     * @param array $config auth client instance configuration.
     * @return ClientInterface auth client instance.
     */
    protected function createClient($id, $config)
    {
        $config['id'] = $id;

        if (!isset($config['httpClient']) && $this->httpClient !== null) {
            $config['httpClient'] = $this->httpClient;
        }

        return Yii::createObject($config);
    }
}
