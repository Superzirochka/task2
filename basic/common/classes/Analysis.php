<?php

namespace app\common\classes;

use app\common\classes\Compute;
use app\common\classes\TradingView;
use yii\httpclient\Client;

class Analysis
{
    public $exchange;
    public $symbols;
    public $screener;
    public $time;
    public $interval;
    public $summary;
    public $oscillators;
    public $moving_averages;
    public $indicators;

    public function __construct($screener = "", $exchange = "", $symbols = "", $interval = "")
    {
        $this->screener = $screener;
        $this->exchange = $exchange;
        $this->symbols = $symbols;
        $this->interval = $interval;
        $trading = new TradingView;
        $this->indicators = $trading->getIndicators();
    }






    public  function calculate($indicators, $screener, $symbol, $exchange, $interval)
    {


        $oscillators_counter = ["BUY" => 0, "SELL" => 0, "NEUTRAL" => 0];
        $ma_counter = ["BUY" => 0, "SELL" => 0, "NEUTRAL" => 0];
        $computed_oscillators = [];
        $computed_ma = [];

        // $indicators = array_values($indicators);
        // $compute = new Compute;
        // RECOMMENDATIONS
        if (!in_array(null, array_slice($indicators, 0, 2))) {
            $recommend_oscillators = Compute::Recommend($indicators[0]);
            $recommend_summary = Compute::Recommend($indicators[1]);
            $recommend_moving_averages = Compute::Recommend($indicators[2]);
        } else {
            return null;
        }

        // OSCILLATORS
        // RSI (14)
        if (!in_array(null, array_slice($indicators, 3, 2))) {
            $computed_oscillators["RSI"] = Compute::RSI($indicators[3], $indicators[4]);
            $oscillators_counter[$computed_oscillators["RSI"]] += 1;
        }
        // Stoch %K
        if (!in_array(null, array_slice($indicators, 5, 4))) {
            $computed_oscillators["STOCH.K"] = Compute::Stoch($indicators[5], $indicators[6], $indicators[7], $indicators[8]);
            $oscillators_counter[$computed_oscillators["STOCH.K"]] += 1;
        }
        // CCI (20)
        if (!in_array(null, array_slice($indicators, 9, 2))) {
            $computed_oscillators["CCI"] = compute::CCI20($indicators[9], $indicators[10]);
            $oscillators_counter[$computed_oscillators["CCI"]] += 1;
        }
        // ADX (14)
        if (!in_array(null, array_slice($indicators, 11, 5))) {
            $computed_oscillators["ADX"] = compute::ADX($indicators[11], $indicators[12], $indicators[13], $indicators[14], $indicators[15]);
            $oscillators_counter[$computed_oscillators["ADX"]] += 1;
        }
        // AO
        if (!in_array(null, array_slice($indicators, 16, 2)) && $indicators[86] !== null) {
            $computed_oscillators["AO"] = compute::AO($indicators[16], $indicators[17], $indicators[86]);
            $oscillators_counter[$computed_oscillators["AO"]] += 1;
        }
        // Mom (10)
        if (!in_array(null, array_slice($indicators, 18, 2))) {
            $computed_oscillators["Mom"] = compute::Mom($indicators[18], $indicators[19]);
            $oscillators_counter[$computed_oscillators["Mom"]] += 1;
        }
        // MACD
        if (!in_array(null, array_slice($indicators, 20, 2))) {
            $computed_oscillators["MACD"] = compute::MACD($indicators[20], $indicators[21]);
            $oscillators_counter[$computed_oscillators["MACD"]] += 1;
        }
        // Stoch RSI
        if ($indicators[22] !== null) {
            $computed_oscillators["Stoch.RSI"] = compute::Simple($indicators[22]);
            $oscillators_counter[$computed_oscillators["Stoch.RSI"]] += 1;
        }
        // W%R
        if ($indicators[24] !== null) {
            $computed_oscillators["W%R"] = compute::Simple($indicators[24]);
            $oscillators_counter[$computed_oscillators["W%R"]] += 1;
        }
        // BBP
        if ($indicators[26] !== null) {
            $computed_oscillators["BBP"] = compute::Simple($indicators[26]);
            $oscillators_counter[$computed_oscillators["BBP"]] += 1;
        }
        // UO
        if ($indicators[28] !== null) {
            $computed_oscillators["UO"] = compute::Simple($indicators[28]);
            $oscillators_counter[$computed_oscillators["UO"]] += 1;
        }

        // MOVING AVERAGES
        $ma_list = ["EMA10", "SMA10", "EMA20", "SMA20", "EMA30", "SMA30", "EMA50", "SMA50", "EMA100", "SMA100", "EMA200", "SMA200"];
        $close = $indicators[30];
        $ma_list_counter = 0;
        for ($index = 33; $index < 45; $index++) {
            if ($indicators[$index] !== null) {
                $computed_ma[$ma_list[$ma_list_counter]] = compute::MA($indicators[$index], $close);
                $ma_counter[$computed_ma[$ma_list[$ma_list_counter]]] += 1;
                $ma_list_counter += 1;
            }
        }

        // MOVING AVERAGES, pt 2
        // ICHIMOKU
        if ($indicators[45] !== null) {
            $computed_ma["Ichimoku"] = compute::Simple($indicators[45]);
            $ma_counter[$computed_ma["Ichimoku"]] += 1;
        }
        // VWMA
        if ($indicators[47] !== null) {
            $computed_ma["VWMA"] = compute::Simple($indicators[47]);
            $ma_counter[$computed_ma["VWMA"]] += 1;
        }
        // HullMA (9)
        if ($indicators[49] !== null) {
            $computed_ma["HullMA"] = compute::Simple($indicators[49]);
            $ma_counter[$computed_ma["HullMA"]] += 1;
        }
        $result = [];

        $result['screener'] = $this->screener;
        $result['exchange'] = $this->exchange;

        $result['symbol'] = $symbol;
        $result['interval'] =  $this->interval;
        $result['time'] = date("Y-m-d H:i:s");

        foreach (range(0, count($indicators) - 1) as $x) {
            $result['indicators'][$this->indicators[$x]] = $indicators[$x];
        }

        // $this->indicators = (array) $this->indicators;

        $result['oscillators'] = [
            "RECOMMENDATION" => $recommend_oscillators,
            "BUY" => $oscillators_counter["BUY"],
            "SELL" => $oscillators_counter["SELL"],
            "NEUTRAL" => $oscillators_counter["NEUTRAL"],
            "COMPUTE" => $computed_oscillators
        ];

        $result['moving_averages'] = [
            "RECOMMENDATION" => $recommend_moving_averages,
            "BUY" => $ma_counter["BUY"],
            "SELL" => $ma_counter["SELL"],
            "NEUTRAL" => $ma_counter["NEUTRAL"],
            "COMPUTE" => $computed_ma
        ];

        $result['summary'] = [
            "RECOMMENDATION" => $recommend_summary,
            "BUY" => $oscillators_counter["BUY"] + $ma_counter["BUY"],
            "SELL" => $oscillators_counter["SELL"] + $ma_counter["SELL"],
            "NEUTRAL" => $oscillators_counter["NEUTRAL"] + $ma_counter["NEUTRAL"]
        ];

        return $result;
    }




