<?php

namespace zsql\Migrator;

use zsql\Database;

abstract class MigrationAbstract
{
  /**
   * @var \zsql\Database
   */
  protected $database;
  
  /**
   * Constructor
   * 
   * @param \zsql\Database $database
   */
  public function __construct(Database $database)
  {
    $this->database = $database;
  }
  
  /**
   * Migrate up
   */
  abstract public function up();
  
  /**
   * Migrate down
   */
  abstract public function down();
}
