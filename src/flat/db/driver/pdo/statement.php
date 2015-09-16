<?php
/**
 * \flat\db\driver\pdo\statement class 
 *
 * PHP version >=5.6
 * 
 * Copyright (c) 2012-2015 Doug Bird. 
 *    All Rights Reserved. 
 * 
 * NO LICENSE, EXPRESS OR IMPLIED, BY THE COPYRIGHT OWNERS
 * OR OTHERWISE, IS GRANTED TO ANY INTELLECTUAL PROPERTY IN THIS SOURCE FILE.
 *
 * ALL WORKS HEREIN ARE CONSIDERED TO BE TRADE SECRETS, AND AS SUCH ARE AFFORDED 
 * ALL CRIMINAL AND CIVIL PROTECTIONS AS APPLICABLE.
 * 
 * @license see /flat/LICENSE.txt
 */
namespace flat\db\driver\pdo;
/**
 * structurally prepare, execute, and fetch from a \PDO statement
 * 
 * @package    flat\db
 * @author     D. Bird <retran@gmail.com>
 * @copyright  Copyright (c) 2012-2014 Doug Bird. All Rights Reserved.
 * @version    0.1.0-alpha
 * 
 */
class statement extends \flat\core\collection 
{
   const default_fetch_class = "\\flat\\data\\generic";
   const default_limit=100;
   /**
    * @param \PDO $pdo \PDO instance
    * @param \flat\db\driver\pdo\statement\rules $rules structural rules for 
    *    creating SQL, including mapping and binding WHERE clause.
    * @param string $data_class (optional) if given, will fetch all results
    * 
    * @uses \flat\db\driver\pdo\statement\rules
    */
   public function __construct(\PDO $pdo,statement\rules $rules) {
      //if (empty($data_class)) $data_class = self::default_data_class;

      $param=(array) $rules;
      
      /*
       * param sanity enforce:
       *    'criteria','criteria_hash','column_hash'
       *    must be array with at least 1 element
       */
      foreach (
         array('criteria','criteria_hash','column_hash')
         as $key
      ) {
         if (!isset($param[$key]) || !is_array($param[$key])) throw new \flat\db\driver\pdo\exception\bad_rule(
            $key,
            "must be array with at least 1 element"
         );
      }
      
      /*
       * param sanity enforce:
       *    'join_hash', 'order', 'join'
       *    must be array if specified (but may be 0 len array)
       */
      foreach (
         array('join_hash','order') 
         as $key
      ) {
         if (isset($param[$key]) && $param[$key]!=NULL && !is_array($param[$key])) throw new \flat\db\driver\pdo\exception\bad_rule(
            $key,
            "must be array or NULL if given"
         );
      }
      
      /*
       * param sanity enforce:
       *    'table_name', 'table_alias' 
       *    must be specified, must be string, must be alpha numeric
       *       and must start with alpha char
       */
      foreach (array('table_name','table_alias') as $key) {
         if (empty($param[$key]) || !is_string($param[$key])) throw new \flat\db\driver\pdo\exception\bad_rule(
            $key,
            "must be non-empty string"
         );
         if(preg_match('/[^a-zA-Z_0-9]/', $param[$key]) || preg_match('/[^a-zA-Z]/', substr($param[$key],0,1))) {
            throw new \flat\db\driver\pdo\exception\bad_rule(
               $key,
               "must be alphanumeric string and start with alpha char"
            );
         }
      }
      
      /*
       * param sanity enforce:
       *    'start', 'limit'
       *    must be integer if specified
       */
      foreach (array('skip','limit') as $key) {
         if (isset($param[$key]) && !is_int($param[$key])) throw new \flat\db\driver\pdo\exception\bad_rule(
            $key,
            "must be int"
         );
      }
      
      /*
       * param sanity enforce:
       *    'start'
       *    must be 0 or greater if specified
       */
      if (isset($param['skip']) && $param['skip']<0) throw new \flat\db\driver\pdo\exception\bad_rule(
         $key,
         "must be 0 or greater"
      );
      
      /*
       * param sanity enforce:
       *    'limit'
       *    must be 1 or greater if specified
       */
      if (isset($param['limit']) && $param['limit']<1) throw new \flat\db\driver\pdo\exception\bad_rule(
         $key,
         "must be 1 or greater"
      );
      
      /*
       * initialize $options array, dereference from $param['options']
       */
      $options=array();
      if (is_array($param['options'])) $options = $param['options']; //dereference options array
      
      $fetch_callback = NULL;
      $fetch_class = NULL;
      $statement = $this;
      
      /**
       * @var callable|NULL $fetch_callback
       * @var string|NULL $fetch_class
       * @var \flat\db\driver\pdo\statement $statement
       * 
       * @todo complete implementation of the following
       * $options['fetch'] sanitize and dereference:
       *    if it's boolean true: 
       *       add results as \flat\data\generic (self::default_fetch_class)
       *       using \flat\core\collection parent methods
       *    if it's a string:
       *       enforce that its a valid instantiable class,
       *       add results as that class using \flat\core\collection parent methods
       */
      if (isset($options['fetch'])) {
         if (is_bool($options['fetch'])) {
            if ($options['fetch']===true) {
               $fetch_class = self::default_fetch_class;
               $fetch_callback = function(array $data) use(&$statement,$fetch_class) {
                  $statement->add(new $fetch_class($data));
               };
            } else {
               $fetch_callback = function(){};
            }
         } else   
         if (is_string($options['fetch'])) {
            if (class_exists($options['fetch'])) {
               $fetch_class= $options['fetch'];
               $r = new \ReflectionClass($fetch_class);
               if (!$r->isInstantiable()) throw new \flat\db\driver\pdo\exception\bad_option(
                  'fetch',
                  "if string value, must be instantiable class name"
               );
               
               /*
                * if it's a \flat\data object, 
                *    give assoc row data to constructor
                * --otherwise--
                *    iterate visible properties to set their values
                */
               if (is_a($fetch_class,"\\flat\\data",true)) {
                  $fetch_callback = function(array $data) use(&$statement,$fetch_class) {
                     $statement->add(new $fetch_class($data));
                  };
               } else {
                  $fetch_callback = function(array $data) use(&$statement,$fetch_class) {
                     $fetch_object = new $fetch_class();
                     foreach ($fetch_object as $prop=>&$val) {
                        if (isset($data[$prop])) $val = $data[$prop];
                     }
                     $statement->add($fetch_object);
                  };
               }
            } else {
               throw new \flat\db\driver\pdo\exception\bad_option(
                  'fetch',
                  "if string value, must be instantiable class name"
               );
               
            }
         } else
         if (is_callable($options['fetch'])) {
            $fetch_callback = $options['fetch'];
         } throw new \flat\db\driver\pdo\exception\bad_option(
            'fetch',
            "if specified, value must be one of the following types: ".
            "(callable) {callback function to invoke for each row}, or ".
            "(string) '{instantiable class name}' adds new instance of class to this collection for each row, ".
            "(bool) {false: no fetch occurs | true: creates and adds generic object to this collection for each row}"
         );
         
      }

      /**
       * @var string $table_name table name for SELECT...FROM clause,
       *    dereferenced from param 'table_name'.
       * 
       * @var string $table_alias alias for table to correlate to $column_hash
       *    and $join_clause, dereferenced from param 'table_alias'.
       * 
       * @var string $join_clause join clause (or empty string) needed for 
       *    SELECT...FROM clause, dereferenced from param 'join_clause'.
       * 
       * @var string[] $join_hash assoc array of join clauses that may be needed 
       *    depending on specified criteria (only added to SQL if correlating
       *    $criteria assoc key is specified), dereferenced from param 'join_hash'.
       */
      $join_hash=array();
      if (is_array($param['join_hash'])) $join_hash = $param['join_hash']; //dereference join_hash array
      $table_name = $param['table_name'];
      $table_alias = $param['table_alias'];
      $join_clause = "";
      if (!empty($param['join_clause'])) $join_clause = $param['join_clause'];
      if (empty($param['skip'])) $param['skip']=0;
      if (empty($param['limit'])) $param['limit']=self::default_limit;
      $order = array();
      if (!empty($param['order'])) $order = $param['order'];
      $joinc = array();
      if (!empty($param['join'])) $joinc= $param['join'];
      $column_hash = $param['column_hash'];
      /*
       * sanitize join_clause
       */
      //$clause_test = $pdo->quote($join_clause);
      $clause_comp = preg_replace("/[\s]+/", " ", $join_clause);//condenses multiple whitespace chars
      $clause_comp = preg_replace("/[\s]/", " ", $clause_comp);//replace all whitespace with space
      $clause_test = trim($pdo->quote($clause_comp),"'");//escape value, removing quote
      if ($clause_comp!=$clause_test) throw new \flat\db\driver\pdo\exception\bad_rule(
         'join_clause',
         "syntax error: invalid chars found in sql clause"
      );
      $badchar=";'\"\\G";
      for($i=0;$i<strlen($badchar);$i++) {
         if (false!==strpos($join_clause,$badchar[$i])) {
            throw new \flat\db\driver\pdo\exception\bad_rule(
               'join_clause',
               'syntax error: invalid char '.$badchar[$i].' found in sql clause'
            );
         }
      }
      /*
       * initialize some arrays for prepare/bind commands
       * 
       */
      $bind = array(); //list of params to bind
      $where = array(); //list of SQL WHERE clauses
      $join = array();
      
      /**
       * iterate through possible criteria keys to prepare params to bind 
       *    and WHERE clause.
       * 
       * @var string[] $bind becomes list of PDO PARAMS needed for $criteria key
       * @var string[] $where list of SQL WHERE clauses needed for criteria
       */
      //var_dump($param['criteria']);
      foreach ($param['criteria_hash'] as $key=>$col) {
         // echo "\n";
         // var_dump($col);
         // echo "key=$key dump db/driver/pdo"."\n\n";
         
         if (isset($param['criteria'][$key])) {
            if (is_scalar($param['criteria'][$key])) {
               $bind[$key] = $param['criteria'][$key];
               if (is_array($col)) {
                  foreach ($col as $jkey=>$tcol) {
                     $where[] = "$tcol = :$key";
                     if (isset($join_hash[$jkey])) {
                        $join[$jkey]=$join_hash[$jkey];
                     }
                  }
               } else {
                  $where[] = "$col = :$key";
               }
            } else {
               throw new \flat\db\driver\pdo\exception\bad_criteria(
                  "$key",
                  "non-scalar value given for criteria '$key'"
               );
            }
         }
      }
      if (!count($where)) {
         //$where = "";
         throw new \flat\db\driver\pdo\exception\bad_rule(
            'criteria',
            "must provide at least one of the following criteria:".
            implode(", ", array_keys($param['criteria_hash']))
            ." bind: ".implode(", ", array_keys($bind))
         );
      }

      $where = "WHERE\n   ".implode("\nAND\n   ",$where);
      
      //var_dump($bind);var_dump($where);echo "dump: flat/db/driver/pdo\n\n";
      
      $clist=array();
      $order_clause=array();
      foreach($column_hash as $pre=>$col) {
         if(empty($pre) || !is_string($pre) || preg_match('/[^a-zA-Z_0-9]/', $pre) || preg_match('/[^a-zA-Z]/', substr($pre,0,1))) {
            throw new \flat\db\driver\pdo\exception\bad_rule(
               'column_hash',
               "all assoc keys must be alphanumeric strings that start with alpha char"
            );
         }
         foreach ($col as $name=>$map) {
            if(empty($map) || !is_string($map) || preg_match('/[^a-zA-Z_0-9]/', $map) || preg_match('/[^a-zA-Z]/', substr($map,0,1))) {
               throw new \flat\db\driver\pdo\exception\bad_rule(
                  'column_hash',
                  "all assoc column references must be alphanumeric strings that start with alpha char"
               );
            }
            if ( empty($name) || is_numeric((string) $name) ) {
               $name = $map;
            } else {
               if(!is_string($name) || preg_match('/[^a-zA-Z_0-9]/', $name) || preg_match('/[^a-zA-Z]/', substr($name,0,1))) {
                  throw new \flat\db\driver\pdo\exception\bad_rule(
                     'column_hash',
                     "all assoc column alias references must be alphanumeric strings that start with alpha char"
                  );
               }               
            }
            if (isset($order[$pre]) && is_array($order[$pre]) && count($order[$pre]) && (isset($order[$pre][$name]))) {
               $o = "ASC";
               if (strtoupper($order[$pre][$name])=="DESC") $o="DESC";
               $order_clause[] = "$pre.$name $o";
            }
            $clist[] = "$pre.$name as ".$pre."_$map";
         }
      }
      $nothing=array('join'=>array(
         'a'=>array('artist_id'=>array('apa'=>'artist_id'))
      ));
// LEFT JOIN
   // artists t1
// ON
   // t1.artist_id=t2.artist_id
      // $nothing=array('join'=>array(
         // 't1'=>array('LEFT'=>array('artists'=>array(
            // 'artist_id'=>array('t2'=>'artist_id')
          // )))
      // ));
         // 'join'=>array(
            // 'a'=>array('LEFT'=>array('artists'=>array(
               // 'artist_id'=>array('apa'=>'artist_id')
             // )))
          // ),
// LEFT JOIN
   // artists a
// ON
   // a.artist_id=apa.artist_id
      $nothing = array(
         'join'=>array(
            'a'=>array('artists'=>array(
               'LEFT'=>array(
                  'apa'=>'artists_id'
               )
            ))
          ));
      $joinrel = array();
      foreach ($joinc as $t1alias=>$rel) {
         if (is_array($rel)) {
            foreach ($rel as $dir=>$on) {
               $dir=strtoupper($dir);
               if (is_array($on) && ($dir=='LEFT'||$dir=='INNER'||$dir=="RIGHT"||$dir="OUTER"||$dir="LEFT INNER")) {
                  
                  foreach ($on as $t1name => $on2) {
                     $relon=array();
                     if (is_array($on2)) {
                        foreach ($on2 as $c1name=>$rel2) {
                           if (is_array($rel2)) {
                              foreach ($rel2 as $t2alias=>$c2name) {
                                 $relon[] = "   $t1alias.$c1name=$t2alias.$c2name";
                              } 
                           }
                        }
                     }
                     if (count($relon)) {
                        $joinrel[]="LEFT JOIN\n   $t1name $t1alias\nON\n   ".implode("AND\n",$relon);
                     }
                  }
                  
               }
            }

         }
      }
      $order_by="";
      if (count($order_clause)) {
         $order_by = "ORDER BY\n   ".implode(",\n",$order_clause);
      }
      $join_clause = $join_clause . implode("\n",$join) . implode("\n",$joinrel);
      //var_dump($order_by);echo "db driver statement echo\n";
      $col = implode(",\n   ",$clist);
      $sth = $pdo->prepare($sql="
SELECT
   $col
FROM
   $table_name $table_alias
$join_clause
$where
$order_by
LIMIT :skip,:limit
      ");
      //LIMIT :skip,:limit
      //var_dump($sql);echo "/flat/db/driver/pdo sql\n\n";
      /*
       * bind some params that are ints skip,limit
       */
      foreach (array('skip','limit') as $val) {
         //echo "\ndb driver pdo statement echo: $val=".$param[$val]."for sql\n$sql\n";
         $sth->bindValue(":$val",$param[$val],\PDO::PARAM_INT);
      }
      /*
       * bind given $criteria
       */
      foreach ($bind as $key=>$val) {
         if (is_int($val) || is_float($val)) {
            $type = \PDO::PARAM_INT;
         } else {
            $type = \PDO::PARAM_STR;
         }
         $sth->bindValue(":$key",$val,$type);
      }
      if (!empty($options['execute']) || !empty($options['fetch']) || !empty($options['fetch_callback'])) {
         $sth->execute();
         if (!$sth->rowCount()) throw new \flat\db\driver\pdo\exception\not_found(
            $bind
         );
      }
      if (!empty($options['fetch_callback'])) {
         $callback = $options['fetch_callback'];
         while ($data = $sth->fetch(\PDO::FETCH_ASSOC) ) $callback($data);
         return;
      }
      while ($data = $sth->fetch(\PDO::FETCH_ASSOC) ) new $data_class($data);
   }
}



















