<?php

namespace mata\form\models;

use Yii;

/**
 * This is the model class for table "form".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $ReferencedTable
 */
class Form extends \matacms\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'mata_form';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['Name', 'ReferencedTable'], 'required'],
            [['Name'], 'string', 'max' => 128],
            [['ReferencedTable'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'Id' => 'ID',
            'Name' => 'Name',
            'ReferencedTable' => 'Referenced Table',
        ];
    }
}