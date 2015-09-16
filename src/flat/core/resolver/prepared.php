<?php
/**
 * \flat\core\resolver\prepared definition 
 *
 * PHP version >=5.6
 * 
 * Copyright (c) 2012-2015 Doug Bird. 
 *    All Rights Reserved. 
 * 
 * BY ACCESSING THE CONTENTS OF THIS SOURCE FILE IN ANY WAY YOU AGREE TO BE 
 * BOUND BY THE PROVISIONS OF THE "SOURCE FILE ACCESS AGREEMENT", A COPY OF 
 * WHICH CAN IS FOUND IN THE FILE 'LICENSE.txt'.
 * 
 * NO LICENSE, EXPRESS OR IMPLIED, BY THE COPYRIGHT OWNERS
 * OR OTHERWISE, IS GRANTED TO ANY INTELLECTUAL PROPERTY IN THIS SOURCE FILE.
 *
 * ALL WORKS HEREIN ARE CONSIDERED TO BE TRADE SECRETS, AND AS SUCH ARE AFFORDED 
 * ALL CRIMINAL AND CIVIL PROTECTIONS AS APPLICABLE.
 * 
 * @license see /flat/LICENSE.txt
 */
namespace flat\core\resolver;
/**
 * prepared
 * 
 * @package    flat\core\route
 * @author     D. Bird <retran@gmail.com>
 * @copyright  Copyright (c) 2012-2015 Doug Bird. All Rights Reserved.
 * @version    0.1.0-alpha
 * 
 */
interface prepared {
   /**
    * Called on a \flat\core\routable object by a resolving controller 
    *    (such as \flat\core\controller\route ) when it is finished with the object.
    * 
    * @return void
    * 
    * @see \flat\core\controller\route::__construct() the route resolving controller
    * 
    */
   public function set_prepared_on();
}






















