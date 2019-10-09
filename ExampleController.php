<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Unirest\Request as UniRequest;
use Unirest\Request\Body as UniBody;

class ExampleController extends Controller
{
    private $apiKey;
    private $apiSecret;
    private $hostName;
    private $carbon;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiKey = 'b9f15229-04e8-4760-a66f-3f4f3ce80455';
        $this->apiSecret = '38b5ecda-8afa-4d4e-9aea-74a9cebea769';
        $this->hostName = 'https://sandbox.bca.co.id:443';
    }

    public function getTimestamp()
    {
        $date = Carbon::now('Asia/Jakarta');
        date_default_timezone_set('Asia/Jakarta');
        $fmt = $date->format('Y-m-d\TH:i:s');
        $ISO8601 = sprintf("$fmt.%s%s", substr(microtime(), 2, 3), date('P'));

        return $ISO8601;
    }

    public function getLowerCaseHexEncode($bodyToHash = [])
    {
        $hash = hash("sha256", "");

        return $hash;
    }

    public function getStringToSign(string $httpMethod, string $relativeUrl, string $accessToken, $lowerCaseStr, string $timestamp)
    {
        return $httpMethod . ":" . $relativeUrl . ":" . $accessToken . ":" . $lowerCaseStr . ":" . $timestamp;
    }

    public function getSignature(string $stringToSign)
    {
        $signature = hash_hmac('sha256', $stringToSign, $this->apiSecret);

        return $signature;
    }

    public function balanceInformation(Request $request)
    {
        try {
            $httpMethod = 'GET';
            $relativeUrl = '/banking/v3/corporates/BCAAPI2016/accounts/0201245680';
            $accessToken = $request->header()['accesstoken'][0];
            $contentType = $request->header()['content-type'][0];
            $timestamp = $this->getTimestamp();
            $stringToSign = $this->getStringToSign($httpMethod, $relativeUrl, $accessToken, $this->getLowerCaseHexEncode(), $timestamp);

            $headers = [
                'Authorization'     =>  'Bearer ' . $accessToken,
                'Content-Type'      =>  $contentType,
                'X-BCA-Key'         =>  $this->apiKey,
                'X-BCA-Timestamp'   =>  $timestamp,
                'X-BCA-Signature'   =>  $this->getSignature($stringToSign)
            ];

            $response = UniRequest::get($this->hostName . $relativeUrl, $headers);

            return response()->json($response->body);
        } catch (\Exception $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return $responseBodyAsString;
        }
    }

    public function rateForex(Request $request)
    {
        try {
            $httpMethod = 'GET';
            $relativeUrl = '/general/rate/forex';
            $accessToken = $request->header()['accesstoken'][0];
            $contentType = $request->header()['content-type'][0];
            $timestamp = $this->getTimestamp();
            $stringToSign = $this->getStringToSign($httpMethod, $relativeUrl, $accessToken, $this->getLowerCaseHexEncode(), $timestamp);

            $headers = [
                'Authorization'     =>  'Bearer ' . $accessToken,
                'Content-Type'      =>  $contentType,
                'X-BCA-Key'         =>  $this->apiKey,
                'X-BCA-Timestamp'   =>  $timestamp,
                'X-BCA-Signature'   =>  $this->getSignature($stringToSign)
            ];

            $response = UniRequest::get($this->hostName . $relativeUrl, $headers);

            return response()->json($response->body);
        } catch (\Exception $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return $responseBodyAsString;
        }
    }
}
