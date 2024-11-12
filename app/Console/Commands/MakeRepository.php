<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make new repository with all files in specific module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getRepositoryStub()
    {
        return __DIR__.'/stubs/Repository.stub';
    }    
    
    protected function getRepositoryInterfaceStub()
    {
        return __DIR__.'/stubs/RepositoryInterface.stub';
    }    

    protected function getRepositoryServiceProviderStub()
    {
        return __DIR__.'/stubs/RepositoryServiceProvider.stub';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $module_name = $this->ask('What is the module name?');
        $entity_name = $this->ask('What is the entity name?');
        $path = null;
        if(app('modules')->find($module_name)){
            $path = module_path($module_name);
        }else{
            $this->info('Module not found!');
            return;
        }
        if (!file_exists($path . '/Repositories')) {
            mkdir($path . '/Repositories', 0777, true);
        }
        if (!file_exists($path . '/Repositories/Contracts')) {
            mkdir($path . '/Repositories/Contracts', 0777, true);
        }

        $repoFile = $this->getRepositoryStub();
        $repoInterfaceFile = $this->getRepositoryInterfaceStub();
        $repoServieProviderFile = $this->getRepositoryServiceProviderStub();
        $repoFilePath = $path.'/Repositories/'.$entity_name.'Repository.php';
        $repoInterfacePath = $path.'/Repositories/Contracts/'.$entity_name.'RepositoryInterface.php';
        $repoServiceProviderPath = $path.'/Providers/RepositoryServiceProvider.php';
        if(!copy($repoFile, $repoFilePath)){
            echo "failed to copy $repoFile";
        }
        if(!copy($repoInterfaceFile, $repoInterfacePath)){
            echo "failed to copy $repoFile";
        }    
        if (!file_exists($repoServiceProviderPath)) {
            if(!copy($repoServieProviderFile, $repoServiceProviderPath)){
                $this->info("failed to copy $repoServieProviderFile");
            }
            // add to config file
            $oldContent = file_get_contents('config/app.php');
            $search = "// Modules Providers";
            $replace = "
                // Modules Providers
                Modules\\".$module_name."\Providers\RepositoryServiceProvider::class,
            ";
            $newContent = str_replace($search, $replace, $oldContent);
            file_put_contents('config/app.php', $newContent);
        }else{
            // append
            $oldContent = file_get_contents($repoServiceProviderPath);
            $search = "// append";
            $replace = "
                $"."this->app->bind(
                    'Modules\@module_name@\Repositories\Contracts\@entity_name@RepositoryInterface',
                    'Modules\@module_name@\Repositories\@entity_name@Repository'
                );

                // append
            ";
            $newContent = str_replace($search, $replace, $oldContent);
            file_put_contents($repoServiceProviderPath, $newContent);
        }


        $searchAndReplaces = [
            '@module_name@' => $module_name,
            '@entity_name@' => $entity_name,
        ];

        $repo = file_get_contents($repoFilePath);
        $interface = file_get_contents($repoInterfacePath);
        $provider = file_get_contents($repoServiceProviderPath);
        foreach ($searchAndReplaces as $search => $replace) {
            $repo = str_replace($search, $replace, $repo);
            $interface = str_replace($search, $replace, $interface);
            $provider = str_replace($search, $replace, $provider);
        }
        file_put_contents($repoFilePath, $repo);
        file_put_contents($repoInterfacePath, $interface);
        file_put_contents($repoServiceProviderPath, $provider);

        $this->info("Created: " . $repoFilePath);
        $this->info("Created: " . $repoInterfacePath);
        $this->info("Thank You. Have a nice day");
    }
}
