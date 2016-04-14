<?php
namespace Ehesp\SteamLogin;

use Exception;

class SteamLogin implements SteamLoginInterface
{
    /**
     * Steam Community OpenID URL
     *
     * @var string
     */
    private static $openId = 'https://steamcommunity.com/openid/login';

    /**
     * Validates a given URL, ensuring it contains the http or https URI Scheme
     *
     * @param string $url
     * @return bool
     */
    private function validateUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }

    /**
     * Build the Steam login URL
     *
     * @param string $return A custom return to URL
     * @return string
     */
    public function url($return = null, $altRealm = null)
    {
        $useHttps = !empty($_SERVER['HTTPS']) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
        if (!is_null($return)) {
            if (!$this->validateUrl($return)) {
                throw new Exception('The return URL must be a valid URL with a URI Scheme or http or https.');
            }
        }
        else {
            if($altRealm == null)
                $return = ($useHttps ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
            else
                $return = $altRealm . $_SERVER['SCRIPT_NAME'];
        }

        $params = array(
            'openid.ns'         => 'http://specs.openid.net/auth/2.0',
            'openid.mode'       => 'checkid_setup',
            'openid.return_to'  => $return,
            'openid.realm'      => $altRealm != null ? $altRealm : (($useHttps ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']),
            'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        );

        return self::$openId . '?' . http_build_query($params);
    }

    /**
     * Validates a Steam login request and returns the users Steam Community ID
     *
     * @return string
     */
    public function validate($timeout = 30)
    {
        $response = null;

        try {
            $params = array(
                'openid.assoc_handle' => $_GET['openid_assoc_handle'],
                'openid.signed'       => $_GET['openid_signed'],
                'openid.sig'          => $_GET['openid_sig'],
                'openid.ns'           => 'http://specs.openid.net/auth/2.0',
            );

            $signed = explode(',', $_GET['openid_signed']);

            foreach ($signed as $item) {
                $val = $_GET['openid_' . str_replace('.', '_', $item)];
                $params['openid.' . $item] = get_magic_quotes_gpc() ? stripslashes($val) : $val; 
            }

            $params['openid.mode'] = 'check_authentication';

            $data =  http_build_query($params);

            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 
                    "Accept-language: en\r\n".
                    "Content-type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen($data) . "\r\n",
                    'content' => $data,
                    'timeout' => $timeout
                    ),
            ));

            $result = file_get_contents(self::$openId, false, $context);

            preg_match("#^http://steamcommunity.com/openid/id/([0-9]{17,25})#", $_GET['openid_claimed_id'], $matches);

            $steamID64 = is_numeric($matches[1]) ? $matches[1] : 0;

            $response = preg_match("#is_valid\s*:\s*true#i", $result) == 1 ? $steamID64 : null;

        } catch (Exception $e) {
            $response = null;
        }

        if (is_null($response)) {
            throw new Exception('The Steam login request timed out or was invalid');
        }

        return $response;
    }
}