    public function get_multiple_analysis($screener, $interval, $exchange, $symbols, $additional_indicators = [], $timeout = null, $proxies = null)
    {

        $rewrite_symbols = [];
        foreach ($symbols as $symbol) {
            $rewrite_symbols[] = $exchange . ':' . $symbol;
        }
        if (empty($screener) || !is_string($screener)) {
            throw new \Exception("Screener is empty or not valid.");
        }

        if (empty($symbols) || !is_array($symbols)) {
            throw new \Exception("Symbols is empty or not valid.");
        }

        foreach ($rewrite_symbols as $symbol) {
            if (count(explode(":", $symbol)) !== 2 || in_array("", explode(":", $symbol))) {
                throw new \Exception("One or more symbol is invalid. Symbol should be a list of exchange and ticker symbol separated by a colon. Example: ['NASDAQ:TSLA', 'NYSE:DOCN'] or ['BINANCE:BTCUSDT', 'BITSTAMP:ETHUSD'].");
            }
        }
        $trading_view = new TradingView;
        $indicators = $trading_view->getIndicators();
        $indicators_key = $indicators;
        $scan_url = $trading_view->getUrl() . strtolower($screener) . "/scan";
        $data = $trading_view->data($rewrite_symbols, $interval, $indicators);

        $curl = new Client();
        $curl = $curl->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($scan_url)
            ->addHeaders([
                'Content-Type' => 'application/json',
            ])
            ->setMethod('POST')
            ->setData($data)
            ->send();

        $res = json_decode($curl->getContent());
        $final = [];

        foreach ($res->data as $analysis) {
            // Convert list to dict
            $indicators = [];
            $result = $analysis->d;
            // foreach ($analysis->d as $x) {
            //     $indicators[$indicators[$x]] = $analysis->d[$x];
            // }

            $final[$analysis->s] = $this->calculate($result, $screener,  explode(":", $analysis->s)[1], explode(":", $analysis->s)[0], '1m');
            // print_r($final);
        }

        // foreach ($rewrite_symbols as $symbol) {

        //     // Add None if there is no analysis for symbol
        //     if (!array_key_exists(explode(':', $symbol)[1], $final)) {
        //         $final[strtoupper($symbol)] = null;
        //     }
        // }

        return $final;
    }
}
