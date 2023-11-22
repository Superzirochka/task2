<?php

namespace app\common\classes;

use app\common\classes\Compute;
use app\common\classes\TradingView;
use yii\httpclient\Client;

class Binance
{
    public  $api_key = //'KgwTjFzBcZRIqHh5k64ZAuBQ29ZIVmiGAyBGuTZmwGx5KXrRtoh0cHq68Zk7CjgC';
    'RTUj6hBtIERq0LlLLt9gpxppgrN4CtEVfQ0HcfqqkmWiM2wM80kx4OnUG6b9nXXK';
    public $secret_key = //'PQimxIoWkxj83otJjtdFij8Smsy1kF2ubEY8bpuziC7yZIUiAdZ4fhL11QQZoKAy';
    '5nua2TFl7LsdzkDWWbLc1HGWCUb3jiZene4yS0M1DncpCgtphvfQRvOjGIuJkCDy';
    public $base_url = 'https://testnet.binance.vision/';
    public $info = [
        'timeOffset' => 0
    ];
    public $lastRequest = [];
    public $charts = [];
    //public $timestamp = round((microtime(true) * 1000));
    public $xMbxUsedWeight = 0;

    public function __construct()
    {
        $this->useServerTimestamp();
    }

    public function binance_build_query($params = [])
    {
        $new_arr = array();
        $query_add = '';
        foreach ($params as $label => $item) {
            if (gettype($item) == 'array') {
                foreach ($item as $arritem) {
                    $query_add = $label . '=' . $arritem . '&' . $query_add;
                }
            } else {
                $new_arr[$label] = $item;
            }
        }
        $query = http_build_query($new_arr, '', '&');
        $query = $query_add . $query;

        return $query;
    }

    public function httpRequest(string $url, string $method = "GET", array $params = [], bool $signed = false)
    {

        if (function_exists('curl_init') === false) {
            throw new \Exception("Sorry cURL is not installed!");
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        $query = $this->binance_build_query($params);

        // signed with params
        if ($signed === true) {
            if (empty($this->api_key)) {
                throw new \Exception("signedRequest error: API Key not set!");
            }

            if (empty($this->secret_key)) {
                throw new \Exception("signedRequest error: API Secret not set!");
            }

            $base = $this->base_url;

            $ts = (microtime(true) * 1000) + $this->info['timeOffset'];
            $params['timestamp'] =  number_format($ts, 0, '.', '');



            $query = $this->binance_build_query($params);
            $query = str_replace(['%40'], ['@'], $query); //if send data type "e-mail" then binance return: [Signature for this request is not valid.]
            $signature = hash_hmac('sha256', $query, $this->secret_key);
            if ($method === "POST") {
                $endpoint = $base . $url;
                $params['signature'] = $signature; // signature needs to be inside BODY
                $query = $this->binance_build_query($params); // rebuilding query
            } else {
                $endpoint = $base . $url . '?' . $query . '&signature=' . $signature;
            }

            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-MBX-APIKEY: ' . $this->api_key,
            ));
            //   print_r(curl_getinfo($curl));
        }

