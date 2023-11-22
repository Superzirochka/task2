<?php

namespace app\common\classes;

use app\common\classes\Recommendation;

class Compute

{
    public static function MA($ma, $close)
    {
        if ($ma < $close) {
            return Recommendation::BUY;
        } elseif ($ma > $close) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function RSI($rsi, $rsi1)
    {
        if ($rsi < 30 && $rsi1 < $rsi) {
            return Recommendation::BUY;
        } elseif ($rsi > 70 && $rsi1 > $rsi) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function Stoch($k, $d, $k1, $d1)
    {
        if ($k < 20 && $d < 20 && $k > $d && $k1 < $d1) {
            return Recommendation::BUY;
        } elseif ($k > 80 && $d > 80 && $k < $d && $k1 > $d1) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function CCI20($cci20, $cci201)
    {
        if ($cci20 < -100 && $cci20 > $cci201) {
            return Recommendation::BUY;
        } elseif ($cci20 > 100 && $cci20 < $cci201) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function ADX($adx, $adxpdi, $adxndi, $adxpdi1, $adxndi1)
    {
        if ($adx > 20 && $adxpdi1 < $adxndi1 && $adxpdi > $adxndi) {
            return Recommendation::BUY;
        } elseif ($adx > 20 && $adxpdi1 > $adxndi1 && $adxpdi < $adxndi) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function AO($ao, $ao1, $ao2)
    {
        if (($ao > 0 && $ao1 < 0) || ($ao > 0 && $ao1 > 0 && $ao > $ao1 && $ao2 > $ao1)) {
            return Recommendation::BUY;
        } elseif (($ao < 0 && $ao1 > 0) || ($ao < 0 && $ao1 < 0 && $ao < $ao1 && $ao2 < $ao1)) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function Mom($mom, $mom1)
    {
        if ($mom < $mom1) {
            return Recommendation::SELL;
        } elseif ($mom > $mom1) {
            return Recommendation::BUY;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function MACD($macd, $signal)
    {
        if ($macd > $signal) {
            return Recommendation::BUY;
        } elseif ($macd < $signal) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function BBBuy($close, $bblower)
    {
        if ($close < $bblower) {
            return Recommendation::BUY;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function BBSell($close, $bbupper)
    {
        if ($close > $bbupper) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function PSAR($psar, $open)
    {
        if ($psar < $open) {
            return Recommendation::BUY;
        } elseif ($psar > $open) {
            return Recommendation::SELL;
        } else {
            return Recommendation::NEUTRAL;
        }
    }

    public static function Recommend($value)
    {
        if ($value >= -1 && $value < -0.5) {
            return Recommendation::STRONG_SELL;
        } elseif ($value >= -0.5 && $value < -0.1) {
            return Recommendation::SELL;
        } elseif ($value >= -0.1 && $value <= 0.1) {
            return Recommendation::NEUTRAL;
        } elseif ($value > 0.1 && $value <= 0.5) {
            return Recommendation::BUY;
        } elseif ($value > 0.5 && $value <= 1) {
            return Recommendation::STRONG_BUY;
        } else {
            return Recommendation::ERROR;
        }
    }

    public static function Simple($value)
    {


        if ($value == -1) {
            return Recommendation::SELL;
        } elseif ($value == 1) {
            return Recommendation::BUY;
        } else {
            return Recommendation::NEUTRAL;
        }
    }
}
