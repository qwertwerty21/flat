<?php
/**
 * \flat\cloud\aws\client_provider\from_config class 
 *
 * PHP version >=7.1
 * 
 * Copyright (c) 2012-2017 Doug Bird. 
 *    All Rights Reserved. 
 * 

 * 
 * @license see /flat/LICENSE.txt
 */
namespace flat\cloud\aws\client_provider\from_config;
/**
 * namespace alias for AWS
 */
use \Aws\S3\S3Client;
/**
 * s3 configuration for \flat\app extending
 * 
 * @package    flat\cloud\aws
 * @author     D. Bird <retran@gmail.com>
 * @copyright  Copyright (c) 2012-2014 Doug Bird. All Rights Reserved.
 * @version    0.1.0-alpha
 * 
 */
class s3 extends \flat\cloud\aws\client_provider\from_config {
   /**
    * @return \Aws\S3\S3Client
    */
   protected function _client_from_config($config_ns) {
      return new S3Client([
         'credentials' => [
            /**
             * @var string $key AWS key
             */      
            'key'    => $key = \flat\core\config::get($config_ns.'/key'),
            /**
             * @var string $secret AWS secret
             */             
            'secret' => $secret = \flat\core\config::get($config_ns.'/secret'),
         ],
         
         'region'=> $secret = \flat\core\config::get($config_ns.'/region'),
         
         'version'=>"2006-03-01",
      ]);
   }
}









