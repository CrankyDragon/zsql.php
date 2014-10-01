<?php

namespace zsql\Tests\Fixtures\MigrationsA;

use zsql\Migrator\MigrationAbstract;

class Migration1412129062_TestA extends MigrationAbstract
{
    public function up()
    {
        $this->database->query('create table `migrationtesta` ( `test` int );');
    }
    
    public function down()
    {
        $this->database->query('drop table `migrationtesta`');
    }
}

class Migration1412129177_TestB extends MigrationAbstract
{
    public function up()
    {
        $this->database->query('create table `migrationtestb` ( `test` int );');
    }
    
    public function down()
    {
        $this->database->query('drop table `migrationtestb`');
    }
}
