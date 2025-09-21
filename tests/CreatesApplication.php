<?php<?php



namespace Tests;namespace Tests;



use Illuminate\Support\Facades\Artisan;use Illuminate\Support\Facades\Artisan;

use Illuminate\Contracts\Console\Kernel;use Illuminate\Contracts\Console\Kernel;

use Illuminate\Foundation\Application;use Illuminate\Foundation\Application;



trait CreatesApplicationtrait CreatesApplication

{{

    /**    /**

     * Creates the application.     * Creates the application.

     */     *

    public function createApplication(): Application     * @return \Illuminate\Foundation\Application

    {     */

        $app = require __DIR__.'/../bootstrap/app.php';    public function createApplication(): Application

    {

        $app->make(Kernel::class)->bootstrap();        $app = require __DIR__.'/../bootstrap/app.php';



        return $app;        $app->make(Kernel::class)->bootstrap();

    }

}        $this->clearConfigurationCached($app);

        return $app;
    }

    /**
     * Clear any cached configuration.
     *
     * @param  Application  $app
     * @return void
     */
    protected function clearConfigurationCached($app): void
    {
        if (file_exists($app->getCachedConfigPath())) {
            unlink($app->getCachedConfigPath());
        }

        if (file_exists($app->getCachedRoutesPath())) {
            unlink($app->getCachedRoutesPath());
        }
    }
}