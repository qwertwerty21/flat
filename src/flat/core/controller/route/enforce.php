<?php
/**
 * \flat\core\controller\route\enforce interface 
 *
 * PHP version >=7.1
 * 
 * Copyright (c) 2012-2017 Doug Bird. 
 *    All Rights Reserved. 
 * 

 * 
 * @license see /flat/LICENSE.txt
 */
namespace flat\core\controller\route;
/**
 * if implemented on a resovled class the route controller will use this
 *    interface to determine enforcement status.
 * 
 * @package    flat\route
 * @author     D. Bird <retran@gmail.com>
 * @copyright  Copyright (c) 2012-2014 Doug Bird. All Rights Reserved.
 * @version    0.1.0-alpha
 * 
 */
interface enforce {
   public function enforce_check();
   public function get_enforce_fail_route();
}