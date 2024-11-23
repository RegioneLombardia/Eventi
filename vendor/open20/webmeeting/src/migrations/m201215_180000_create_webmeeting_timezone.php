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
class m201215_180000_create_webmeeting_timezone extends Migration
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
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if ($this->db->schema->getTableSchema($this->tableName, true) === null) {
            $this->createTable(
                $this->tableName,
                [
                    'id' => $this->primaryKey(),
                    'timezone' => $this->char(255)->defaultValue(null)->comment('Title'),
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
        
        $this->insert($this->tableName, ['id' =>  1, 'timezone' => '(UTC-12:00) International Date Line West']);
        $this->insert($this->tableName, ['id' =>  2, 'timezone' => '(UTC-11:00) Coordinated Universal Time-11']);
        $this->insert($this->tableName, ['id' =>  3, 'timezone' => '(UTC-10:00) Hawaii']);
        $this->insert($this->tableName, ['id' =>  4, 'timezone' => '(UTC-09:00) Alaska']);
        $this->insert($this->tableName, ['id' =>  5, 'timezone' => '(UTC-08:00) Pacific Time (US &amp; Canada)']);
        $this->insert($this->tableName, ['id' =>  6, 'timezone' => '(UTC-08:00) Baja California']);
        $this->insert($this->tableName, ['id' =>  7, 'timezone' => '(UTC-07:00) Arizona']);
        $this->insert($this->tableName, ['id' =>  8, 'timezone' => '(UTC-07:00) Chihuahua, La Paz, Mazatlan']);
        $this->insert($this->tableName, ['id' =>  9, 'timezone' => '(UTC-07:00) Mountain Time (US &amp; Canada)']);
        $this->insert($this->tableName, ['id' => 10, 'timezone' => '(UTC-06:00) Central Time (US &amp; Canada)']);
        $this->insert($this->tableName, ['id' => 11, 'timezone' => '(UTC-06:00) Guadalajara, Mexico City, Monterrey']);
        $this->insert($this->tableName, ['id' => 12, 'timezone' => '(UTC-06:00) Saskatchewan']);
        $this->insert($this->tableName, ['id' => 13, 'timezone' => '(UTC-06:00) Central America']);
        $this->insert($this->tableName, ['id' => 14, 'timezone' => '(UTC-05:00) Bogota, Lima, Quito, Rio Branco']);
        $this->insert($this->tableName, ['id' => 15, 'timezone' => '(UTC-05:00) Indiana (East)']);
        $this->insert($this->tableName, ['id' => 16, 'timezone' => '(UTC-05:00) Eastern Time (US &amp; Canada)']);
        $this->insert($this->tableName, ['id' => 17, 'timezone' => '(UTC-04:00) Caracas']);
        $this->insert($this->tableName, ['id' => 18, 'timezone' => '(UTC-04:00) Atlantic Time (Canada)']);
        $this->insert($this->tableName, ['id' => 19, 'timezone' => '(UTC-04:00) Georgetown, La Paz, Manaus, San Juan']);
        $this->insert($this->tableName, ['id' => 20, 'timezone' => '(UTC-03:30) Newfoundland']);
        $this->insert($this->tableName, ['id' => 21, 'timezone' => '(UTC-03:00) Asuncion']);
        $this->insert($this->tableName, ['id' => 22, 'timezone' => '(UTC-03:00) Brasilia']);
        $this->insert($this->tableName, ['id' => 23, 'timezone' => '(UTC-03:00) City of Buenos Aires']);
        $this->insert($this->tableName, ['id' => 24, 'timezone' => '(UTC-03:00) Montevideo']);
        $this->insert($this->tableName, ['id' => 25, 'timezone' => '(UTC-03:00) Nuuk']);
        $this->insert($this->tableName, ['id' => 26, 'timezone' => '(UTC-03:00) Cayenne, Fortaleza']);
        $this->insert($this->tableName, ['id' => 27, 'timezone' => '(UTC-03:00) Santiago']);
        $this->insert($this->tableName, ['id' => 28, 'timezone' => '(UTC-02:00) Coordinated Universal Time-02']);
        $this->insert($this->tableName, ['id' => 29, 'timezone' => '(UTC-01:00) Azores']);
        $this->insert($this->tableName, ['id' => 30, 'timezone' => '(UTC-01:00) Cabo Verde Is.']);
        $this->insert($this->tableName, ['id' => 31, 'timezone' => '(UTC+00:00) Dublin, Edinburgh, Lisbon, London']);
        $this->insert($this->tableName, ['id' => 32, 'timezone' => '(UTC+00:00) Monrovia, Reykjavik']);
        $this->insert($this->tableName, ['id' => 33, 'timezone' => '(UTC+01:00) Brussels, Copenhagen, Madrid, Paris']);
        $this->insert($this->tableName, ['id' => 34, 'timezone' => '(UTC+01:00) Casablanca']);
        $this->insert($this->tableName, ['id' => 35, 'timezone' => '(UTC+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague']);
        $this->insert($this->tableName, ['id' => 36, 'timezone' => '(UTC+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna']);
        $this->insert($this->tableName, ['id' => 37, 'timezone' => '(UTC+01:00) West Central Africa']);
        $this->insert($this->tableName, ['id' => 38, 'timezone' => '(UTC+02:00) Amman']);
        $this->insert($this->tableName, ['id' => 39, 'timezone' => '(UTC+02:00) Athens, Bucharest']);
        $this->insert($this->tableName, ['id' => 40, 'timezone' => '(UTC+02:00) Chisinau']);
        $this->insert($this->tableName, ['id' => 41, 'timezone' => '(UTC+02:00) Cairo']);
        $this->insert($this->tableName, ['id' => 42, 'timezone' => '(UTC+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius']);
        $this->insert($this->tableName, ['id' => 43, 'timezone' => '(UTC+02:00) Harare, Pretoria']);
        $this->insert($this->tableName, ['id' => 44, 'timezone' => '(UTC+02:00) Jerusalem']);
        $this->insert($this->tableName, ['id' => 45, 'timezone' => '(UTC+02:00) Windhoek']);
        $this->insert($this->tableName, ['id' => 46, 'timezone' => '(UTC+03:00) Istanbul']);
        $this->insert($this->tableName, ['id' => 47, 'timezone' => '(UTC+03:00) Moscow, St. Petersburg, Volgograd']);
        $this->insert($this->tableName, ['id' => 48, 'timezone' => '(UTC+03:00) Nairobi']);
        $this->insert($this->tableName, ['id' => 49, 'timezone' => '(UTC+03:00) Kuwait, Riyadh']);
        $this->insert($this->tableName, ['id' => 50, 'timezone' => '(UTC+03:30) Tehran']);
        $this->insert($this->tableName, ['id' => 51, 'timezone' => '(UTC+04:00) Abu Dhabi, Muscat']);
        $this->insert($this->tableName, ['id' => 52, 'timezone' => '(UTC+04:00) Baku']);
        $this->insert($this->tableName, ['id' => 53, 'timezone' => '(UTC+04:00) Yerevan']);
        $this->insert($this->tableName, ['id' => 54, 'timezone' => '(UTC+04:30) Kabul']);
        $this->insert($this->tableName, ['id' => 55, 'timezone' => '(UTC+05:00) Islamabad, Karachi']);
        $this->insert($this->tableName, ['id' => 56, 'timezone' => '(UTC+05:00) Ekaterinburg']);
        $this->insert($this->tableName, ['id' => 57, 'timezone' => '(UTC+05:30) Sri Jayawardenepura']);
        $this->insert($this->tableName, ['id' => 58, 'timezone' => '(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi']);
        $this->insert($this->tableName, ['id' => 59, 'timezone' => '(UTC+05:45) Kathmandu']);
        $this->insert($this->tableName, ['id' => 60, 'timezone' => '(UTC+06:00) Astana']);
        $this->insert($this->tableName, ['id' => 61, 'timezone' => '(UTC+06:30) Yangon (Rangoon)']);
        $this->insert($this->tableName, ['id' => 62, 'timezone' => '(UTC+07:00) Bangkok, Hanoi, Jakarta']);
        $this->insert($this->tableName, ['id' => 63, 'timezone' => '(UTC+07:00) Novosibirsk']);
        $this->insert($this->tableName, ['id' => 64, 'timezone' => '(UTC+08:00) Beijing, Chongqing, Hong Kong, Urumqi']);
        $this->insert($this->tableName, ['id' => 65, 'timezone' => '(UTC+08:00) Kuala Lumpur, Singapore']);
        $this->insert($this->tableName, ['id' => 66, 'timezone' => '(UTC+08:00) Perth']);
        $this->insert($this->tableName, ['id' => 67, 'timezone' => '(UTC+08:00) Taipei']);
        $this->insert($this->tableName, ['id' => 68, 'timezone' => '(UTC+09:00) Seoul']);
        $this->insert($this->tableName, ['id' => 69, 'timezone' => '(UTC+09:00) Osaka, Sapporo, Tokyo']);
        $this->insert($this->tableName, ['id' => 70, 'timezone' => '(UTC+09:00) Yakutsk']);
        $this->insert($this->tableName, ['id' => 71, 'timezone' => '(UTC+09:30) Darwin']);
        $this->insert($this->tableName, ['id' => 72, 'timezone' => '(UTC+10:00) Brisbane']);
        $this->insert($this->tableName, ['id' => 73, 'timezone' => '(UTC+10:00) Guam, Port Moresby']);
        $this->insert($this->tableName, ['id' => 74, 'timezone' => '(UTC+10:00) Vladivostok']);
        $this->insert($this->tableName, ['id' => 75, 'timezone' => '(UTC+10:30) Adelaide']);
        $this->insert($this->tableName, ['id' => 76, 'timezone' => '(UTC+11:00) Hobart']);
        $this->insert($this->tableName, ['id' => 77, 'timezone' => '(UTC+11:00) Solomon Is., New Caledonia']);
        $this->insert($this->tableName, ['id' => 78, 'timezone' => '(UTC+11:00) Canberra, Melbourne, Sydney']);
        $this->insert($this->tableName, ['id' => 79, 'timezone' => '(UTC+13:00) Fiji']);
        $this->insert($this->tableName, ['id' => 80, 'timezone' => '(UTC+13:00) Nuku\'alofa']);
        $this->insert($this->tableName, ['id' => 81, 'timezone' => '(UTC+13:00) Auckland, Wellington']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropTable($this->tableName);
    }
    
}
