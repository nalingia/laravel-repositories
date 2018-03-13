<?php

namespace Nalingia\Repositories\Tests;
use Orchestra\Testbench\TestCase;
use \InvalidArgumentException;

class RepositoryCreatorTest extends TestCase {

  /**
   * Repository creator instance.
   *
   * @var \Nalingia\Repositories\RepositoryCreator
   */
  private $_repositoryCreator;

  /**
   * @var string Base path for test files creation.
   */
  private $_basePath = __DIR__ . DIRECTORY_SEPARATOR . 'temp';

  /**
   * @var string Model name.
   */
  private $_model = 'TestModel';

  /**
   * @var string Missing model name.
   */
  private $_missingModel = 'FooModel';

  /**
   * @var array Configuration for repository creator.
   */
  private $_config = [
    'base_application_namespace' => 'Nalingia\Repositories\Tests',
    'model_base_path' => 'Models',
    'repositories_base_path' => 'Repositories',
    'repositories_base_namespace' => 'Repositories',
    'repository_contract_base_path' => 'Repositories/Contracts',
    'repository_contract_base_namespace' => 'Repositories\Contracts',
    'pluralise' => true,
  ];

  public function setUp() {
    parent::setUp();

    $this->deleteDirectory($this->_basePath);
    $this->setUpConfiguration($this->app, $this->_config);
    $this->_repositoryCreator = $this->app->make('Nalingia\Repositories\RepositoryCreator');
  }

  /** @test */
  public function it_should_throw_an_invalid_argument_exception_if_model_does_not_exist() {
    $this->expectException(InvalidArgumentException::class);
    $this->_repositoryCreator->create($this->_missingModel, $this->_basePath);
  }

  /** @test */
  public function it_should_create_repository_contract_and_implementation() {
    list($repositoryPath, $contractPath) = $this->_repositoryCreator->create($this->_model, $this->_basePath);
    $expectingRepositoryNamespace = $this->_basePath . DIRECTORY_SEPARATOR . $this->_config['repositories_base_path'] . DIRECTORY_SEPARATOR . "TestModelsRepository.php";
    $expectingContractNamespace = $this->_basePath . DIRECTORY_SEPARATOR . $this->_config['repository_contract_base_path'] . DIRECTORY_SEPARATOR . "TestModelsRepositoryContract.php";

    $this->assertEquals($expectingRepositoryNamespace, $repositoryPath);
    $this->assertEquals($expectingContractNamespace, $contractPath);
    $this->assertFileExists($repositoryPath);
    $this->assertFileExists($contractPath);
  }

  private function setUpConfiguration($app, $config) {
    $app['config']->set('repositories', $config);
  }

  private function deleteDirectory(string $path) {
    if (!file_exists($path)) {
      return true;
    }

    if (!is_dir($path)) {
      return unlink($path);
    }
    foreach (scandir($path) as $item) {
      if ($item == '.' || $item == '..') {
        continue;
      }
      if (! $this->deleteDirectory($path . DIRECTORY_SEPARATOR . $item)) {
        return false;
      }
    }
    return rmdir($path);
  }
}