        // params so buildquery string and append to url
        elseif (count($params) > 0) {
            curl_setopt($curl, CURLOPT_URL, $this->base_url . $url . '?' . $query);
        }
        // no params so just the base url
        else {
            curl_setopt($curl, CURLOPT_URL, $this->base_url . $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-MBX-APIKEY: ' . $this->api_key,
            ));
        }
        curl_setopt($curl, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; PHP Binance API)");
        // Post and postfields
        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        }
        // Delete Method
        if ($method === "DELETE") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        // PUT Method
        if ($method === "PUT") {
            curl_setopt($curl, CURLOPT_PUT, true);
        }

        // proxy settings

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        // set user defined curl opts last for overriding

        //print_r(curl_getinfo($curl));
        $output = curl_exec($curl);
        // print_r($output);
        // echo '197 <br>';
        // exit;
        // Check if any error occurred
        if (curl_errno($curl) > 0) {
            // should always output error, not only on httpdebug
            // not outputing errors, hides it from users and ends up with tickets on github
            throw new \Exception('Curl error: ' . curl_error($curl));
        }

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = $this->get_headers_from_curl_response($output);
        $output = substr($output, $header_size);

        curl_close($curl);

        $json = json_decode($output, true);

        $lastRequest = [
            'url' => $url,
            'method' => $method,
            'params' => $params,
            'header' => $header,
            'json' => $json
        ];


        if (isset($json['msg']) && !empty($json['msg'])) {
            if ($url != 'v1/system/status' && $url != 'v3/systemStatus.html' && $url != 'v3/accountStatus.html') {
                // should always output error, not only on httpdebug
                // not outputing errors, hides it from users and ends up with tickets on github
                throw new \Exception('signedRequest error: ' . print_r($output, true));
            }
        }
        // $this->transfered += strlen($output);
        // $this->requestCount++;
        return $output;
    }

    public function get_headers_from_curl_response(string $header)
    {
        $headers = array();
        $header_text = substr($header, 0, strpos($header, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
            if ($i === 0)
                $headers['http_code'] = $line;
            else {
                list($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }

        return $headers;
    }

    public function useServerTimestamp()
    {

        $json = $this->httpRequest("api/v1/time", 'GET');
        $request = json_decode($json, true);
        // print_r($request);
        // exit;
        $this->info['timeOffset'] = $request['serverTime'] - (microtime(true) * 1000);
    }

    public function account()
    {
        return $this->httpRequest("api/v3/account", "GET", [], true);
    }

    public function accountTrades($symbol)
    {
        $params = [
            'symbol' => $symbol,

        ];
        return $this->httpRequest("api/v3/myTrades", "GET", $params, true);
    }

    // Функция для размещения ордера

    public function new_order($side, string $type = "LIMIT", $symbol, $quantity, $price, array $flags = [])
    {
        $ts = round((microtime(true) * 1000) + $this->info['timeOffset']);

        if (gettype($price) !== "string") {
            // for every other type, lets format it appropriately
            $price = number_format($price, 8, '.', '');
        }

        if (is_numeric($quantity) === false) {
            // WPCS: XSS OK.
            throw new \Exception("warning: quantity expected numeric got " . gettype($quantity) . PHP_EOL);
        }

        if (is_string($price) === false) {
            // WPCS: XSS OK.
            throw new \Exception("warning: price expected string got " . gettype($price) . PHP_EOL);
        }
        $params = array(
            'symbol' => $symbol,
            'side' => $side,
            'type' => $type,
            // 'quantity' => $quantity,
            'recvWindow' => 5000,
            'timestamp' => $ts
        );

        switch ($type) {
            case 'LIMIT':
                $params['quantity'] = $quantity;
                $params["price"] = $price;
                $params["timeInForce"] = "GTC";
                if (isset($flags['icebergQty']) && $flags['icebergQty'] !== 0) {
                    unset($params['icebergQty']);
                    $params['icebergQty'] = $flags['icebergQty'];
                }
                break;
            case 'MARKET':
                $params['quantity'] = $quantity;
                if (isset($flags['isQuoteOrder']) && $flags['isQuoteOrder'] != 0) {
                    unset($params['quantity']);
                    $params['quoteOrderQty'] = $quantity;
                }
                break;
            case 'STOP_LOSS':
                $params['quantity'] = $quantity;
                if (isset($flags['stopPrice'])) {
                    $params['stopPrice'] = $flags['stopPrice'];
                } else {
                    $params['trailingDelta'] = $flags['trailingDelta'];
                }
                break;
            case 'STOP_LOSS_LIMIT':
                $params["price"] = $price;
                $params['quantity'] = $quantity;
                $params["timeInForce"] = "GTC";
                if (isset($flags['stopPrice'])) {
                    $params['stopPrice'] = $flags['stopPrice'];
                } else {
                    $params['trailingDelta'] = $flags['trailingDelta'];
                }
                break;
            case 'TAKE_PROFIT':
                $params['quantity'] = $quantity;
                if (isset($flags['stopPrice'])) {
                    $params['stopPrice'] = $flags['stopPrice'];
                } else {
                    $params['trailingDelta'] = $flags['trailingDelta'];
                }
                break;
            case 'TAKE_PROFIT_LIMIT':
                $params["price"] = $price;
                $params["timeInForce"] = "GTC";
                $params['quantity'] = $quantity;

                if (isset($flags['stopPrice'])) {
                    $params['stopPrice'] = $flags['stopPrice'];
                } else {
                    $params['trailingDelta'] = $flags['trailingDelta'];
                }
                break;

            case 'LIMIT_MAKER':
                $params["price"] = $price;
                $params['quantity'] = $quantity;
                if (isset($flags['icebergQty']) && $flags['icebergQty'] !== 0) {
                    unset($params['icebergQty']);
                    $params['icebergQty'] = $flags['icebergQty'];
                    $params["timeInForce"] = "GTC";
                }
                break;
        }

        // if ($type === "LIMIT" || $type === "STOP_LOSS_LIMIT" || $type === "TAKE_PROFIT_LIMIT") {
        //     $params["price"] = $price;
        //     $params["timeInForce"] = "GTC";
        // }

        // if ($type === "MARKET" && isset($flags['isQuoteOrder']) && $flags['isQuoteOrder']) {
        //     unset($params['quantity']);
        //     $params['quoteOrderQty'] = $quantity;
        // }

        // if (isset($flags['stopPrice'])) {
        //     $params['stopPrice'] = $flags['stopPrice'];
        // }
        // if (isset($flags['trailingDelta'])) {
        //     $params['trailingDelta'] = $flags['trailingDelta'];
        // }

        // if (isset($flags['icebergQty'])) {
        //     $params['icebergQty'] = $flags['icebergQty'];
        // }

        // if (isset($flags['newOrderRespType'])) {
        //     $params['newOrderRespType'] = $flags['newOrderRespType'];
        // }

        // if (isset($flags['newClientOrderId'])) {
        //     $params['newClientOrderId'] = $flags['newClientOrderId'];
        // }

        $query = $this->binance_build_query($params);
        $signature = hash_hmac('sha256', $query, $this->secret_key);
        $scan_url = $this->base_url . 'api/v3/order?' .
            $query
            . '&signature=' . $signature;
        $curl = new Client();
        $curl = $curl->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($scan_url)
            ->addHeaders([
                'Content-Type' => 'application/json',
                'X-MBX-APIKEY' => $this->api_key
            ])
            ->setMethod('POST')
            ->send();

        $res = json_decode($curl->getContent());

        return ($curl->getContent());

        //   return httpRequest('api/v3/order',  'POST', $params);
    }

    //удаляет конкретный ордер, обязательные параметры $data['orderId'] или  $data['origClientOrderId']
    public function deleteOrder($symbol, $data = [])
    {
        $ts = round((microtime(true) * 1000) + $this->info['timeOffset']);
        $params = array(
            'symbol' => $symbol,
            'recvWindow' => 5000,
            'timestamp' => $ts
        );
        if (!empty($data['orderId'])) {
            $params['orderId'] = $data['orderId'];
        }
        if (!empty($data['origClientOrderId'])) {
            $params['origClientOrderId'] = $data['origClientOrderId'];
        }
        if (!empty($data['cancelRestrictions'])) {
            $params['cancelRestrictions'] = $data['cancelRestrictions'];
        }
        $query = $this->binance_build_query($params);
        $signature = hash_hmac('sha256', $query, $this->secret_key);
        $scan_url = $this->base_url . 'api/v3/order?' .
            $query
            . '&signature=' . $signature;
        $curl = new Client();
        $curl = $curl->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($scan_url)
            ->addHeaders([
                'Content-Type' => 'application/json',
                'X-MBX-APIKEY' => $this->api_key
            ])
            ->setMethod('DELETE')
            ->send();

        $res = json_decode($curl->getContent());

        return ($curl->getContent());
    }

    public function deleteOpenOrder($symbol)
    {
        $ts = round((microtime(true) * 1000) + $this->info['timeOffset']);
        $params = array(
            'symbol' => $symbol,
            'recvWindow' => 5000,
            'timestamp' => $ts
        );

        $query = $this->binance_build_query($params);
        $signature = hash_hmac('sha256', $query, $this->secret_key);
        $scan_url = $this->base_url . '/api/v3/openOrders?' .
            $query
            . '&signature=' . $signature;
        $curl = new Client();
        $curl = $curl->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($scan_url)
            ->addHeaders([
                'Content-Type' => 'application/json',
                'X-MBX-APIKEY' => $this->api_key
            ])
            ->setMethod('DELETE')
            ->send();

        $res = json_decode($curl->getContent());

        return ($curl->getContent());
    }


    // Функция для размещения ордера на покупку

    public function place_buy_order($symbol, $quantity, $price)
    {

        $params = array(
            'symbol' => $symbol,
            'side' => 'BUY',
            'type' => 'LIMIT',
            'timeInForce' => 'GTC',
            'quantity' => $quantity,
            'price' => $price,
        );
        return $this->order("BUY", $symbol, $quantity, $price, 'LIMIT', [], true);
        //   return httpRequest('api/v3/order',  'POST', $params);
    }



    // Функция для размещения ордера на продажу
    public function place_sell_order($symbol, $quantity, $price)
    {
        $params = array(
            'symbol' => $symbol,
            'side' => 'SELL',
            'type' => 'LIMIT',
            'timeInForce' => 'GTC',
            'quantity' => $quantity,
            'price' => $price,
        );

        return $this->order("SELL", $symbol, $quantity, $price, 'LIMIT', [], true);
    }

    // Функция для получения текущей цены Bitcoin (BTC)
    public function get_btc_price($trading_pair)
    {
        $ticker = $this->httpRequest('api/v3/ticker/price', 'GET', array('symbol' => $trading_pair));
        return $ticker;
    }

    public function getPriceFilter($symbol)
    {

        return $this->httpRequest('api/v3/exchangeInfo', 'GET', array('symbol' => $symbol));
    }

    public function order(string $side, string $symbol, $quantity, $price, string $type = "LIMIT", array $flags = [], bool $test = true)
    {
        $opt = [
            "symbol" => $symbol,
            "side" => $side,
            "type" => $type,
            "quantity" => $quantity,
            "recvWindow" => 60000,
        ];

        // someone has preformated there 8 decimal point double already
        // dont do anything, leave them do whatever they want
        if (gettype($price) !== "string") {
            // for every other type, lets format it appropriately
            $price = number_format($price, 8, '.', '');
        }

        if (is_numeric($quantity) === false) {
            // WPCS: XSS OK.
            echo "warning: quantity expected numeric got " . gettype($quantity) . PHP_EOL;
        }

        if (is_string($price) === false) {
            // WPCS: XSS OK.
            echo "warning: price expected string got " . gettype($price) . PHP_EOL;
        }

        if ($type === "LIMIT" || $type === "STOP_LOSS_LIMIT" || $type === "TAKE_PROFIT_LIMIT") {
            $opt["price"] = $price;
            $opt["timeInForce"] = "GTC";
        }

        if ($type === "MARKET" && isset($flags['isQuoteOrder']) && $flags['isQuoteOrder']) {
            unset($opt['quantity']);
            $opt['quoteOrderQty'] = $quantity;
        }

        if (isset($flags['stopPrice'])) {
            $opt['stopPrice'] = $flags['stopPrice'];
        }

        if (isset($flags['icebergQty'])) {
            $opt['icebergQty'] = $flags['icebergQty'];
        }

        if (isset($flags['newOrderRespType'])) {
            $opt['newOrderRespType'] = $flags['newOrderRespType'];
        }

        if (isset($flags['newClientOrderId'])) {
            $opt['newClientOrderId'] = $flags['newClientOrderId'];
        }

        $qstring = ($test === false) ? "api/v3/order" : "api/v3/order/test";

        return $this->httpRequest($qstring, "POST", $opt, true);
    }

    public function openOrders(string $symbol = null)
    {
        // $this->useServerTimestamp();
        $params = [];
        if (is_null($symbol) != true) {
            $params = [
                "symbol" => $symbol,
            ];
        }
        $ts = round(
            (microtime(true) * 1000)
                + $this->info['timeOffset']
        );
        $params = [
            'recvWindow' => 5000,
            'timestamp' => $ts
        ];
        return $this->httpRequest("api/v3/openOrders", "GET", $params, true);
    }
    public function allOrders($symbol)
    {
        //$symbol = "BTCUSDT";
        $params = [];
        // if (is_null($symbol) != true) {
        $params = [
            "symbol" => $symbol,
        ];
        // }
        // $ts = round(
        //     (microtime(true) * 1000)
        //         + $this->info['timeOffset']
        // );
        // $params = [
        //     //  'recvWindow' => 5000,
        //     'timestamp' => $ts
        // ];
        return $this->httpRequest("api/v3/allOrders", "GET", $params, true);
        $query = $this->binance_build_query($params);
        $signature = hash_hmac('sha256', $query, $this->secret_key);
        $scan_url = $this->base_url . 'api/v3/allOrders' //;
            . '?' .
            $query
            . '&signature=' . $signature;
        $params['signature'] = $signature;
        $curl = new Client();
        $curl = $curl->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($scan_url)
            ->addHeaders([
                'Content-Type' => 'application/json',
                'X-MBX-APIKEY' => $this->api_key
            ])
            ->setMethod('GET')
            //    ->setData($params)
            ->send();


        //      print_r($scan_url);
        $res = json_decode($curl->getContent());

        return ($curl->getContent());
        //   return $this->httpRequest("api/v3/allOrders", "GET", $params, true);
    }
}
