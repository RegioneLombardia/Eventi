<?php
/**
 */

namespace creocoder\flysystem;

use League\Flysystem\Adapter\Ftp;
use Yii;
use yii\base\InvalidConfigException;

/**
 * FtpFilesystem
 *
 */
class FtpFilesystem extends Filesystem
{
    /**
     * @var string
     */
    public $host;
    /**
     * @var integer
     */
    public $port;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @var boolean
     */
    public $ssl;
    /**
     * @var integer
     */
    public $timeout;
    /**
     * @var string
     */
    public $root;
    /**
     * @var integer
     */
    public $permPrivate;
    /**
     * @var integer
     */
    public $permPublic;
    /**
     * @var boolean
     */
    public $passive;
    /**
     * @var integer
     */
    public $transferMode;
    /**
     * @var bool
     */
    public $enableTimestampsOnUnixListings = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->host === null) {
            throw new InvalidConfigException('The "host" property must be set.');
        }

        if ($this->root !== null) {
            $this->root = Yii::getAlias($this->root);
        }

        parent::init();
    }

    /**
     * @return Ftp
     */
    protected function prepareAdapter()
    {
        $config = [];

        foreach ([
            'host',
            'port',
            'username',
            'password',
            'ssl',
            'timeout',
            'root',
            'permPrivate',
            'permPublic',
            'passive',
            'transferMode',
            'enableTimestampsOnUnixListings',
        ] as $name) {
            if ($this->$name !== null) {
                $config[$name] = $this->$name;
            }
        }

        return new Ftp($config);
    }
}
