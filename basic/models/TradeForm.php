<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class TradeForm extends Model
{
    public $symbol;
    public $type;
    public $side;
    public $quantity;
    public $price;
    public $stopPrice;
    public $trailingDelta;
    public $icebergQty;
    public $quoteOrderQty;





    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [

            [['symbol', 'type', 'side'], 'required'],
            [['quantity', 'price', 'stopPrice', 'icebergQty', 'quoteOrderQty'], 'number'],
            [['symbol', 'type', 'side'], 'string'],

        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'symbol' => Yii::t('app', 'Choose the symbol '),
            'type' =>  Yii::t('app', 'Choose the type'),
            'side' =>  Yii::t('app', 'Choose the side'),
            'quantity' => Yii::t('app', 'Choose the quantity '),
            'price' =>  Yii::t('app', 'Choose the price'),
            'stopPrice' =>  Yii::t('app', 'Choose the stopPrice'),
            'icebergQty' => Yii::t('app', 'Choose the icebergQty '),

        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
}
