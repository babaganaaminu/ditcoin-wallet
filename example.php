<?php
require "vendor/autoload.php";

use Ditcoin\Wallet;

$wallet = new Ditcoin\Wallet();
# or with rpc authentification needed
// $wallet = new Ditcoin\Wallet('127.0.0.1', 19092, 'ditrpcuser', 'rpcpasswd');

# used when rpc wallet is started with `--wallet-dir` option
// echo $wallet->create_wallet('dit_testy', 'testy');
// echo $wallet->open_wallet('dit_testy', 'testy');

$destination1 = (object) [
    'amount' => '1',
    'address' => '9RUGwFu3WGh3wAkeWWzMNiQXiW9ChYRpH974mDdrGcjpEcpPrz143oc9sV1W8YyAUwCztbfxt9usZSMVnSBwPxCaDXzhYWz'
];

$options = [
    'destinations' => $destination1
];

echo $wallet->transfer($options);

// echo $wallet->getAddress($options);
