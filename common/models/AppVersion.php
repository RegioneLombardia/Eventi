<?php

namespace common\models;


/**
 * This is the base-model class for table "app_version".
 *
 * @property integer $id
 * @property integer $version
 * @property integer $days_notice
 * @property string $version_name
 * @property string $from
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class AppVersion extends \open20\amos\core\record\Record {


    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'app_version';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['version', 'days_notice'], 'integer'],
            [['version_name'], 'string'],
            [['created_at', 'updated_at', 'deleted_at', 'from'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [

        ];
    }

}
