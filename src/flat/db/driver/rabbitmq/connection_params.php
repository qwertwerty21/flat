<?php
/**
 * class definition 
 *
 * PHP version >=5.6
 * 
 * Copyright (c) 2012-2015 Doug Bird. 
 *    All Rights Reserved. 
 * 
 * COPYRIGHT NOTICE:
 * The flat framework. https://github.com/katmore/flat
 * Copyright (C) 2012-2015  Doug Bird.
 * ALL RIGHTS RESERVED. THIS COPYRIGHT APPLIES TO THE ENTIRE CONTENTS OF THE WORKS HEREIN
 * UNLESS A DIFFERENT COPYRIGHT NOTICE IS EXPLICITLY PROVIDED WITH AN EXPLANATION OF WHERE
 * THAT DIFFERENT COPYRIGHT APPLIES. WHERE SUCH A DIFFERENT COPYRIGHT NOTICE IS PROVIDED
 * IT SHALL APPLY EXCLUSIVELY TO THE MATERIAL AS DETAILED WITHIN THE NOTICE.
 * 
 * The flat framework is copyrighted free software.
 * You can redistribute it and/or modify it under either the terms and conditions of the
 * "The MIT License (MIT)" (see the file MIT-LICENSE.txt); or the terms and conditions
 * of the "GPL v3 License" (see the file GPL-LICENSE.txt).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * @license The MIT License (MIT) http://opensource.org/licenses/MIT
 * @license GNU General Public License, version 3 (GPL-3.0) http://opensource.org/licenses/GPL-3.0
 * @link https://github.com/katmore/flat
 * @author     D. Bird <retran@gmail.com>
 * @copyright  Copyright (c) 2012-2015 Doug Bird. All Rights Reserved.
 */
namespace flat\db\driver\rabbitmq;
interface connection_params {
   /**
    * provides rabbitmq connection params
    *
    *  string $param['host'] required
    *  string $param['port'] (optional)
    *  string $param['user'] (optional)
    *  bool $param['password'] (optional)
    *  string $param['vhost'] (optional)
    *  bool $param['insist'] (optional)
    *  string $param['login_method'] (optional)
    *  null $param['login_response'] (optional)
    *  string $param['locale'] (optional)
    *  int $param['connection_timeout'] (optional)
    *  int $param['read_write_timeout'] (optional)
    *  null $param['context'] (optional)
    *  bool $param['keepalive'] (optional)
    *  int $param['heartbeat'] (optional)
    *
    * @return array
    * @see \flat\db\driver\rabbitmq\connection_params part of
    *    connection_params interface.
    */
   public function get_rabbitmq_connection_params();
}