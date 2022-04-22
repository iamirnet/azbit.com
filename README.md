[![Latest Version](https://img.shields.io/github/release/iamirnet/azbit.com.svg?style=flat-square)](https://github.com/iamirnet/azbit.com/releases)
[![GitHub last commit](https://img.shields.io/github/last-commit/iamirnet/azbit.com.svg?style=flat-square)](#)
[![Packagist Downloads](https://img.shields.io/packagist/dt/iamirnet/azbitcom.svg?style=flat-square)](https://packagist.org/packages/iamirnet/azbitcom)

# PHP Azbit API
This project is designed to help you make your own projects that interact with the [Azbit API](https://data.azbit.com/swagger/index.html).

#### Installation
```
composer require iamirnet/azbitcom
```
<details>
 <summary>Click for help with installation</summary>

## Install Composer
If the above step didn't work, install composer and try again.
#### Debian / Ubuntu
```
sudo apt-get install curl php-curl
curl -s http://getcomposer.org/installer | php
php composer.phar install
```
Composer not found? Use this command instead:
```
php composer.phar require "iamirnet/azbitcom"
```

#### Installing on Windows
Download and install composer:
1. https://getcomposer.org/download/
2. Create a folder on your drive like C:\iAmirNet\Azbit
3. Run command prompt and type `cd C:\iAmirNet\Azbit`
4. ```composer require iamirnet/azbitcom```
5. Once complete copy the vendor folder into your project.

</details>

#### Getting started
`composer require iamirnet/azbitcom`
```php
require 'vendor/autoload.php';
// config by specifying api key and secret
$api = new \iAmirNet\Azbit\Client("<api key>","<secret>");
```


=======
#### Get list of currency codes
```php
//Call this before running any functions
print_r($api->currencies());
```

=======
#### Get list of currency pair settings
```php
//Call this before running any functions
print_r($api->currenciesPairs());
```

=======
#### Get currency pair settings
```php
//Call this before running any functions
print_r($api->currenciesPair("BTC_USDT"));
```

=======
#### Get currency pair commissions
```php
//Call this before running any functions
print_r($api->currenciesCommissions());
```

=======
#### Get currency pair commissions and user commission info
```php
//Call this before running any functions
print_r($api->currenciesUserCommissions());
```

#### Get history of trades for CurrencyPair (100)
```php
//Call this before running any functions
$sinceDate = "2021-02-05T14:00:00";
$endDate = "2021-02-05T15:00:00";
$pageNumber = 1;
$pageSize = 20;
print_r($api->trades("BTC_USDT",/* optional */ $sinceDate,/* optional */  $endDate,/* optional */ $pageNumber,/* optional */  $pageSize));
```

#### Get history of trades for user per CurrencyPair (100)
```php
//Call this before running any functions
$sinceDate = "2021-02-05T14:00:00";
$endDate = "2021-02-05T15:00:00";
$pageNumber = 1;
$pageSize = 20;
print_r($api->myTrades("BTC_USDT",/* optional */ $sinceDate,/* optional */  $endDate,/* optional */ $pageNumber,/* optional */  $pageSize));
```

#### Get ticker info
```php
//Call this before running any functions
print_r($api->ticker("BTC_USDT"));
```

#### Get Kline/Candlestick Data
```php
//Call this before running any functions
print_r($api->kline("BTC_USDT",/* year, month, day, hour4, hour, minutes30, minutes15, minutes5, minutes3, minute */  "minute",/* Example: "2021-02-05T14:00:00" */ false,/* Example: "2021-02-05T14:00:00" */ false));
```

#### Get orderbook (40 bids + 40 asks)
```php
//Call this before running any functions
print_r($api->orderbook("BTC_USDT"));
```

#### Create Buy or Sell limit order
###### Buy
```php
//Call this before running any functions
$quantity = 1;
$price = 0.0005;
print_r($api->buy("BTC_USDT", $quantity, $price, "LIMIT"));
```

###### Sell
```php
//Call this before running any functions
$quantity = 1;
$price = 0.0006;
print_r($api->sell("BTC_USDT", $quantity, $price, "LIMIT"));
```

#### Delete selected order
```php
//Call this before running any functions
$orderId = "3fa85f64-5717-4562-b3fc-2c963f66afa6";
print_r($api->cancel($orderId));
```

#### Delete all user orders for currency pair
```php
//Call this before running any functions
print_r($api->cancel("BTC_USDT"));
```

#### Get user order info with deals
```php
//Call this before running any functions
$orderId = "3fa85f64-5717-4562-b3fc-2c963f66afa6";
print_r($api->orderInfo($orderId));
```

#### Get orders of user
```php
$orders = $api->orders("BTC_USDT",/* "all" / "active" / "canceled" */  "all");
print_r($orders);
```

#### Get deposits and withdrawals of user
```php
//Call this before running any functions
print_r($api->wallet());
```

#### Get user balances (available and blocked in orders)
```php
//Call this before running any functions
print_r($api->balances());
```

#### Get Limits
```php
//Call this before running any functions
print_r($api->limits());
```

#### Get information for deposit
```php
//Call this before running any functions
print_r($api->address("BTC_USDT"));
```

#### Withdraw money
```php
//Call this before running any functions
print_r($api->withdrawal("BTC_USDT", "address", "addressPublicKey",/* amount */ 0.05));
```

## Contribution
- Give us a star :star:
- Fork and Clone! Awesome
- Select existing [issues](https://github.com/iamirnet/azbit.com/issues) or create a [new issue](https://github.com/iamirnet/azbit.com/issues/new) and give us a PR with your bugfix or improvement after. We love it ❤️

## Donate
- USDT Or TRX: TUE8GiY4vmz831N65McwzZVbA9XEDaLinn 😘❤
