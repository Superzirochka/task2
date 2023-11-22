<?

namespace app\common\classes;

class TradingView
{
    public  $indicators = [
        "Recommend.Other", "Recommend.All", "Recommend.MA", "RSI", "RSI[1]", "Stoch.K", "Stoch.D", "Stoch.K[1]", "Stoch.D[1]", "CCI20", "CCI20[1]", "ADX", "ADX+DI", "ADX-DI", "ADX+DI[1]", "ADX-DI[1]", "AO", "AO[1]", "Mom", "Mom[1]", "MACD.macd", "MACD.signal", "Rec.Stoch.RSI", "Stoch.RSI.K", "Rec.WR", "W.R", "Rec.BBPower", "BBPower", "Rec.UO", "UO", "close", "EMA5", "SMA5", "EMA10", "SMA10", "EMA20", "SMA20", "EMA30", "SMA30", "EMA50", "SMA50", "EMA100", "SMA100", "EMA200", "SMA200", "Rec.Ichimoku", "Ichimoku.BLine", "Rec.VWMA", "VWMA", "Rec.HullMA9", "HullMA9", "Pivot.M.Classic.S3", "Pivot.M.Classic.S2", "Pivot.M.Classic.S1", "Pivot.M.Classic.Middle", "Pivot.M.Classic.R1",
        "Pivot.M.Classic.R2", "Pivot.M.Classic.R3", "Pivot.M.Fibonacci.S3", "Pivot.M.Fibonacci.S2", "Pivot.M.Fibonacci.S1", "Pivot.M.Fibonacci.Middle", "Pivot.M.Fibonacci.R1", "Pivot.M.Fibonacci.R2", "Pivot.M.Fibonacci.R3", "Pivot.M.Camarilla.S3", "Pivot.M.Camarilla.S2", "Pivot.M.Camarilla.S1", "Pivot.M.Camarilla.Middle", "Pivot.M.Camarilla.R1", "Pivot.M.Camarilla.R2", "Pivot.M.Camarilla.R3", "Pivot.M.Woodie.S3", "Pivot.M.Woodie.S2", "Pivot.M.Woodie.S1", "Pivot.M.Woodie.Middle", "Pivot.M.Woodie.R1", "Pivot.M.Woodie.R2", "Pivot.M.Woodie.R3", "Pivot.M.Demark.S1", "Pivot.M.Demark.Middle", "Pivot.M.Demark.R1", "open", "P.SAR", "BB.lower", "BB.upper", "AO[2]", "volume", "change", "low", "high"
    ];

    public  $scan_url = "https://scanner.tradingview.com/";

    public function getIndicators()
    {
        return $this->indicators;
    }

    public function getUrl()
    {
        return $this->scan_url;
    }

    public  function data($symbols, $interval, $indicators)
    {

        if ($interval == "1m") {
            $data_interval = "|1";
        } elseif ($interval == "5m") {
            $data_interval = "|5";
        } elseif ($interval == "15m") {
            $data_interval = "|15";
        } elseif ($interval == "30m") {
            $data_interval = "|30";
        } elseif ($interval == "1h") {
            $data_interval = "|60";
        } elseif ($interval == "2h") {
            $data_interval = "|120";
        } elseif ($interval == "4h") {
            $data_interval = "|240";
        } elseif ($interval == "1W") {
            $data_interval = "|1W";
        } elseif ($interval == "1M") {
            $data_interval = "|1M";
        } else {
            if ($interval != '1d') {
                trigger_error("Interval is empty or not valid, defaulting to 1 day.", E_USER_WARNING);
            }
            $data_interval = "";
        }

        $data_json = [
            "symbols" => [
                "tickers" => array_map("strtoupper", $symbols),
                "query" => [
                    "types" => []
                ]
            ],
            "columns" => array_map(function ($x) use ($data_interval) {
                return $x . $data_interval;
            }, $indicators)
        ];


        return ($data_json);
    }

    // public static function search($text, $type = null)
    // {
    //     $params = ["text" => $text];
    //     if ($type !== null) {
    //         $params["type"] = $type;
    //     }
    //     $url_symbol = "https://symbol-search.tradingview.com/symbol_search";
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url_symbol);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    //     $req = curl_exec($ch);

    //     // завершение сеанса и освобождение ресурсов
    //     curl_close($ch);
    //     //  $req = requests_post("https://symbol-search.tradingview.com/symbol_search", $params);
    //     $symbols = json_decode($req, true);
    //     $res = [];
    //     foreach ($symbols as $symbol) {
    //         $logo = null;
    //         if (isset($symbol["logoid"])) {
    //             $logo = "https://s3-symbol-logo.tradingview.com/{$symbol['logoid']}.svg";
    //         } elseif (isset($symbol["base-currency-logoid"])) {
    //             $logo = "https://s3-symbol-logo.tradingview.com/{$symbol['base-currency-logoid']}.svg";
    //         } elseif (isset($symbol["country"])) {
    //             $logo = "https://s3-symbol-logo.tradingview.com/country/{$symbol['country']}.svg";
    //         }
    //         $res[] = [
    //             "symbol" => $symbol["symbol"],
    //             "exchange" => $symbol["exchange"],
    //             "type" => $symbol["type"],
    //             "description" => $symbol["description"],
    //             "logo" => $logo
    //         ];
    //     }
    //     return $res;
    // }
}
