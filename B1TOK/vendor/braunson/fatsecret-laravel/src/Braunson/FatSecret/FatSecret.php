<?php
namespace Braunson\FatSecret;

use Illuminate\Support\Facades\Log;

class FatSecret
{
    static public $base = 'https://platform.fatsecret.com/rest/server.api?format=json&';

    /* Private Data */
    private $_consumerKey;
    private $_consumerSecret;

    /* Constructors */
    public function __construct($consumerKey, $consumerSecret)
    {
        $this->_consumerKey     = $consumerKey;
        $this->_consumerSecret  = $consumerSecret;
    }

    /* Properties */
    public function getKey()
    {
        return $this->_consumerKey;
    }

    public function setKey($consumerKey)
    {
        $this->_consumerKey = $consumerKey;
    }

    public function getSecret()
    {
        return $this->_consumerSecret;
    }

    public function setSecret($consumerSecret)
    {
        $this->_consumerSecret = $consumerSecret;
    }

    /* Public Methods */

    /**
     * Create a new profile with a user specified ID
     *
     * @param string $userID  Your ID for the newly created profile
     * @param string $token   The token for the newly created profile is returned here
     * @param string $secret  The secret for the newly created profile is returned here
     */
    public function profileCreate($userID, &$token, &$secret)
    {
        $url = static::$base . 'method=profile.create';

        if (!empty($userID)) {
            $url = $url . '&user_id=' . $userID;
        }

        $oauth = new OAuthBase();

        // Инициализируем переменные
        $normalizedUrl = '';
        $normalizedRequestParameters = '';

        $signature = $oauth->generateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);

        $response = $this->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

        // Парсим XML ответ
        $doc = simplexml_load_string($response);

        if ($doc === false) {
            throw new \Exception('Failed to parse XML response');
        }

        $this->errorCheck($doc);

        $token = (string)$doc->auth_token;
        $secret = (string)$doc->auth_secret;

