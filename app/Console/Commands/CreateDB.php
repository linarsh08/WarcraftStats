<?php
/**
 * Created by PhpStorm.
 * User: vanyaz158
 * Date: 15.11.16
 * Time: 1:14
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDB extends Command
{
    protected $signature = 'db:create';
    protected $description = 'Create database';

    protected $directConnection;
    protected $dbName;

    public function __construct(\PDO $directConnection)
    {
        parent::__construct();

        $this->directConnection = $directConnection;
        $this->dbName = env('DB_DATABASE');
    }

    public function handle()
    {
        $result = $this->directConnection->exec("CREATE DATABASE $this->dbName");

        if ($result === false)
            $this->error("Database $this->dbName already created");
        else
            $this->line("Database $this->dbName successfully created");
    }
}
