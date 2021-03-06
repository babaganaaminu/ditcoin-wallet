# Ditcoin-Wallet-PHP

A PHP library for the `ditcoin-wallet-rpc` interface. 

For more information about Ditcoin, please visit https://ditcoin.io.

If you found this useful, feel free to donate!

DIT: `9RUGwFu3WGh3wAkeWWzMNiQXiW9ChYRpH974mDdrGcjpEcpPrz143oc9sV1W8YyAUwCztbfxt9usZSMVnSBwPxCaDXzhYWz`

## Installation

Install the library using Composer.
    
    composer require ditcoin/ditcoin-wallet

## Run an Instance of the RPC Wallet

For internal communication through library and RPC wallet, following options are optional:

```bash
--rpc-bind-port
--rpc-bind-ip
--daemon-host
--confirm-external-bind
```

**Note**: more informations can be found using `--help` option.

### Without authentification

```bash
ditcoin-wallet-rpc --password "$wallet_password" --wallet-file $wallet_filepath --rpc-bind-port 19092 --rpc-bind-ip $external_ip --daemon-host $external_ip --confirm-external-bind --disable-rpc-login
```

### With authentification

```bash
ditcoin-wallet-rpc --password "$wallet_password" --wallet-file $wallet_filepath --rpc-bind-port 19092 --rpc-bind-ip $external_ip --daemon-host $external_ip --confirm-external-bind --rpc-login 'ditrpc_user:rpc_password'
```

### Multi-wallets usage

```bash
ditcoin-wallet-rpc --password "$wallet_password" --wallet-file $wallet_filepath --rpc-bind-port 19092 --rpc-bind-ip $external_ip --daemon-host $external_ip --confirm-external-bind --rpc-login 'ditrpc_user:rpc_password' --wallet-dir $wallet_dirpath
```

## Create an Instance of the Wallet

```php
require 'vendor/autoload.php';
use Ditcoin\Wallet;

$wallet = new Ditcoin\Wallet();
```

Default hostname and port connects to http://127.0.0.1:19092.

To connect to an external IP or different port:

```php
$hostname = YOUR_WALLET_RPC_IP;
$port = YOUR_WALLET_RPC_PORT;

$wallet = new Ditcoin\Wallet($hostname, $port);

# or with rpc authentification needed
$username = YOUR_WALLET_RPC_USERNAME;
$password = YOUR_WALLET_RPC_PASSWORD;

$wallet = new Ditcoin\Wallet($hostname, $port, $username, $password);

```

## Wallet Methods

### createWallet
Usage:

```php
# used when rpc wallet is started with `--wallet-dir` option

$wallet->createWallet('dit_wallet', 'ditcoin', 'English');
```

Creates a new wallet.
    
Parameters:

* `filename` - filename of wallet to create (*string*)
* `password` - wallet password (*string*)
* `language` - language to use for mnemonic phrase (*string*)

Example response: 

```
{}
```

Returns an object with `error` field if unsuccessful.

### openWalllet
Usage:

```php
# used when rpc wallet is started with `--wallet-dir` option

$wallet->openWallet('dit_wallet', 'ditcoin');
```

Opens a wallet.
    
Parameters:

* `filename` - filename of wallet to open (*string*)
* `password` -wallet password (*string*)

Example response: 

```
{}
```

Returns an object with `error` field if unsuccessful.

### getBalance

```php
$balance = $wallet->getBalance();
```

Responds with the current balance and unlocked (spendable) balance of the wallet in atomic units. Divide by 1e8 to convert.
    
Example response: 

```
{ balance: 361198014257, unlocked_balance: 361198014257 }
```

### getAddress

```php
$address = $wallet->getAddress();
```

Responds with the Ditcoin address of the wallet.

Example response:

```
{ address: '9RUGwFu3WGh3wAkeWWzMNiQXiW9ChYRpH974mDdrGcjpEcpPrz143oc9sV1W8YyAUwCztbfxt9usZSMVnSBwPxCaDXzhYWz' }
```

### transfer

```php
$tx_hash = $wallet->transfer($options);
```

Transfers Ditcoin to a single recipient OR a group of recipients in a single transaction. Responds with the transaction hash of the payment.

Parameters:

* `options` - an array containing: 

```php
[
    'destinations' => (object OR array of objects)
    'mixin' => (*int*), // amount of existing transaction outputs to mix yours with (default is 4)
    'unlockTime' => (*int*), // number of blocks before tx is spendable (default is 0)
    'pid' => (*string*) // optional payment ID (a 64 character hexadecimal string used for identifying the sender of a payment)
    'payment_id' => (*string*) // optional payment ID (a 64 character hexadecimal string used for identifying the sender of a payment)
    'do_not_relay' => (*boolean*) // optional boolean used to indicate whether a transaction should be relayed or not
    'priority' => (*int*) // optional transaction priority
    'get_tx_hex' => (*boolean*) // optional boolean used to indicate that the transaction should be returned as hex string after sending
    'get_tx_key' => (*boolean*) // optional boolean used to indicate that the transaction key should be returned after sending
]
```

