<?php

namespace JchOptimize;

use CURLFile;
use curl_init;
use curl_exec;
use RuntimeException;
//use JchOptimizeFileRetriever;

class Kraken
{

        protected $auth = array();

        public function __construct($dlid)
        {
                $this->auth = array(
                                "dlid"    => $dlid
                );
        }

        public function upload($opts = array())
        {
                if (!isset($opts['file']))
                {
                        return array(
                                "success" => false,
                                "error"   => "File parameter was not provided"
                        );
                }

                if (preg_match("/\/\//i", $opts['file']))
                {
                        $opts['url'] = $opts['file'];
                        unset($opts['file']);
                        return $this->url($opts);
                }

                if (!file_exists($opts['file']))
                {
                        return array(
                                "success" => false,
                                "error"   => "File `" . $opts['file'] . "` does not exist"
                        );
                }

                if (class_exists('CURLFile'))
                {
                        $file = new CURLFile($opts['file']);
                }
                else
                {
                        $file = '@' . $opts['file'];
                }

                unset($opts['file']);

                $data = array_merge(array(
                        "file" => $file,
                        "data" => json_encode(array_merge(
                                        $this->auth, $opts
                        ))
                ));

                $response = self::request($data, "http://jchoptimize.com/index.php?option=com_jchio");

                return $response;
        }

        private function request($data, $url)
        {
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_FAILONERROR, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
                curl_setopt($curl, CURLOPT_TIMEOUT, 10);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

                $response = json_decode(curl_exec($curl), true);
                $error    = curl_errno($curl);
                $message = curl_error($curl);
                
                curl_close($curl);

                if ($error > 0)
                {
                        throw new RuntimeException(sprintf('cURL returned with the following error: "%s"', $message));
                }

                return $response;
        }

//        private function request($data, $url)
//        {
//                $oFileRetreiver = JchOptimizeFileRetriever::getInstance();
//                $oFileRetreiver->allow_400;
//                $response = $oFileRetreiver->getFileContents($url, $data, array('Content-Type' => 'multipart/form-data'));
//                
//                return json_decode($response, TRUE);
//        }
}
