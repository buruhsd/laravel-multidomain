<?php namespace Aljawad\Multidomain\Foundation\Console;

use Illuminate\Console\GeneratorCommand;
use Config;

class RemoveDomainCommand extends GeneratorCommand {

    use DomainCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:remove 
                            {domain : The name of the domain to remove from the framework} 
                            {--force : Force the deletion of domain storage dirs also if they exist and they are possibly full}';

    protected $description = "Removes a domain from the framework.";
    protected $domain;

    /*
     * Se il file di ambiente esiste già viene semplicemente sovrascirtto con i nuovi valori passati dal comando (update)
     */
    public function handle() {
        $this->domain = $this->argument('domain');

        /*
         * CREATE ENV FILE FOR DOMAIN
         */
        $this->deleteDomainEnvFile();


        /*
         * Setting domain storage directories
         */

        if ($this->option('force')) {
            $this->deleteDomainStorageDirs();
        }

        /*
         * Update config file
         */

        $this->updateConfigFile('remove');

        $this->line("<info>Removed</info> <comment>" . $this->domain . "</comment> <info>from the application.</info>");
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {

    }

    protected function deleteDomainEnvFile()
    {

        $domainEnvFilePath = $this->getDomainEnvFilePath();
        $envDomainArray = $this->getVarsArray($domainEnvFilePath);
        //Get user
        $envFilePath = base_path() . DIRECTORY_SEPARATOR . Config::get('domain.env_stub', '.env');
        $envArray = $this->getVarsArray($envFilePath);

        $this->removeDomainDatabase($envDomainArray['DB_DATABASE'], $envArray);

        if ($this->files->exists($domainEnvFilePath)) {
            $this->files->delete($domainEnvFilePath);
        }
    }

    public function deleteDomainStorageDirs() {
        $path = $this->getDomainStoragePath($this->domain);
        if ($this->files->exists($path)) {
            $this->files->deleteDirectory($path);
        }
    }

    protected function removeDomainToConfigFile($config) {
        $domains = array_get($config, 'domains', []);
        if (array_key_exists($this->domain, $domains)) {
            unset($domains[$this->domain]);
        }
        $config['domains'] = $domains;
        return $config;
    }

    public function removeDomainDatabase($dbname, $dbconn){
        $conn = new \mysqli($dbconn['DB_HOST'], $dbconn['DB_USERNAME'], $dbconn['DB_PASSWORD']);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // Create database
        $sql = "DROP DATABASE ".$dbname;
        if ($conn->query($sql) === TRUE) {
            echo "Database remove successfully";
        } else {
            echo "Error creating database: " . $conn->error;
        }

        $conn->close();
    }




}
