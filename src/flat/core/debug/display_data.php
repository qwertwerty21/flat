<?php
/**
 * File:
 *    display_data.php
 * 
 * Purpose:
 *    debug message data to display
 *
 *
 * PHP version >=7.1
 *
 * Copyright (c) 2012-2017 Doug Bird. 
 *    All Rights Reserved. 
 * 
 * COPYRIGHT NOTICE:
 * The flat framework. https://github.com/katmore/flat
 * Copyright (c) 2012-2017  Doug Bird.
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
 * @copyright  Copyright (c) 2012-2017 Doug Bird. All Rights Reserved.. All works herein are considered to be trade secrets, and as such are afforded 
 * all criminal and civil protections as applicable to trade secrets.
 * 
 * @package    flat/core
 * @author     D. Bird <retran@gmail.com>
 * @copyright  Copyright (c) 2012-2014 Doug Bird. All Rights Reserved.
 * 
 */
namespace flat\core\debug;
class display_data {
   public $str="";
   public $caller;
   private $_created;
   
   public function __toString() {
      ob_start();
      var_dump($this);
      return ob_get_clean();
   }
   public function get_created() {
      return $this->_created;
   }
   public function __construct($str,$caller) {
      $this->str = $str;
      $this->caller = $caller;
      $this->_created = microtime(true); 
   }
}