```php
$options = [
    'destinations' => (object) [
        'amount' => '1',
        'address' => '9RUGwFu3WGh3wAkeWWzMNiQXiW9ChYRpH974mDdrGcjpEcpPrz143oc9sV1W8YyAUwCztbfxt9usZSMVnSBwPxCaDXzhYWz'
    ]
];
```

Example response:

```
{ tx_hash: '<b9272a68b0f242769baa1ac2f723b826a7efdc5ba0c71a2feff4f292967936d8>', tx_key: '' }
```

### transferSplit

```php
$tx_hash = $wallet->transferSplit($options);
```

Same as `transfer()`, but can split into more than one transaction if necessary. Responds with a list of transaction hashes.

Additional property available for the `options` array:

* `new_algorithm` - `true` to use the new transaction construction algorithm. defaults to `false`. (*boolean*)

Example response:

```
{ tx_hash_list: [ '<f17fb226ebfdf784a0f5814e1c5bb78c19ea26930a0d706c9dc1085a250ceb37>' ] }
```

### sweepDust

```php
$tx_hashes = $wallet->sweepDust();
```

Sends all dust outputs back to the wallet, to make funds easier to spend and mix. Responds with a list of the corresponding transaction hashes.

Example response:

```
{ tx_hash_list: [ '<75c666fc96120a643321a5e76c0376b40761582ee40cc4917e8d1379a2c8ad9f>' ] }
```

### sweepAll
Usage:

```
$tx_hashes = $wallet->sweepAll('7Dty8AeoNi3CgvYRH7rpEoQVRrkCETSdrKAdPE3kVTyYjmh21iiw48z5HEj5nGub1y9pVLLx8gZmwGNKRuLLtaMSLe9QdWx');
```

Sends all spendable outputs to the specified address. Responds with a list of the corresponding transaction hashes.

Example response:

```
{ tx_hash_list: [ '<75c666fc96120a643321a5e76c0376b40761582ee40cc4917e8d1379a2c8ad9f>' ] }
```

### getPayments

```php
$payments = $wallet->getPayments($payment_id);
```

Returns a list of incoming payments using a given payment ID.

Parameters:

* `paymentID` - the payment ID to scan wallet for included transactions (*string*)

### getBulkPayments

```php
$payments = $wallet->getBulkPayments($payment_id, $height);
```

Returns a list of incoming payments using a single payment ID or a list of payment IDs from a given height.

Parameters:

* `paymentIDs` - the payment ID or list of IDs to scan wallet for (*array*)
* `minHeight` - the minimum block height to begin scanning from (example: 800000) (*int*)

### incomingTransfers

```php
$transfers = $wallet->incomingTransfers($type);
```

Returns a list of incoming transfers to the wallet.

Parameters:

* `type` - accepts `"all"`: all the transfers, `"available"`: only transfers that are not yet spent, or `"unavailable"`: only transfers which have been spent (*string*)

### queryKey

```php
$key = $wallet->queryKey($type);
```

Returns the wallet's spend key (mnemonic seed) or view private key.

Parameters:

* `type` - accepts `"mnemonic"`: the mnemonic seed for restoring the wallet, or `"view_key"`: the wallet's view key (*string*)

### integratedAddress

```php
$integratedAddress = $wallet->integratedAddress($payment_id);
```

Make and return a new integrated address from your wallet address and a given payment ID, or generate a random payment ID if none is given.

Parameters:

* `payment_id` - a 64 character hexadecimal string. If not provided, a random payment ID is automatically generated. (*string*, optional)

Example response:

```
{ integrated_address: '4HCSju123guax69cVdqVP5APVLkcxxjjXdcP9fJWZdNc5mEpn3fXQY1CFmJDvyUXzj2Fy9XafvUgMbW91ZoqwqmQ96NYBVqEd6JAu9j3gk' }
```

### splitIntegrated

```php
$splitIntegrated = $wallet->splitIntegrated($integrated_address);
```

Returns the standard address and payment ID corresponding for a given integrated address.

Parameters:

* `integrated_address` - an Ditcoin integrated address (*string*)

Example response:

```
{ payment_id: '<61eec5ffd3b9cb57>',
  standard_address: '9RUGwFu3WGh3wAkeWWzMNiQXiW9ChYRpH974mDdrGcjpEcpPrz143oc9sV1W8YyAUwCztbfxt9usZSMVnSBwPxCaDXzhYWz' }
```

### getHeight 
Usage:

```
$height = $wallet->getHeight();
```

Returns the current block height of the daemon.

Example response:

```
{ height: 874458 }
```

### stopWallet

```php
$wallet->stopWallet();
```

Cleanly shuts down the current `ditcoin-wallet-rpc` process.