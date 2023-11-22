<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\httpclient\Client;
use app\common\classes\Compute;
use app\common\classes\TradingView;
use app\common\classes\Analysis;
use app\common\classes\Binance;
use app\models\TradeForm;
use yii\bootstrap4\Button;

class TradingBotController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $interval = '1m';
        $binance = new Binance();
        $res = $binance->account();
        $account = (json_decode($res, true));

        $model = new TradeForm();
        if (!$this->existsTradingPair()) {
            $this->setTradingPaire();
        }
        //  $order = $binance->new_order("SELL", "LIMIT", "ETHUSDT", '0.01', '1614.05', []);
        $pairs = json_decode($this->getTradingPair(), true);
        $analise = new Analysis($pairs->screener, $pairs->exchange, $pairs->symbols, $interval);
        $arr_info = [];
        foreach ($pairs[0]['symbols'] as $res) {
            array_push($arr_info, $binance->getPriceFilter($res));
        }


        $final = $analise->get_multiple_analysis($pairs[0]['screener'], $interval, $pairs[0]['exchange'], $pairs[0]['symbols']);
        $session = Yii::$app->session;
        $session->open();
        Yii::$app->session->set('savedData', json_encode($final, JSON_FORCE_OBJECT));

        $quantity = '0.01';
        $symbol = 'ETHUSDT';
        $price = $binance->get_btc_price($symbol);
        $price = json_decode($price, true);
        // $order = $binance->place_buy_order($symbol, $quantity, $price['price']);
        $all_orders = $binance->allOrders("BTCUSDT");
        // $data['orderId'] = 1165202;
        // $open_order = $binance->deleteOrder($symbol, $data);
        $open_order = $binance->openOrders($symbol);
        if ($model->load($this->request->post())) {

            $cur_price = ($binance->get_btc_price($model->symbol));

            $cur_price = json_decode($cur_price, true);

            // for every other type, lets format it appropriately
            $model->price = number_format($model->price, 8, '.', '');

            $price =  ($cur_price['price'] * $model->price);
            $flags = [];
            if (!empty($model->stopPrice) || $model->stopPrice != 0) {
                $flags['stopPrice'] = ($cur_price['price'] * $model->stopPrice);
            }
            if (!empty($model->trailingDelta) || $model->trailingDelta != 0) {
                $flags['trailingDelta'] = $model->trailingDelta;
            }

            if (!empty($model->icebergQty) || $model->icebergQty != 0) {
                $flags['icebergQty'] = $model->icebergQty;
            }
            if (!empty($model->quoteOrderQty) || $model->quoteOrderQty != 0) {
                $flags['quoteOrderQty'] = $model->quoteOrderQty;
            }
            // print_r($cur_price);
            // exit;

            $order = $binance->new_order($model->side, $model->type, $model->symbol, $model->quantity, $price, $flags);
            print_r($order);
            // return $this->render(['view', 'order' => $order]);
        }
        return $this->render('index', [
            'balances' => $account['balances'],
            'pairs' =>  $pairs,
            'arr_info' => $arr_info,
            'final' =>  $final,
            'order' => $all_orders,
            'open_order' => $open_order,
            'model' => $model
        ]);
    }

    public function actionInformation($interval)
    {
        $session = Yii::$app->session;
        $session->open();
        // if (\Yii::$app->request->isAjax) {
        $interval = ($_POST['data']);
        if (empty($_POST['data'])) {
            $interval = '1m';
        }
        $pairs = json_decode($this->getTradingPair(), true);
        $analise = new Analysis($pairs->screener, $pairs->exchange, $pairs->symbols, $interval);
        $final =  $analise->get_multiple_analysis($pairs[0]['screener'], $interval, $pairs[0]['exchange'], $pairs[0]['symbols']);

        if ($this->existsTradingRecomend()) {
            $old_info = json_decode($this->getTradingRecomend(), true);
        }
        foreach ($final as $key => $val) {
            if ($val == null) {
                unset($final[$key]);
                // print_r($old_info);
                // exit;
                foreach ($old_info[0] as $time => $info) {
                    // print_r($old_info[0][$time]);
                    // exit;
                    $final[$key] = $info[$key];
                }
            }
        }
        $this->setTradingRecomend($final);
        Yii::$app->session->set('savedData', json_encode($final, JSON_FORCE_OBJECT));
        return json_encode($final, JSON_FORCE_OBJECT);
        // 'ok';
        //}
    }

    public function getTradingView($screener, $interval, $exchange, $symbol)
    {
        $analise = new Analysis($screener, $exchange, [$symbol], $interval);
        $final = $analise->get_multiple_analysis($screener, $interval, $exchange, [$symbol]);
        return $final;
    }


    public function actionView($order)
    {
        return $this->render('view', ['order' => $order]);
    }

    public function getTradingPair()
    {
        $basePath = Yii::getAlias('@app');

        $filePath =  $basePath . "/cache/TradingPair.json";
        if ($this->existsTradingPair()) {
            //  return file_get_contents(json_decode($filePath, true));
            $value = file_get_contents($filePath);
            return ($value);
        }
        return null;
    }

    public function existsTradingPair()
    {
        $basePath = Yii::getAlias('@app');
        $filePath = $basePath . "/cache/TradingPair.json";
        if (file_exists($filePath)) {
            return true;
        }
        return false;
    }

    public function setTradingPaire()
    {
        $basePath = Yii::getAlias('@app');
        $filePath =  $basePath . "/cache/TradingPair.json";
        $value =
            [
                [
                    "exchange" => "BINANCE",
                    "screener" => "crypto",
                    "symbols" => ["ETHUSDT", "BTCUSDT"]
                ]

            ];
        return file_put_contents($filePath, json_encode($value));
    }

    public function getTradingRecomend()
    {
        $basePath = Yii::getAlias('@app');

        $filePath =  $basePath . "/cache/Recomend.json";
        if ($this->existsTradingPair()) {
            //  return file_get_contents(json_decode($filePath, true));
            $value = file_get_contents($filePath);
            return ($value);
        }
        return null;
    }

    public function existsTradingRecomend()
    {
        $basePath = Yii::getAlias('@app');
        $filePath = $basePath . "/cache/Recomend.json";
        if (file_exists($filePath)) {
            return true;
        }
        return false;
    }

    public function setTradingRecomend($data)
    {
        $basePath = Yii::getAlias('@app');
        $filePath =  $basePath . "/cache/Recomend.json";
        $time = time();
        foreach ($data as $key => $val) {
            if ($val == null) {
                unset($data[$key]);
            }
        }
        if ($this->existsTradingRecomend()) {
            $value = json_decode($this->getTradingRecomend());
        } else {
            $value = [];
        }
        array_unshift($value, [$time => $data]);

        return file_put_contents($filePath, json_encode($value));
    }
}
