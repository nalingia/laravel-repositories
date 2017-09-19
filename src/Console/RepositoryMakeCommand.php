<?php

namespace Nalingia\Repositories\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Nalingia\Repositories\RepositoryCreator;

class RepositoryMakeCommand extends Command {
  /**
   * The console command signature.
   *
   * @var string
   */
  protected $signature = 'make:repository {model : The name of the model}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new repository file.';

  /**
   * The repository creator instance.
   *
   * @var \Nalingia\Repositories\RepositoryCreator
   */
  protected $_creator;

  /**
   * The Composer instance.
   *
   * @var \Illuminate\Support\Composer
   */
  protected $_composer;

  public function __construct(RepositoryCreator $creator, Composer $composer) {
    parent::__construct();
    $this->_creator = $creator;
    $this->_composer = $composer;
  }

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function fire() {
    $model = trim($this->input->getArgument('model'));

    $this->writeRepository($model);

    $this->_composer->dumpAutoloads();
  }

  /**
   * Write the repository file to disk.
   *
   * @param string $model
   * @return void
   */
  protected function writeRepository($model) {
    list($repositoryPath, $contractPath) = $this->_creator->create($model, $this->getLaravel()->basePath() . '/app');

    $repository = pathinfo($repositoryPath, PATHINFO_FILENAME);
    $contract = pathinfo($contractPath, PATHINFO_FILENAME);

    $this->line("<info>Created Repository: </info> {$repository}");
    $this->line("<info>Created Repository Contract: </info> {$contract}");
  }
}