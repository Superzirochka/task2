<?php

namespace app\common\socket;

use app\common\socket\websocketCore;
//include __DIR__ . "/../phpClient/websocketCore.php";

class websocketBinance extends websocketCore
{

    // public $Address;

    //private $socketMaster;

    function __construct($Address)
    {



        if (parent::__construct($Address) == false) {
            return;
        }

        $respo = $this->readSocket();
        echo var_dump(json_decode($respo));
    }
}

// $x = new websocketBinance(
//     $this->Address

//     //"wss://stream.binance.com:9443/ws/btcusdt@ticker"
// );
