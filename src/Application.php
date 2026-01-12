<?php
//to hold the application configurations and settings
//to run the application [routes and database settings]
namespace Elmasry;
use  Elmasry\Http\Request ;
use  Elmasry\Http\Response ;   
use  Elmasry\Http\Route ;   
use  Elmasry\Support\Config ;
use Elmasry\Support\Session;
use Elmasry\Database\Database;
use Elmasry\Database\Migrator;
use App\Seeders\DatabaseSeeder;

class Application {

protected $route ;
protected $request ;    

protected $response ;

protected $config ;
public $session ;

public function __construct()
{
    $this->request = new  Request;
    $this->response = new Response;
    $this->session = new Session;
    $this->route = new Route($this->request , $this->response);
    $this->config = new Config($this->loadConfigurations());
    
    // Initialize database and run migrations/seeders
    $this->initializeDatabase();
}


  public function config()
    {
        return $this->config;
    }
    protected function loadConfigurations()
    {
        foreach(scandir(config_path()) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filename = explode('.', $file)[0];

            yield $filename => require config_path() . $file;
        }

    }

public function run()
{
$this->route->resolve();
}

/**
 * Initialize database: run migrations and seeders if needed
 */
protected function initializeDatabase(): void
{
    // Establish database connection
    Database::connection();
    
    // Run pending migrations (creates tables if not exist)
    $migrator = new Migrator();
    $result = $migrator->migrate();
    
    // Run seeders only if migrations were executed (fresh tables)
    if ($result['status'] === 'success' && !empty($result['migrated'])) {
        $seeder = new DatabaseSeeder();
        $seeder->run();
    }
}

/**
 * Get the database connection
 */
public function db(): \PDO
{
    return Database::connection();
}
}