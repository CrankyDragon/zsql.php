<?php

namespace zsql;

/**
 * Base abstract query
 */
abstract class Query
{
  /**
   * Toggle whether to interpolate parameters into the query
   * 
   * @var boolean
   */
  protected $interpolation = false;
  
  /**
   * The table
   * 
   * @var string
   */
  protected $table;
  
  /**
   * The parameters to bind
   * 
   * @var array
   */
  protected $params;
  
  /**
   * Array of query fragments
   * 
   * @var array
   */
  protected $parts;
  
  /**
   * Callback to quote a string
   * 
   * @var callback
   */
  protected $quoteCallback;
  
  /**
   * The current query
   * 
   * @var string
   */
  protected $query;
  
  /**
   * The function to proxy calls to query to
   * 
   * @var callback
   */
  protected $queryCallback;
  
  /**
   * The character to use to quote strings
   * 
   * @var string
   */
  protected $quoteChar = "'";
  
  /**
   * The character to use to quote identifiers
   * 
   * @var string
   */
  protected $quoteIdentifierChar = '`';
  
  /**
   * Constructor 
   * 
   * @param callback $queryCallback
   */
  public function __construct($queryCallback = null)
  {
    if( $queryCallback ) {
      $this->queryCallback = $queryCallback;
    }
  }
  
  /**
   * Magic string conversion. Alias of {@link Query::toString()}
   * 
   * @return string
   */
  public function __toString()
  {
    try {
      return $this->toString();
    } catch( Exception $e ) {
      trigger_error($e->getMessage(), E_USER_WARNING);
      return '';
    }
  }
  
  /**
   * Assemble parts
   */
  abstract protected function assemble();
  
  /**
   * Interpolate parameters into query
   * 
   * @throws \zsql\Exception
   */
  protected function interpolateParams()
  {
    if( count($this->params) <= 0 ) {
      return;
    }
    if( !$this->quoteCallback ) {
      throw new \zsql\Exception('Interpolation not available without setting a quote callback');
    }
    if( substr_count($this->query, '?') != count($this->params) ) {
      throw new \zsql\Exception('Parameter count mismatch');
    }
    
    $parts = explode('?', $this->query);
    $query = $parts[0];
    for( $i = 0, $l = count($this->params); $i < $l; $i++ ) {
      $query .= call_user_func($this->quoteCallback, $this->params[$i]) . $parts[$i+1];
    }
    $this->query = $query;
  }
  
  /**
   * Toggle whether to interpolate parameters into the query
   * 
   * @return \zsql\Query
   */
  public function interpolation($interpolation = true)
  {
    $this->interpolation = (bool) $interpolation;
    return $this;
  }
  
  /**
   * Get the parameters
   * 
   * @return array
   */
  public function params()
  {
    return (array) $this->params;
  }
  
  /**
   * Get the array of parts
   * 
   * @return array
   */
  public function parts()
  {
    return (array) $this->parts;
  }
  
  /**
   * Push an arbitrary string onto parts
   * 
   * @param string $string
   * @return \zsql\Query
   */
  protected function push($string)
  {
    $this->parts[] = $string;
    return $this;
  }
  
  /**
   * Push table onto parts
   * 
   * @return \zsql\Query
   * @throws \zsql\Exception
   */
  protected function pushTable()
  {
    if( empty($this->table) ) {
      throw new \zsql\Exception('No table specified');
    }
    $this->parts[] = $this->quoteIdentifierIfNotExpression($this->table);
    return $this;
  }
  
  /**
   * Push values onto parts
   * 
   * @return \zsql\Query
   * @throws \zsql\Exception
   */
  protected function pushValues()
  {
    if( empty($this->values) ) {
      throw new \zsql\Exception('No values specified');
    }
    foreach( $this->values as $key => $value ) {
      if( !is_int($key) ) {
        $this->parts[] = $this->quoteIdentifierIfNotExpression($key);
        $this->parts[] = '=';
      }
      if( $value instanceof Expression ) {
        $this->parts[] = (string) $value;
      } else if( !is_int($key) ) {
        $this->parts[] = '?';
        $this->params[] = $value;
      }
      $this->parts[] = ',';
    }
    array_pop($this->parts);
    
    return $this;
  }
  
  /**
   * Proxy to query callback
   * 
   * @return mixed
   * @throws \zsql\Exception
   */
  public function query()
  {
    if( !$this->queryCallback ) {
      throw new \zsql\Exception('query() called when no callback set');
    }
    $query = $this->toString();
    $params = $this->params();
    if( $this->interpolation ) {
      return call_user_func($this->queryCallback, $query);
    } else {
      return call_user_func($this->queryCallback, $query, $params);
    }
  }
  
  /**
   * Quotes an identifier
   * 
   * @param string $identifier
   * @return string
   */
  protected function quoteIdentifier($identifier)
  {
    $c = $this->quoteIdentifierChar;
    return $c . str_replace('.', 
        $c . '.' . $c, 
        str_replace($c, $c . $c, $identifier)) . $c;
  }
  
  /**
   * Quotes an identifier if not an {@link Expression}
   * 
   * @param mixed $identifier
   * @return string
   */
  protected function quoteIdentifierIfNotExpression($identifier)
  {
    if( $identifier instanceof Expression ) {
      return (string) $identifier;
    } else {
      return $this->quoteIdentifier($identifier);
    }
  }
  
  /**
   * Set the function to use to quote strings
   * 
   * @param type $callback
   * @return \zsql\Query
   * @throws \zsql\Exception
   */
  public function setQuoteCallback($callback)
  {
    if( !is_callable($callback) ) {
      throw new \zsql\Exception('Invalid callback specified');
    }
    $this->quoteCallback = $callback;
    return $this;
  }
  
  /**
   * Set the table
   * 
   * @param mixed $table
   * @return \zsql\Query
   */
  public function table($table)
  {
    if( $table instanceof Expression ) {
      $this->table = $table;
    } else {
      $this->table = (string) $table;
    }
    return $this;
  }
  
  /**
   * Convert to string
   * 
   * @return string
   */
  public function toString()
  {
    $this->parts = array();
    $this->params = array();
    $this->assemble();
    $this->query = join(' ', $this->parts);
    if( $this->interpolation ) {
      $this->interpolateParams();
    }
    return $this->query;
  }
}
