<?php

namespace zsql;

/**
 * Insert query generator
 */
class Insert extends Query
{
  /**
   * Set delayed clause
   * 
   * @var boolean
   */
  protected $delayed;
  
  /**
   * Set ignore clause
   * 
   * @var boolean
   */
  protected $ignore;
  
  /**
   * Set on duplicate key update clause
   * 
   * @var array
   */
  protected $onDuplicateKeyUpdate;
  
  /**
   * Use replace instead of insert
   * 
   * @var boolean
   */
  protected $replace;
  
  /**
   * Values
   * 
   * @var array
   */
  protected $values;
  
  /**
   * Assemble parts
   * 
   * @return void
   */
  protected function assemble()
  {
    $this->push($this->replace ? 
                  'REPLACE' : 
                  'INSERT')
         ->pushIgnoreDelayed()
         ->push('INTO')
         ->pushTable()
         ->push('SET')
         ->pushValues()
         ->pushOnDuplicateKeyUpdate();
  }
  
  /**
   * Set delayed clause
   * 
   * @param boolean $delayed
   * @return \zsql\Insert
   */
  public function delayed($delayed = true)
  {
    $this->delayed = (bool) $delayed;
    return $this;
  }
  
  /**
   * Set ignore clause
   * 
   * @param boolean $ignore
   * @return \zsql\Insert
   */
  public function ignore($ignore = true)
  {
    $this->ignore = (bool) $ignore;
    return $this;
  }
  
  /**
   * Alias for {@link Insert::values()}
   * 
   * @param array $values
   * @return \zsql\Insert
   */
  public function insert($values)
  {
    $this->values($values);
    return $this;
  }
  
  /**
   * Alias for {@link Query::table()}
   * 
   * @param string $table
   * @return \zsql\Insert
   */
  public function into($table)
  {
    $this->table($table);
    return $this;
  }
  
  /**
   * Set on duplicate key update clause
   * 
   * @param array $values
   */
  public function onDuplicateKeyUpdate($key, $value = null)
  {
    if( func_num_args() == 2 ) {
      $this->onDuplicateKeyUpdate[$key] = $value;
    } else if( $key instanceof Expression ) {
      $this->onDuplicateKeyUpdate[] = $key;
    } else if( is_array($key) ) {
      $this->onDuplicateKeyUpdate = $key;
    }
    return $this;
  }
  
  /**
   * Push ignore or delayed onto parts
   * 
   * @return \zsql\Insert
   */
  protected function pushIgnoreDelayed()
  {
    if( $this->delayed ) {
      $this->parts[] = 'DELAYED';
    }
    if( $this->ignore && !$this->replace ) {
      $this->parts[] = 'IGNORE';
    }
    return $this;
  }
  
  /**
   * Push on duplicate key update clause
   * 
   * @return \zsql\Insert
   */
  protected function pushOnDuplicateKeyUpdate()
  {
    if( $this->onDuplicateKeyUpdate ) {
      $this->parts[] = 'ON DUPLICATE KEY UPDATE';
      $tmp = $this->values;
      $this->values = $this->onDuplicateKeyUpdate;
      $this->pushValues();
      $this->values = $tmp;
    }
    return $this;
  }
  
  /**
   * Use replace instead of insert
   * 
   * @param boolean $replace
   * @return \zsql\Insert
   */
  public function replace($replace = true)
  {
    $this->replace = (bool) $replace;
    return $this;
  }
  
  /**
   * Alias for {@link Insert::value()} or {@link Insert::values()}
   * 
   * @param mixed $key
   * @param mixed $value
   * @return \zsql\Insert
   */
  public function set($key, $value = null)
  {
    if( is_array($key) ) {
      $this->values($key);
    } else {
      $this->value($key, $value);
    }
    return $this;
  }
  
  /**
   * Set a value
   * 
   * @param mixed $key
   * @param mixed $value
   * @return \zsql\Insert
   */
  public function value($key, $value = null)
  {
    if( null === $value && $key instanceof Expression ) {
      $this->values[] = $key;
    } else {
      $this->values[$key] = $value;
    }
    return $this;
  }
  
  /**
   * Set values
   * 
   * @param array $values
   * @return \zsql\Insert
   */
  public function values(array $values)
  {
    $this->values = $values;
    return $this;
  }
}
