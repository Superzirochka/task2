<?php

use app\models\Countries;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

// $autobrend = ManufacturerAuto::find()->select('Id, Marka, Img, link')->all();
// $autobrend = ManufacturerAuto::find()->select('Id, Marka, Img, link')->all();
// $modif = Modification::find()->select(['IdModelAuto', 'IdEngine', 'IdValueEngine', 'Id'])->indexBy('Id')->all();
$textModif = ['-'];

?>
<div class="col-sm-12">
    <? Pjax::begin([
        // Опции Pjax
    ]) ?>
    <?php $form = ActiveForm::begin([
        'action' => 'index',
        'options' => [
            'method' => 'POST', 'data' => ['pjax' => true] //, 'autofocus' => true
        ]
    ]) ?>
    <div class="form-row">
        <div class="col-md-3 ">


            <?= $form->field($model, 'countries')->dropDownList(
                Countries::find()->select(['country', 'id'])->indexBy('id')->column(),
                [
                    'prompt' => 'Choose the country...',
                    'id' => 'countries'
                ]
            ); ?>


            <?php
            $js = <<<JS
    $('#countries').on('change', function(){
        let data = $(this).val();
        $.ajax({
            url: 'index',
            type: 'GET',         
            data:{'data' : data},
            
            success: function(res){
                $("#regions").html(res);
                $("#cities").html('<option>-</option>');
                $("#regions").focus();
                console.log(res);
            },
            error: function(){
                //let statusCode = request.status;
                console.log('Error!'+data);
            }
        });
        return false;
    });
JS;

            $this->registerJs($js);
            ?>

        </div>
        <div class="col-md-3">

            <?= $form->field($model, 'regions')->dropDownList(
                $textModif,
                [
                    'prompt' => 'Choose the region...',
                    'id' => 'regions'

                ]
            ) ?>

            <?php
            $js = <<<JS
    $('#regions').on('change', function(){
        let data = $(this).val();
        $.ajax({
            url: 'index',
            type: 'GET',
          //  dataType : 'json',
            data:{'regions' : data},
            
            success: function(res){
                $("#cities").html(res);
                $("#cities").focus();
                console.log(res);
            },
            error: function(){
                //let statusCode = request.status;
                console.log('Error!'+res);
            }
        });
        return false;
    });
JS;

            $this->registerJs($js);
            ?>
        </div>
        <div class="col-md-3 ">

            <?= $form->field($model, 'cities')->dropDownList(
                $textModif,
                [
                    'prompt' => 'Choose the city...',
                    'id' => 'cities'

                ]
            ) ?>



        </div>


    </div>





    <?php ActiveForm::end() ?>
    <? Pjax::end(); ?>
</div>