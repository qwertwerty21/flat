<?php
/**
 * File:
 *    route.php
 * 
 * Purpose:
 *    create routes in flat/app for route controller
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
 * @copyright  Copyright (c) 2012-2017 Doug Bird. All Rights Reserved..
 * 
 * @package    flat/route
 * @author     D. Bird <retran@gmail.com>
 * @copyright  Copyright (c) 2012-2014 Doug Bird. All Rights Reserved.
 * 
 * 
 */
namespace flat\route;
class factory extends \flat\core\factory {
   private $route; //route collection
   private $base;
   private function _init() {
      if (is_a($this->route,"\\flat\\route\\collection")) return;
      $this->route = new collection();
      /*
       * apply a base
       */
      if (! ($this instanceof base)) {
         $this->base = $this->get_base();
      }
   }
   public function get_route() {
      return $this->route;
   }
   protected function add_route(rule $route) {
      $this->_init();
      //echo "route: ".$route->ns." weight: ".$route->weight."\n";
      if (is_int($route->weight)) {
         $key = $this->route->count()+$route->weight;
      } else {
         $key=NULL;
      }
      $this->route->add($route,$key);
   }
}