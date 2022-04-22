<?php

namespace iAmirNet\Azbit;

class Client
{
    protected $base = "https://data.azbit.com/api/", $api_key, $api_secret;

    public function __construct($key = null, $secret = null)
    {
        if ($key) $this->api_key = $key;
        if ($secret) $this->api_secret = $secret;
    }

    public function currencies()
    {
        return $this->request("currencies");
    }

    public function currenciesPairs()
    {
        return $this->request("currencies/pairs");
    }

    public function currencyPair($symbol)
    {
        $currenciesPairs = $this->currenciesPairs();
        if ($currenciesPairs->status) {
            foreach ($currenciesPairs->data as $item) {
                if ($item['code'] == strtoupper($symbol))
                    return (object) ['status' => true, 'data' => (object) $item];
            }
        }
        return (object)['status' => false, "message" => "Not found."];
    }

    public function currenciesCommissions()
    {
        return $this->request("currencies/commissions");
    }

    public function currenciesUserCommissions()
    {
        return $this->signedRequest("currencies/user/commissions");
    }

    public function trades($symbol, $startTime = false, $endTime = false, $page = 1, $pageSize = 200)
    {
        $params = ["currencyPairCode" => strtoupper($symbol)];
        if ($startTime) $params['sinceDate'] = $startTime;
        if ($endTime) $params['endDate'] = $endTime;
        $params['pageNumber'] = $page;
        $params['pageSize'] = $pageSize;
        return $this->request("deals", $params);
    }

    public function myTrades($symbol, $startTime = false, $endTime = false, $page = 1, $pageSize = 200)
    {
        $params = ["currencyPairCode" => strtoupper($symbol)];
        if ($startTime) $params['sinceDate'] = $startTime;
        if ($endTime) $params['endDate'] = $endTime;
        $params['pageNumber'] = $page;
        $params['pageSize'] = $pageSize;
        return $this->signedRequest("user/deals", $params);
    }

    public function ticker($symbol)
    {
        return $this->request("tickers", ["currencyPairCode" => strtoupper($symbol)]);
    }

    public function kline($symbol,/* year, month, day, hour4, hour, minutes30, minutes15, minutes5, minutes3, minute */  $interval = "minute",/* Example: "2021-02-05T14:00:00" */ $startTime = false,/* Example: "2021-02-05T14:00:00" */ $endTime = false)
    {
        $params = ["currencyPairCode" => strtoupper($symbol)];
        $params['interval'] = $interval;
        $params['start'] = $startTime ? : date("Y-m-dTH:i:s");
        $params['end'] = $endTime ? : date("Y-m-dTH:i:s", time() + (60 * 60 * 1000));
        return $this->request("ohlc", $params);
    }

    public function orderbook($symbol)
    {
        return $this->request("orderbook", ["currencyPairCode" => strtoupper($symbol)]);
    }

    public function buy($symbol, $quantity, $price, $flags = [])
    {
        return $this->order("BUY", $symbol, $quantity, $price, $flags);
    }

    public function sell($symbol, $quantity, $price, $flags = [])
    {
        return $this->order("SELL", $symbol, $quantity, $price, $flags);
    }

    public function order($side, $symbol, $quantity, $price, $flags = [])
    {
        $opt = $this->orderData($side, $symbol, $quantity, $price, $flags);
        return $this->signedRequest("orders", $opt, "POST");
    }

    public function orderData($side, $symbol, $quantity, $price, $flags = [])
    {
        $side = strtolower($side);
        if (!in_array($side, ['buy', 'sell'])) die("Unsupport side parameters, please check!");
        $opt = [
            "side" => $side,
            "currencyPairCode" => strtoupper($symbol),
            "amount" => $quantity,
            "price" => $price,
        ];
        return $opt;
    }

    public function cancel($orderid)
    {
        return $this->signedRequest("orders/$orderid", [], "DELETE");
    }

    public function cancelBySymbol($symbol)
    {
        return $this->signedRequest("orders", ["currencyPairCode" => strtoupper($symbol)], "DELETE");
    }

    public function orderInfo($orderid)
    {
        return $this->signedRequest("orders/$orderid/deals");
    }

    public function orders($symbol,/* "all" / "active" / "canceled" */ $status = "all")
    {
        $params = ["currencyPairCode" => strtoupper($symbol)];
        $params['status'] = $status;
        return $this->signedRequest("user/orders", $params);
    }

    public function wallet()
    {
        return $this->signedRequest("wallets/history");
    }

    public function balances()
    {
        return $this->signedRequest("wallets/balances");
    }

    public function limits()
    {
        return $this->signedRequest("withdrawals/limits");
    }

    public function withdrawal(string $currencyCode,string $address,string $addressPublicKey, $amount)
    {
        $params = ["currencyCode" => strtoupper($currencyCode)];
        $params['address'] = $address;
        $params['addressPublicKey'] = $addressPublicKey;
        $params['amount'] = $amount;
        return $this->signedRequest("wallets/withdrawal", $params, "POST");
    }

    public function address($currencyCode)
    {
        return $this->signedRequest("deposit-address/$currencyCode");
    }

    private function request($url, $params = [], $method = "GET")
    {
        $headers = array('User-Agent: Mozilla/4.0 (compatible; PHP Azbit API - iamir.net)', 'Content-type: application/json', 'accept: application/json');
        $query = http_build_query($params, '', '&');
        if ($method == 'GET') {
            $endpoint = "{$this->base}{$url}?{$query}";
            $ret = $this->http_get($endpoint, $headers);
        } else if ($method == 'POST') {
            $endpoint = "{$this->base}{$url}";
            $ret = $this->http_post($endpoint, $params, []);
        } else {
            $endpoint = "{$this->base}{$url}?{$query}";
            $ret = $this->http_other($method, $endpoint, $headers);
        }
        return $ret;
    }

    private function signedRequest($url, $params = [], $method = "GET")
    {
        if (empty($this->api_key)) die("signedRequest error: API Key not set!");
        if (empty($this->api_secret)) die("signedRequest error: API Secret not set!");

        $query = http_build_query($params, '', '&');
        $msg = (string)($this->api_key . "{$this->base}{$url}" . json_encode($params, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        $signature = hash_hmac('sha256', $msg, $this->api_secret);
        $headers = array("User-Agent: Mozilla/4.0 (compatible; PHP Azbit API - iamir.net)",
            "Content-type: application/json",
            'accept: */*',
            "API-PublicKey: $this->api_key",
            "API-Signature: " .$signature);
        if ($method == 'GET') {
            // parameters encoded as query string in URL
            $endpoint = "{$this->base}{$url}?{$query}";
            $ret = $this->http_get($endpoint, $headers);
        } else if ($method == 'POST') {
            $endpoint = "{$this->base}{$url}";
            $ret = $this->http_post($endpoint, $params, $headers);
        } else {
            $endpoint = "{$this->base}{$url}?{$query}";
            $ret = $this->http_other($method, $endpoint, $headers);
        }
        return $ret;
    }

    private function http_post($url, $data, $headers = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $this->output($output, $url);
    }

    public static function json_decode($string) {
        if (!$string || is_array($string))
            return $string;
        $output = json_decode($string, true);
        return (json_last_error() == JSON_ERROR_NONE) ? $output : $string;
    }

    private function http_get($url, $headers = [], $data = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $this->output($output, $url);
    }

    private function http_other($method, $url, $headers = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $this->output($output, $url);
    }

    private function output($output, $url)
    {
        $output = static::json_decode($output);
        if (!$output) return (object)['status' => false, "message" => "NOK"];
        else return (object)['status' => true, 'code' => 200, 'data' => $output];
    }
}