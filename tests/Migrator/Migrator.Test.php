<?php

class Migrator_Test extends Common_Test
{
    public function testMigrationsA() {
        
        $migrator = new zsql\Migrator\Migrator(array(
            'database' => $this->databaseFactory(),
            'migrationPath' => __DIR__ . '/../fixtures/migrationsA.php',
            'namespace' => 'zsql\\Tests\\Fixtures\\MigrationsA\\',
        ));
        $migrations = $migrator->getMigrations();
        //var_dump($migrations);
        
        $this->assertEquals(2, count($migrations));
    }
}
