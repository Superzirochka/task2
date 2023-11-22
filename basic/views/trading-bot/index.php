<?php

use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Html;
use dosamigos\chartjs\ChartJs;
/* @var $this yii\web\View */

$this->title = 'Trading Bot';
?>
<h1><?= $this->title ?></h1>

<? //$account = (json_decode($res, true));
//print_r($account['balances'])
$symbols = [];

foreach ($pairs[0]['symbols'] as $val) {
    $symbols[$val] = $val;
}
$side = [
    'BUY' => 'BUY',
    'SELL' => 'SELL'
];
$type = [
    'LIMIT' => 'LIMIT',
    'MARKET' => 'MARKET',
    'STOP_LOSS' => 'STOP_LOSS',
    'STOP_LOSS_LIMIT' => 'STOP_LOSS_LIMIT',
    'TAKE_PROFIT' => 'TAKE_PROFIT',
    'TAKE_PROFIT_LIMIT' => 'TAKE_PROFIT_LIMIT',
    'LIMIT_MAKER' => 'LIMIT_MAKER'
];

?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<div class="accordion" id="accordionExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Balances
            </button>
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Trading pair
            </button>
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Open Orders
            </button>
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                Bot
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse " aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="accordion-body">
                <div class="row">
                    <? foreach ($balances as $balance) : ?>

                        <div class="col">
                            <div class="card" style="width: 18rem;">

                                <div class="card-body">
                                    <h5 class="card-title"><?= $balance['asset'] ?></h5>
                                    <p class="card-text">Free balance: <?= $balance['free'] ?></p>
                                    <p class="card-text">Locked balance: <?= $balance['locked'] ?></p>

                                </div>
                            </div>
                        </div>
                    <? endforeach ?>
                </div>


            </div>
        </div>
    </div>
    <div class="accordion-item">

        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
            <div class="accordion-body">
                <p id="error"></p>
                <? Pjax::begin([
                    // Опции Pjax
                ]) ?>


                <?
                print_r($pairs);
                $sym = json_encode($pairs[0]['symbols']);
                echo '<br>';
                $interval = '1m';
                $new_final = '';
                print_r($interval)
                ?>

                <div class="row" id="info">

                </div>
                <? Pjax::end(); ?>

            </div>
        </div>
    </div>
    <div class="accordion-item">

        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
            <div class="accordion-body">
                <?
                print_r('all orders');
                echo '<br>';
                print_r($order);
                echo '<br>';
                ?><br>
                <?
                print_r('open orders: ');
                echo '<br>';
                print_r($open_order);
                echo '<br>';
                ?>
                <?
                // foreach ($arr_info as $info) {
                //     print_r($info);
                //     echo '<br>';
                // }

                ?>
            </div>
        </div>
    </div>
    <div class="accordion-item">

        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
            <div class="accordion-body">
                <div class="col-sm-12">

                    <? Pjax::begin([
                        // Опции Pjax
                    ]) ?>
                    <?php $form = ActiveForm::begin([
                        // 'action' => 'index',
                        'options' => [
                            'method' => 'POST', 'data' => ['pjax' => true] //, 'autofocus' => true
                        ]
                    ]) ?>
                    <div class="form-row">
                        <div class="col-md-3 ">


                            <?= $form->field($model, 'symbol')->dropDownList(
                                $symbols,
                                [
                                    'prompt' => 'Choose the symbol...',
                                    'id' => 'symbol'
                                ]
                            ); ?>




                        </div>
                        <div class="col-md-3">

                            <?= $form->field($model, 'type')->dropDownList(
                                $type,
                                [
                                    'prompt' => 'Choose the type...',
                                    'id' => 'type'

                                ]
                            ) ?>

                            <script>
                                $('#type').on('change', function() {
                                    let data = $(this).val();
                                    console.log($(this).val());
                                    if ($(this).val() === 'LIMIT') {
                                        $('#div_quantity').show();
                                        $('#div_price').show();
                                        $('#div_stopPrice').hide();
                                        $('#div_icebergQty').hide();
                                        $('#div_trailingDelta').hide();
                                        $('#div_quoteOrderQty').hide();
                                    } else if ($(this).val() === 'MARKET') {
                                        $('#div_quantity').show();
                                        $('#div_price').hide();
                                        $('#div_stopPrice').hide();
                                        $('#div_icebergQty').hide();
                                        $('#div_trailingDelta').hide();
                                        $('#div_quoteOrderQty').show();

                                    } else if ($(this).val() === 'STOP_LOSS') {
                                        $('#div_quantity').show();
                                        $('#div_price').hide();
                                        $('#div_stopPrice').show();
                                        $('#div_icebergQty').hide();
                                        $('#div_trailingDelta').show();
                                        $('#div_quoteOrderQty').hide();

                                    } else if ($(this).val() === 'STOP_LOSS_LIMIT') {
                                        $('#div_quantity').show();
                                        $('#div_price').show();
                                        $('#div_stopPrice').show();
                                        $('#div_icebergQty').hide();
                                        $('#div_trailingDelta').show();
                                        $('#div_quoteOrderQty').hide();

                                    } else if ($(this).val() === 'TAKE_PROFIT') {
                                        $('#div_quantity').show();
                                        $('#div_price').hide();
                                        $('#div_stopPrice').show();
                                        $('#div_icebergQty').hide();
                                        $('#div_trailingDelta').show();
                                        $('#div_quoteOrderQty').hide();

                                    } else if ($(this).val() === 'TAKE_PROFIT_LIMIT') {
                                        $('#div_quantity').show();
                                        $('#div_price').show();
                                        $('#div_stopPrice').show();
                                        $('#div_icebergQty').hide();
                                        $('#div_trailingDelta').show();
                                        $('#div_quoteOrderQty').hide();

                                    } else {
                                        $('#div_quantity').show();
                                        $('#div_price').show();
                                        $('#div_stopPrice').hide();
                                        $('#div_icebergQty').hide();
                                        $('#div_trailingDelta').hide();
                                        $('#div_quoteOrderQty').hide();
                                    }

                                    return false;

                                });
                            </script>
                        </div>
                        <div class="col-md-3 ">

                            <?= $form->field($model, 'side')->dropDownList(
                                $side,
                                [
                                    'prompt' => 'Choose the side...',
                                    'id' => 'side'

                                ]
                            ) ?>

                        </div>
                        <div class="col-md-3 " id="div_quantity">

                            <?= $form->field($model, 'quantity')->textInput(

                                [
                                    'prompt' => 'Choose the quantity...',
                                    'id' => 'quantity'

                                ]
                            ) ?>

                        </div>


                    </div>
                    <div class="form-row">
                        <div class="col-md-3 " id="div_price">

                            <?= $form->field($model, 'price')->textInput(

                                [
                                    'prompt' => 'Enter the price...',
                                    'id' => 'price',
                                    'value' => 0

                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3 " id="div_quoteOrderQty">
                            <?= $form->field($model, 'quoteOrderQty')->textInput(

                                [
                                    'prompt' => 'Enter the quoteOrderQty...',
                                    'id' => 'quoteOrderQty',
                                    'value' => 0

                                ]
                            ) ?>

                        </div>
                        <div class="col-md-3 " id="div_stopPrice">

                            <?= $form->field($model, 'stopPrice')->textInput(

                                [
                                    'prompt' => 'Enter the stopPrice...',
                                    'id' => 'stopPrice',
                                    'value' => 1

                                ]
                            ) ?>

                        </div>
                        <div class="col-md-3 " id="div_icebergQty">

                            <?= $form->field($model, 'icebergQty')->textInput(

                                [
                                    'prompt' => 'Enter the icebergQty...',
                                    'id' => 'icebergQty',
                                    'value' => 0

                                ]
                            ) ?>

                        </div>
                        <div class="col-md-3 " id="div_trailingDelta">

                            <?= $form->field($model, 'trailingDelta')->textInput(

                                [
                                    'prompt' => 'Enter the trailingDelta...',
                                    'id' => 'trailingDelta',
                                    'value' => 0

                                ]
                            ) ?>

                        </div>
                    </div>
                    <div class="card-form__row align-items-center">
                        <?= Html::submitButton('PAY', ['class' => 'btn background-green']) ?>

                        <a onclick=" javascript:history.back();" class="btn background-grey " title="Back">Back
                        </a>
                    </div>


                    <? ActiveForm::end() ?>
                    <? Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script>
        function updateInfo() {
            //let interval=
            const error = document.getElementById('error');
            const list = document.getElementById('info');
            fetch('information?interval=<?= $interval ?>', {})
                .then(res => res.ok ? res.json() : Promise.reject(res))
                .then((data) => {
                        let html = '';
                        let id = 1;
                        let symbol = <?= $sym ?>;
                        console.log(data);
                        //   data = data.reverse();
                        for (let i = 0; i < symbol.length; i++) {
                            let key_sym = 'BINANCE:' + symbol[i];
                            let data_info = data[key_sym];
                            // console.log(data_info.indicators);
                            //  html1 = data_info.forEach(function(d) {
                            // let html = '';
                            let id = 1;
                            if (data_info !== null) {
                                html += '<div class="col"><div class="card" style="width: 18rem;"><div class="card-body">';
                                html += '<h5 class="card-title">' + data_info.symbol + '</h5>';
                                html += '<h5 class="card-title">' + data_info.time + '</h5>';
                                html += '<h5 class="card-title">Recomendation : <i id="rec">' + data_info.summary.RECOMMENDATION + '</i></h5>';
                                html += '<p>Indicators</p><ol>';
                                if (data_info.indicators !== undefined) {
                                    //  console.log(data_info.indicators);
                                    //                                 Object.keys(myObj).forEach(key => {
                                    //     console.log(key + ' - ' + myObj[key]) // key - value
                                    // })
                                    Object.keys(data_info.indicators).forEach(
                                        (key) => {
                                            //     console.log(data_info.indicators[key]);
                                            html += ' <ul>' + key + ' : ' + data_info.indicators[key] + '</ul>';

                                        }
                                    );
                                }
                                html += '</ol><p> Oscillators </p>';
                                if (data_info.oscillators !== undefined) {
                                    Object.keys(data_info.oscillators).forEach(
                                        function(key) {
                                            if (key == "COMPUTE") {
                                                html += '<p class = "card-text" >COMPUTE :</p><ol>';
                                                Object.keys(data_info.oscillators["COMPUTE"]).forEach(
                                                    function(k) {
                                                        html += '<ul>' + k + ' : ' + data_info.oscillators["COMPUTE"][k] + '</ul>';
                                                    });
                                            } else {
                                                if (Array.isArray(data_info.oscillators[key])) {

                                                    Object.keys(data_info.oscillators[key]).forEach(
                                                        function(k) {

                                                            html += '<p class = "card-text" >' + k + ' : ' + data_info.oscillators[key][k];

                                                        });

                                                } else {
                                                    html += '<p class = "card-text" >' + key + ' : ' + data_info.oscillators[key];
                                                }
                                            }
                                        }
                                    );
                                }
                                html += '<p> Summary ';
                                if (data_info.summary !== undefined) {
                                    Object.keys(data_info.summary).forEach(
                                        function(key) {
                                            html += '<p class = "card-text" >' + key + ' : ' + data_info.summary[key];
                                        }
                                    );
                                }


                                html += '</div> </div> </div>';
                                //  return html;
                                // });
                            }
                        }

                        list.innerHTML = html;
                    }

                )
                .catch((data) => {
                    console.log(data);

                    error.innerText = 'Can`t fetch data';
                });

        }
        updateInfo();
        setInterval(updateInfo, 500000);
    </script>
    <script>
        function showMessage(messageHTML) {
            $('#chat-box').append(messageHTML);
        }

        $(document).ready(function() {
            var websocket = new WebSocket("wss://testnet.binance.vision/stream");
            websocket.onopen = function(event) {
                console.log(event);
                showMessage("<div class='chat-connection-ack'>Connection is established!</div>");
            }
            websocket.onmessage = function(event) {
                var Data = JSON.parse(event.data);
                showMessage("<div class='" + Data.message_type + "'>" + Data.message + "</div>");
                $('#chat-message').val('');
            };

            websocket.onerror = function(event) {
                var Data = JSON.parse(event.data);
                console.log(Data);
                showMessage("<div class='error'>" + Data + "</div>");

            };
            websocket.onclose = function(event) {
                var Data = JSON.parse(event.data);
                showMessage("<div class='chat-connection-ack'>Connection Closed</div>");
            };

            $('#frmChat').on("submit", function(event) {
                event.preventDefault();
                $('#chat-user').attr("type", "hidden");
                var messageJSON = {
                    chat_user: $('#chat-user').val(),
                    chat_message: $('#chat-message').val()
                };
                websocket.send(JSON.stringify(messageJSON));
            });
        });
    </script>
    <form name="frmChat" id="frmChat">
        <div id="chat-box"></div>
        <input type="text" name="chat-user" id="chat-user" placeholder="Name" class="chat-input" required />
        <input type="text" name="chat-message" id="chat-message" placeholder="Message" class="chat-input chat-message" required />
        <input type="submit" id="btnSend" name="send-chat-message" value="Send">
    </form>
</div>