<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class AutoForm extends Model
{
    public $countries;
    public $regions;
    public $cities;



    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // удалить пробелы для всех полей формы
            [['countries', 'regions', 'cities'], 'required'],
            [['countries', 'regions', 'cities'], 'integer'],

        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'countries' => Yii::t('app', 'Choose the country '),
            'regions' =>  Yii::t('app', 'Choose the region'),
            'cities' =>  Yii::t('app', 'Choose the city'),


        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
}
