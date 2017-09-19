<?php

namespace Nalingia\Repositories;

use Illuminate\Support\ServiceProvider;
use Nalingia\Repositories\Console\RepositoryMakeCommand;

class RepositoriesServiceProvider extends ServiceProvider {

  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot() {
    $this->publishes([
      __DIR__ . '/../config/repositories.php' => config_path('repositories.php'),
    ], 'config');
  }

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register() {
    $this->mergeConfigFrom(__DIR__ . '/../config/repositories.php', 'repositories');
    $this->commands([
      RepositoryMakeCommand::class
    ]);
  }
}