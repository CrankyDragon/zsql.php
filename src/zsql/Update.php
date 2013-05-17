<?php

namespace zsql;

/**
 * Update query generator
 */
class Update extends ExtendedQuery
{
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
    $this->push('UPDATE')
         ->pushTable()
         ->push('SET')
         ->pushValues()
         ->pushWhere()
         ->pushOrder()
         ->pushLimit();
  }
  
  /**
   * Alias for {@link Update::value()} or {@link Update::values()}
   * 
   * @param mixed $key
   * @param mixed $value
   * @return \zsql\Update
   */
  public function set($key, $value = null)
  {
    if( is_array($key) ) {
      return $this->values($key);
    } else {
      return $this->value($key, $value);
    }
  }
  
  /**
   * Alias for {@link Update::table()} and {@link Update::values()}
   * 
   * @param string $table
   * @param array $values
   * @return \zsql\Update
   */
  public function update($table, array $values = null)
  {
    $this->table($table);
    if( !empty($values) ) {
      $this->values($values);
    }
    return $this;
  }
  
  /**
   * Set a value
   * 
   * @param mixed $key
   * @param mixed $value
   * @return \zsql\Update
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
   * @return \zsql\Update
   */
  public function values(array $values)
  {
    $this->values = $values;
    return $this;
  }
}
