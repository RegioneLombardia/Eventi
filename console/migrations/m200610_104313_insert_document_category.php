<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200610_104313_insert_document_category extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('documenti_categorie',  [
            'titolo' => "Generale",
            'created_at' => '2020-06-10 10:51:30',
            'updated_at' => '2020-06-10 10:51:30',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->delete('documenti_categorie',  [
            'titolo' => "Generale",
            'created_at' => '2020-06-10 10:51:30',
            'updated_at' => '2020-06-10 10:51:30',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
