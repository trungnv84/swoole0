<?php
/*
 * Require: Nanoid-php, symfony/lock
 *
 * https://github.com/hidehalo/nanoid-php
 * Install: composer require hidehalo/nanoid-php
 *
 * https://packagist.org/packages/symfony/lock
 * Install: composer require symfony/lock
 *
 * */

namespace RavenDB;

require_once 'lock.php';

use RavenDB\Lock as RavenLock;
use Hidehalo\Nanoid\Client as NanoId;

class Base
{
    private $server;
    private $database;
    private $pem;

    public function __construct($server, $database, $pem = null)
    {
        $this->server = $server;
        $this->database = $database;
        $this->pem = $pem;
    }

    function add($id_prefix, $doc)
    {
        try {
            $t = 0;
            $nano = new NanoId();
            do {
                $t++;
                $id = $id_prefix . $nano->generateId(21, NanoId::MODE_DYNAMIC);
                $old = $this->get($id);
            } while ($old && $t < 10);
            if ($t < 10) {
                return $this->put($id, $doc);
            }
        } catch (\Exception $e) {

        }
    }

    function put($id, $doc)
    {
        $url = $this->_url('/docs?id=' . $id);
        $body = json_encode($doc);
        return $this->_exec('PUT', $url, 201, $body);
    }

    function get($id)
    {
        $url = $this->_url('/docs?id=' . $id);
        return $this->_exec('GET', $url, 200, null)->Results[0];
    }

    function del($id)
    {
        $url = $this->_url('/docs?id=' . $id);
        $this->_exec('DELETE', $url, 204, null);
    }

    function query($query, $args = null)
    {
        $r = $this->raw_query($query, $args);
        return $r->Results;
    }

    function raw_query($query, $args = null)
    {
        $url = $this->_url('/queries');
        $body = json_encode(array('Query' => $query, 'QueryParameters' => $args));
        return $this->_exec('POST', $url, 200, $body);
    }

    private function _exec($method, $url, $expectedStatusCode, $body, Array $curl_options_array = [])
    {
        $curl = curl_init($url);
        try {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            if (count($curl_options_array) > 0) {
                curl_setopt_array($curl, $curl_options_array);
            }
            if ($this->pem != null) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($curl, CURLOPT_SSLCERT, $this->pem);
            }
            if ($body != null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
            }
            $response = curl_exec($curl);
            if (!($error_code = curl_errno($curl))) {
                switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                    case $expectedStatusCode:
                        return json_decode($response);
                    case 404:
                        return null;
                    default:
                        echo $response;
                        throw new \Exception("$url GOT $http_code - $response");
                }
            } else {
                echo self::ERROR_CODES[$error_code];
                throw new \Exception("$url GOT $error_code - $response");
            }
        } finally {
            curl_close($curl);
        }
    }

    private function _url($path)
    {
        return $this->server . '/databases/' . $this->database . $path;
    }

    const ERROR_CODES = array(
        1 => 'CURLE_UNSUPPORTED_PROTOCOL',
        2 => 'CURLE_FAILED_INIT',
        3 => 'CURLE_URL_MALFORMAT',
        4 => 'CURLE_URL_MALFORMAT_USER',
        5 => 'CURLE_COULDNT_RESOLVE_PROXY',
        6 => 'CURLE_COULDNT_RESOLVE_HOST',
        7 => 'CURLE_COULDNT_CONNECT',
        8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
        9 => 'CURLE_REMOTE_ACCESS_DENIED',
        11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
        13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
        14 => 'CURLE_FTP_WEIRD_227_FORMAT',
        15 => 'CURLE_FTP_CANT_GET_HOST',
        17 => 'CURLE_FTP_COULDNT_SET_TYPE',
        18 => 'CURLE_PARTIAL_FILE',
        19 => 'CURLE_FTP_COULDNT_RETR_FILE',
        21 => 'CURLE_QUOTE_ERROR',
        22 => 'CURLE_HTTP_RETURNED_ERROR',
        23 => 'CURLE_WRITE_ERROR',
        25 => 'CURLE_UPLOAD_FAILED',
        26 => 'CURLE_READ_ERROR',
        27 => 'CURLE_OUT_OF_MEMORY',
        28 => 'CURLE_OPERATION_TIMEDOUT',
        30 => 'CURLE_FTP_PORT_FAILED',
        31 => 'CURLE_FTP_COULDNT_USE_REST',
        33 => 'CURLE_RANGE_ERROR',
        34 => 'CURLE_HTTP_POST_ERROR',
        35 => 'CURLE_SSL_CONNECT_ERROR',
        36 => 'CURLE_BAD_DOWNLOAD_RESUME',
        37 => 'CURLE_FILE_COULDNT_READ_FILE',
        38 => 'CURLE_LDAP_CANNOT_BIND',
        39 => 'CURLE_LDAP_SEARCH_FAILED',
        41 => 'CURLE_FUNCTION_NOT_FOUND',
        42 => 'CURLE_ABORTED_BY_CALLBACK',
        43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
        45 => 'CURLE_INTERFACE_FAILED',
        47 => 'CURLE_TOO_MANY_REDIRECTS',
        48 => 'CURLE_UNKNOWN_TELNET_OPTION',
        49 => 'CURLE_TELNET_OPTION_SYNTAX',
        51 => 'CURLE_PEER_FAILED_VERIFICATION',
        52 => 'CURLE_GOT_NOTHING',
        53 => 'CURLE_SSL_ENGINE_NOTFOUND',
        54 => 'CURLE_SSL_ENGINE_SETFAILED',
        55 => 'CURLE_SEND_ERROR',
        56 => 'CURLE_RECV_ERROR',
        58 => 'CURLE_SSL_CERTPROBLEM',
        59 => 'CURLE_SSL_CIPHER',
        60 => 'CURLE_SSL_CACERT',
        61 => 'CURLE_BAD_CONTENT_ENCODING',
        62 => 'CURLE_LDAP_INVALID_URL',
        63 => 'CURLE_FILESIZE_EXCEEDED',
        64 => 'CURLE_USE_SSL_FAILED',
        65 => 'CURLE_SEND_FAIL_REWIND',
        66 => 'CURLE_SSL_ENGINE_INITFAILED',
        67 => 'CURLE_LOGIN_DENIED',
        68 => 'CURLE_TFTP_NOTFOUND',
        69 => 'CURLE_TFTP_PERM',
        70 => 'CURLE_REMOTE_DISK_FULL',
        71 => 'CURLE_TFTP_ILLEGAL',
        72 => 'CURLE_TFTP_UNKNOWNID',
        73 => 'CURLE_REMOTE_FILE_EXISTS',
        74 => 'CURLE_TFTP_NOSUCHUSER',
        75 => 'CURLE_CONV_FAILED',
        76 => 'CURLE_CONV_REQD',
        77 => 'CURLE_SSL_CACERT_BADFILE',
        78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
        79 => 'CURLE_SSH',
        80 => 'CURLE_SSL_SHUTDOWN_FAILED',
        81 => 'CURLE_AGAIN',
        82 => 'CURLE_SSL_CRL_BADFILE',
        83 => 'CURLE_SSL_ISSUER_ERROR',
        84 => 'CURLE_FTP_PRET_FAILED',
        84 => 'CURLE_FTP_PRET_FAILED',
        85 => 'CURLE_RTSP_CSEQ_ERROR',
        86 => 'CURLE_RTSP_SESSION_ERROR',
        87 => 'CURLE_FTP_BAD_FILE_LIST',
        88 => 'CURLE_CHUNK_FAILED'
    );
}