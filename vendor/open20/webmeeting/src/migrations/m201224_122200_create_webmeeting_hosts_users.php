<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use yii\db\Migration;

/**
 * Handles the creation of table `{{%webmeeting}}`.
 */
class m201224_122200_create_webmeeting_hosts_users extends Migration
{
    /**
     * @var type 
     */
    protected $tableName;
    
    /**
     * @var type 
     */
    protected $tableOptions;
    
    /**
     * @inheritdoc
     */
    public function init() {
        $this->tableName = '{{%webmeeting_hosts_users}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if ($this->db->schema->getTableSchema($this->tableName, true) === null) {
            $this->createTable(
                $this->tableName,
                [
                    'id' => $this->primaryKey(),
                    'displayName' => $this->char(255)->defaultValue(null)->comment('Nome del proprietario del WebEx Meeting'),
                    'email' => $this->char(255)->defaultValue(null)->comment("Email dell'utente sulla piattaforma WebEx"),
                    'coHost' => $this->char(255)->defaultValue(null)->comment("Email dell'utente sulla piattaforma WebEx"),
                    'created_at' => $this->dateTime()->defaultValue(null)->comment('Created at'),
                    'updated_at' => $this->dateTime()->defaultValue(null)->comment('Updated at'),
                    'deleted_at' => $this->dateTime()->defaultValue(null)->comment('Deleted at'),
                    'created_by' => $this->integer(11)->defaultValue(null)->comment('Created by'),
                    'updated_by' => $this->integer(11)->defaultValue(null)->comment('Updated by'),
                    'deleted_by' => $this->integer(11)->defaultValue(null)->comment('Deleted by')
                ],
                $this->tableOptions
            );
        }
        
        $this->insert($this->tableName, [
            'id' => 1,
            'displayName' => 'Innovation Console',
            'email' => 'info@open20.it',
            'coHost' => true
        ]);
        $this->insert($this->tableName, [
            'id' => 2,
            'displayName' => 'OpenInnovation Account 1',
            'email' => 'michele.zucchini@open20.it',
            'coHost' => true
        ]);
        $this->insert($this->tableName, [
            'id' => 3,
            'displayName' => 'OpenInnovation Account 2',
            'email' => 'simone.felloni@open20.it',
            'coHost' => true
        ]);
        $this->insert($this->tableName, [
            'id' => 4,
            'displayName' => 'OpenInnovation Account 3',
            'email' => 'damian.gomez@open20.it',
            'coHost' => true
        ]);
        $this->insert($this->tableName, [
            'id' => 5,
            'displayName' => 'OpenInnovation Account 4',
            'email' => 'alessio.deluigi@open20.it',
            'coHost' => true
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropTable($this->tableName);
    }
    
}
