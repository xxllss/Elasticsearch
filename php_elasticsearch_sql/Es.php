<?php
/**
 * Created by PhpStorm.
 * User: xls
 * Date: 2019/12/15
 * Time: 下午2:39
 */

class Es
{
    protected static $url    = 'localhost:9200';
    protected static $auth   = 'username:password';
    protected static $header = ['Content-type:application/json;charset=utf-8'];
    
    /**
     * @param string $url
     * @param array  $params
     *
     * @return array
     */
    public static function post($url,array $params)
    {
        $body = json_encode($params);
        $ch   = curl_init();
        $opt  = array(
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERPWD        => self::$auth,
            CURLOPT_HTTPHEADER     => self::$header,
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_POSTFIELDS     => http_build_query($body),
        );
        
        curl_setopt_array($ch,$opt);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res,true);
    }
    
    protected static function checkErr(array $data)
    {
        if (isset($data['error'])) {
            throw new \Exception($data['error']['reson'],$data['status']);
        }
    }
    
    protected static function getUri()
    {
    }
}
