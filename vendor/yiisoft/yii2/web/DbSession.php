<?php
/**
 */

namespace yii\web;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\PdoValue;
use yii\db\Query;
use yii\di\Instance;

/**
 * DbSession extends [[Session]] by using database as session data storage.
 *
 * By default, DbSession stores session data in a DB table named 'session'. This table
 * must be pre-created. The table name can be changed by setting [[sessionTable]].
 *
 * The following example shows how you can configure the application to use DbSession:
 * Add the following to your application config under `components`:
 *
 * ```php
 * 'session' => [
 *     'class' => 'yii\web\DbSession',
 *     // 'db' => 'mydb',
 *     // 'sessionTable' => 'my_session',
 * ]
 * ```
 *
 * DbSession extends [[MultiFieldSession]], thus it allows saving extra fields into the [[sessionTable]].
 * Refer to [[MultiFieldSession]] for more details.
 *
 * @since 2.0
 */
class DbSession extends MultiFieldSession
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbSession object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $db = 'db';
    /**
     * @var string the name of the DB table that stores the session data.
     * The table should be pre-created as follows:
     *
     * ```sql
     * CREATE TABLE session
     * (
     *     id CHAR(40) NOT NULL PRIMARY KEY,
     *     expire INTEGER,
     *     data BLOB
     * )
     * ```
     *
     * where 'BLOB' refers to the BLOB-type of your preferred DBMS. Below are the BLOB type
     * that can be used for some popular DBMS:
     *
     * - MySQL: LONGBLOB
     * - PostgreSQL: BYTEA
     * - MSSQL: BLOB
     *
     * When using DbSession in a production server, we recommend you create a DB index for the 'expire'
     * column in the session table to improve the performance.
     *
     * Note that according to the php.ini setting of `session.hash_function`, you may need to adjust
     * the length of the `id` column. For example, if `session.hash_function=sha256`, you should use
     * length 64 instead of 40.
     */
    public $sessionTable = '{{%session}}';

    /**
     * @var array Session fields to be written into session table columns
     * @since 2.0.17
     */
    protected $fields = [];


    /**
     * Initializes the DbSession component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    /**
     * Session open handler.
     * @internal Do not call this method directly.
     * @param string $savePath session save path
     * @param string $sessionName session name
     * @return bool whether session is opened successfully
     */
    public function openSession($savePath, $sessionName)
    {
        if ($this->getUseStrictMode()) {
            $id = $this->getId();
            if (!$this->getReadQuery($id)->exists()) {
                //This session id does not exist, mark it for forced regeneration
                $this->_forceRegenerateId = $id;
            }
        }

        return parent::openSession($savePath, $sessionName);
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateID($deleteOldSession = false)
    {
        $oldID = session_id();

        // if no session is started, there is nothing to regenerate
        if (empty($oldID)) {
            return;
        }

        parent::regenerateID(false);
        $newID = session_id();
        // if session id regeneration failed, no need to create/update it.
        if (empty($newID)) {
            Yii::warning('Failed to generate new session ID', __METHOD__);
            return;
        }

        $row = $this->db->useMaster(function() use ($oldID) {
            return (new Query())->from($this->sessionTable)
               ->where(['id' => $oldID])
               ->createCommand($this->db)
               ->queryOne();
        });

        if ($row !== false) {
            if ($deleteOldSession) {
                $this->db->createCommand()
                    ->update($this->sessionTable, ['id' => $newID], ['id' => $oldID])
                    ->execute();
            } else {
                $row['id'] = $newID;
                $this->db->createCommand()
                    ->insert($this->sessionTable, $row)
                    ->execute();
            }
        } else {
            // shouldn't reach here normally
            $this->db->createCommand()
                ->insert($this->sessionTable, $this->composeFields($newID, ''))
                ->execute();
        }
    }

    /**
     * Ends the current session and store session data.
     * @since 2.0.17
     */
    public function close()
    {
        if ($this->getIsActive()) {
            // prepare writeCallback fields before session closes
            $this->fields = $this->composeFields();
            YII_DEBUG ? session_write_close() : @session_write_close();
        }
    }

    /**
     * Session read handler.
     * @internal Do not call this method directly.
     * @param string $id session ID
     * @return string the session data
     */
    public function readSession($id)
    {
        $query = $this->getReadQuery($id);

        if ($this->readCallback !== null) {
            $fields = $query->one($this->db);
            return $fields === false ? '' : $this->extractData($fields);
        }

        $data = $query->select(['data'])->scalar($this->db);
        return $data === false ? '' : $data;
    }

    /**
     * Session write handler.
     * @internal Do not call this method directly.
     * @param string $id session ID
     * @param string $data session data
     * @return bool whether session write is successful
     */
    public function writeSession($id, $data)
    {
        if ($this->getUseStrictMode() && $id === $this->_forceRegenerateId) {
            //Ignore write when forceRegenerate is active for this id
            return true;
        }

        // exception must be caught in session write handler
        // https://secure.php.net/manual/en/function.session-set-save-handler.php#refsect1-function.session-set-save-handler-notes
        try {
            // ensure backwards compatability (fixed #9438)
            if ($this->writeCallback && !$this->fields) {
                $this->fields = $this->composeFields();
            }
            // ensure data consistency
            if (!isset($this->fields['data'])) {
                $this->fields['data'] = $data;
            } else {
                $_SESSION = $this->fields['data'];
            }
            // ensure 'id' and 'expire' are never affected by [[writeCallback]]
            $this->fields = array_merge($this->fields, [
                'id' => $id,
                'expire' => time() + $this->getTimeout(),
            ]);
            $this->fields = $this->typecastFields($this->fields);
            $this->db->createCommand()->upsert($this->sessionTable, $this->fields)->execute();
            $this->fields = [];
        } catch (\Exception $e) {
            Yii::$app->errorHandler->handleException($e);
            return false;
        }
        return true;
    }

    /**
     * Session destroy handler.
     * @internal Do not call this method directly.
     * @param string $id session ID
     * @return bool whether session is destroyed successfully
     */
    public function destroySession($id)
    {
        $this->db->createCommand()
            ->delete($this->sessionTable, ['id' => $id])
            ->execute();

        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     * @internal Do not call this method directly.
     * @param int $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return bool whether session is GCed successfully
     */
    public function gcSession($maxLifetime)
    {
        $this->db->createCommand()
            ->delete($this->sessionTable, '[[expire]]<:expire', [':expire' => time()])
            ->execute();

        return true;
    }

    /**
     * Generates a query to get the session from db
     * @param string $id The id of the session
     * @return Query
     */
    protected function getReadQuery($id)
    {
        return (new Query())
            ->from($this->sessionTable)
            ->where('[[expire]]>:expire AND [[id]]=:id', [':expire' => time(), ':id' => $id]);
    }

    /**
     * Method typecasts $fields before passing them to PDO.
     * Default implementation casts field `data` to `\PDO::PARAM_LOB`.
     * You can override this method in case you need special type casting.
     *
     * @param array $fields Fields, that will be passed to PDO. Key - name, Value - value
     * @return array
     * @since 2.0.13
     */
    protected function typecastFields($fields)
    {
        if (isset($fields['data']) && !is_array($fields['data']) && !is_object($fields['data'])) {
            $fields['data'] = new PdoValue($fields['data'], \PDO::PARAM_LOB);
        }

        return $fields;
    }
}