        return true;
    }

    /**
     * Get the auth details of a profile
     *
     * @param string $userID  Your ID for the profile
     * @param string $token   The token for the profile is returned here
     * @param string $secret  The secret for the profile is returned here
     */
    public function profileGetAuth($userID, &$token, &$secret)
    {
        $url = static::$base . 'method=profile.get_auth&user_id=' . $userID;

        $oauth = new OAuthBase();

        // Инициализируем переменные
        $normalizedUrl = '';
        $normalizedRequestParameters = '';

        $signature = $oauth->generateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);

        $response = $this->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

        $doc = simplexml_load_string($response);

        if ($doc === false) {
            throw new \Exception('Failed to parse XML response');
        }

        $this->errorCheck($doc);

        $token = (string)$doc->auth_token;
        $secret = (string)$doc->auth_secret;

        return true;
    }

    /**
     * Create a new session for JavaScript API users
     *
     * @param array $auth                       Pass user_id or token and secret
     * @param int   $expires                    Minutes before session expires
     * @param int   $consumeWithin              Minutes to start using session
     * @param string $permittedReferrerRegex    Domain restriction
     * @param bool  $cookie                     Session key format
     * @param string $sessionKey                The session key is returned here
     */
    public function profileRequestScriptSessionKey($auth, $expires, $consumeWithin, $permittedReferrerRegex, $cookie, &$sessionKey)
    {
        $url = static::$base . 'method=profile.request_script_session_key';

        if (!empty($auth['user_id'])) {
            $url = $url . '&user_id=' . $auth['user_id'];
        }

        if ($expires > -1) {
            $url = $url . '&expires=' . $expires;
        }

        if ($consumeWithin > -1) {
            $url = $url . '&consume_within=' . $consumeWithin;
        }

        if (!empty($permittedReferrerRegex)) {
            $url = $url . '&permitted_referrer_regex=' . urlencode($permittedReferrerRegex);
        }

        if ($cookie === true) {
            $url = $url . "&cookie=true";
        }

        $oauth = new OAuthBase();

        // Инициализируем переменные
        $normalizedUrl = '';
        $normalizedRequestParameters = '';

        $token = isset($auth['token']) ? $auth['token'] : null;
        $secret = isset($auth['secret']) ? $auth['secret'] : null;

        $signature = $oauth->generateSignature($url, $this->_consumerKey, $this->_consumerSecret, $token, $secret, $normalizedUrl, $normalizedRequestParameters);

        $response = $this->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

        $doc = simplexml_load_string($response);

        if ($doc === false) {
            throw new \Exception('Failed to parse XML response');
        }

        $this->errorCheck($doc);

        $sessionKey = (string)$doc->session_key;

        return true;
    }

    /**
     * Search ingredients by phrase, page and max results
     *
     * @param string $searchPhrase The phrase you want to search for
     * @param int $page Page number (default 0)
     * @param int $maxResults Number of results (default 50)
     * @return array JSON decoded response
     */
    public function searchIngredients($searchPhrase, $page = 0, $maxResults = 50)
    {
        $url = static::$base . 'method=foods.search&page_number=' . $page . '&max_results=' . $maxResults . '&search_expression=' . urlencode($searchPhrase);

        $oauth = new OAuthBase();

        // Инициализируем переменные
        $normalizedUrl = '';
        $normalizedRequestParameters = '';

        $signature = $oauth->generateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);

        $response = $this->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

        // Декодируем JSON ответ
        $decodedResponse = json_decode($response, true);

        if ($decodedResponse === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to decode JSON response: ' . json_last_error_msg());
        }

        $this->errorCheck($decodedResponse);

        return $decodedResponse;
    }

    /**
     * Retrieve an ingredient by ID
     *
     * @param int $ingredientId The ingredient ID
     * @return array JSON decoded response
     */
    public function getIngredient($ingredientId)
    {
        $url = static::$base . 'method=food.get&food_id=' . $ingredientId;

        $oauth = new OAuthBase();

        // Инициализируем переменные
        $normalizedUrl = '';
        $normalizedRequestParameters = '';

        $signature = $oauth->generateSignature($url, $this->_consumerKey, $this->_consumerSecret, null, null, $normalizedUrl, $normalizedRequestParameters);

        $response = $this->getQueryResponse($normalizedUrl, $normalizedRequestParameters . '&' . OAuthBase::$OAUTH_SIGNATURE . '=' . urlencode($signature));

        // Декодируем JSON ответ
        $decodedResponse = json_decode($response, true);

        if ($decodedResponse === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to decode JSON response: ' . json_last_error_msg());
        }

        $this->errorCheck($decodedResponse);

        return $decodedResponse;
    }

    /* Private Methods */

    /**
     * Call the URL and return the response
     *
     * @param string $requestUrl The URL we want to call
     * @param string $postString The POST fields
     * @return string Raw response
     */
    private function getQueryResponse($requestUrl, $postString)
    {
        // Проверяем наличие расширения curl
        if (!extension_loaded('curl')) {
            throw new \Exception('cURL extension is required but not loaded');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMsg = curl_error($ch);
            curl_close($ch);
            throw new \Exception('cURL Error: ' . $errorMsg);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('HTTP Error: ' . $httpCode);
        }

        return $response;
    }

    /**
     * Checking for any errors in the response
     *
     * @param mixed $response SimpleXMLElement or array
     */
    private function errorCheck($response)
    {
        // Если это SimpleXMLElement
        if ($response instanceof \SimpleXMLElement) {
            if (isset($response->error)) {
                $errorMessage = (string)$response->error->message;
                $errorCode = (int)$response->error->code;
                Log::error('FatSecret API Error (XML): ' . $errorMessage);
                throw new \Exception('FatSecret API Error: ' . $errorMessage, $errorCode);
            }
        }
        // Если это массив (JSON ответ)
        elseif (is_array($response) && isset($response['error'])) {
            $errorMessage = $response['error']['message'] ?? 'Unknown error';
            $errorCode = $response['error']['code'] ?? 0;
            Log::error('FatSecret API Error (JSON): ' . $errorMessage);
            throw new \Exception('FatSecret API Error: ' . $errorMessage, $errorCode);
        }
    }
}
