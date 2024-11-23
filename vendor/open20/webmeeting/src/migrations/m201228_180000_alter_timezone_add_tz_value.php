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
class m201228_180000_alter_timezone_add_tz_value extends Migration
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
        $this->tableName = '{{%webmeeting_timezone}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->addColumn(
            $this->tableName,
            'tz_value',
            $this->char(255)->defaultValue(null)->comment('TZ to pass to WebEx')->after('id')
        );
        
        $this->update($this->tableName, ['tz_value' => 'UTC'], ['id' => [1, 2]]);
        $this->update($this->tableName, ['tz_value' => 'Pacific/Honolulu'], ['id' => 3]);
        $this->update($this->tableName, ['tz_value' => 'America/Anchorage'], ['id' => 4]);
        $this->update($this->tableName, ['tz_value' => 'America/Winnipeg'], ['id' => [5, 6]]);
        $this->update($this->tableName, ['tz_value' => 'America/Ojinaga'], ['id' => [7, 8, 9]]);
        $this->update($this->tableName, ['tz_value' => 'America/Mazatlan'], ['id' => [10, 11, 12, 13]]);
        $this->update($this->tableName, ['tz_value' => 'America/Detroit'], ['id' => [14, 15, 16]]);
        $this->update($this->tableName, ['tz_value' => 'America/Halifax'], ['id' => [17, 18, 19]]);
        $this->update($this->tableName, ['tz_value' => 'America/Monterrey'], ['id' => [20, 21, 22, 23, 24, 25, 26, 27]]);
        $this->update($this->tableName, ['tz_value' => 'UTC'], ['id' => 28]);
        $this->update($this->tableName, ['tz_value' => 'Atlantic/Azores'], ['id' => [29, 30]]);
        $this->update($this->tableName, ['tz_value' => 'Europe/Dublin'], ['id' => 31]);
        $this->update($this->tableName, ['tz_value' => 'Africa/Monrovia'], ['id' => 32]);
        $this->update($this->tableName, ['tz_value' => 'Europe/Rome'], ['id' => [33, 34, 35, 36]]);
        $this->update($this->tableName, ['tz_value' => 'Europe/Dublin'], ['id' => 37]);
        $this->update($this->tableName, ['tz_value' => 'Europe/Athens'], ['id' => [38, 39, 40, 41, 42, 43, 44, 45]]);
        $this->update($this->tableName, ['tz_value' => 'Europe/Istanbul'], ['id' => [46, 47, 48, 49, 50]]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Dubai'], ['id' => [51, 52, 53]]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Kabul'], ['id' => 54]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Karachi'], ['id' => [55, 56]]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Kolkata'], ['id' => [57, 58]]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Kathmandu'], ['id' => 59]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Almaty'], ['id' => 60]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Yangon'], ['id' => 61]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Bangkok'], ['id' => [62, 63]]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Hong_Kong'], ['id' => [64, 65, 66, 67]]);
        $this->update($this->tableName, ['tz_value' => 'Asia/Seoul'], ['id' => [68,  69, 70]]);
        $this->update($this->tableName, ['tz_value' => 'Australia/Lindeman'], ['id' => [71, 72]]);
        $this->update($this->tableName, ['tz_value' => 'Pacific/Port_Moresby'], ['id' => [73, 74]]);
        $this->update($this->tableName, ['tz_value' => 'Australia/Adelaide'], ['id' => 75]);
        $this->update($this->tableName, ['tz_value' => 'Pacific/Guadalcanal'], ['id' => [76, 77, 78]]);
        $this->update($this->tableName, ['tz_value' => 'Pacific/Fiji'], ['id' => [79, 80, 81]]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'tz_value');
    }
    
}
