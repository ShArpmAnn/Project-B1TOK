<?php
namespace Braunson\FatSecret;

class OAuthBase
{
    /* OAuth Parameters */

    static public $OAUTH_VERSION_NUMBER = '1.0';
    static public $OAUTH_PARAMETER_PREFIX = 'oauth_';
    static public $XOAUTH_PARAMETER_PREFIX = 'xoauth_';
    static public $PEN_SOCIAL_PARAMETER_PREFIX = 'opensocial_';

    static public $OAUTH_CONSUMER_KEY = 'oauth_consumer_key';
    static public $OAUTH_CALLBACK = 'oauth_callback';
    static public $OAUTH_VERSION = 'oauth_version';
    static public $OAUTH_SIGNATURE_METHOD = 'oauth_signature_method';
    static public $OAUTH_SIGNATURE = 'oauth_signature';
    static public $OAUTH_TIMESTAMP = 'oauth_timestamp';
    static public $OAUTH_NONCE = 'oauth_nonce';
    static public $OAUTH_TOKEN = 'oauth_token';
    static public $OAUTH_TOKEN_SECRET = 'oauth_token_secret';

    protected $unreservedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.~';

    public function generateSignature($url, $consumerKey, $consumerSecret, $token, $tokenSecret, &$normalizedUrl, &$normalizedRequestParameters)
    {
        $signatureBase = $this->generateSignatureBase($url, $consumerKey, $token, 'POST', $this->generateTimeStamp(), $this->generateNonce(), 'HMAC-SHA1', $normalizedUrl, $normalizedRequestParameters);
        $secretKey = $this->urlEncode($consumerSecret) . '&' . $this->urlEncode($tokenSecret);

        return base64_encode(hash_hmac('sha1', $signatureBase, $secretKey, true));
    }

    private function generateSignatureBase($url, $consumerKey, $token, $httpMethod, $timeStamp, $nonce, $signatureType, &$normalizedUrl, &$normalizedRequestParameters)
    {
        $parameters = array();

        $elements = explode('?', $url);
        if (count($elements) > 1) {
            $parameters = $this->getQueryParameters($elements[1]);
        }

        $parameters[self::$OAUTH_VERSION] = self::$OAUTH_VERSION_NUMBER;
        $parameters[self::$OAUTH_NONCE] = $nonce;
        $parameters[self::$OAUTH_TIMESTAMP] = $timeStamp;
        $parameters[self::$OAUTH_SIGNATURE_METHOD] = $signatureType;
        $parameters[self::$OAUTH_CONSUMER_KEY] = $consumerKey;

        if (!empty($token)) {
            $parameters[self::$OAUTH_TOKEN] = $token;
        }

        $normalizedUrl = $elements[0];
        $normalizedRequestParameters = $this->normalizeRequestParameters($parameters);

        return $httpMethod . '&' . $this->urlEncode($normalizedUrl) . '&' . $this->urlEncode($normalizedRequestParameters);
    }

    private function getQueryParameters($paramString)
    {
        $elements = explode('&', $paramString);
        $result   = array();

        foreach ($elements as $element) {
            if (strpos($element, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $element, 2);
            if ($value) {
                $value = urldecode($value);
            }

            if (!empty($result[$key])) {
                if (!is_array($result[$key])) {
                    $result[$key] = array($result[$key], $value);
                } else {
                    array_push($result[$key], $value);
                }
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function normalizeRequestParameters($parameters)
    {
        $elements = array();
        ksort($parameters);

        foreach ($parameters as $paramName => $paramValue) {
            array_push($elements, $this->urlEncode($paramName) . '=' . $this->urlEncode($paramValue));
        }

        return join('&', $elements);
    }

    private function urlEncode($string)
    {
        if ($string === null) {
            return '';
        }

        $string = urlencode($string);
        $string = str_replace('+', '%20', $string);
        $string = str_replace('!', '%21', $string);
        $string = str_replace('*', '%2A', $string);
        $string = str_replace('\'', '%27', $string);
        $string = str_replace('(', '%28', $string);
        $string = str_replace(')', '%29', $string);

        return $string;
    }

    private function generateTimeStamp()
    {
        return time();
    }

    private function generateNonce()
    {
        return md5(uniqid(rand(), true));
    }
}
