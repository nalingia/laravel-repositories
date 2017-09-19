<?php

namespace Nalingia\Repositories;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;
use function Functional\{
  compose,
  curry
};

class RepositoryCreator {

  /**
   * The Filesystem instance.
   *
   * @var Filesystem
   */
  protected $_filesystem;

  /**
   * Create a new repository.
   *
   * @param Filesystem $filesystem
   */
  public function __construct(Filesystem $filesystem) {
    $this->_filesystem = $filesystem;
  }

  /**
   * Create a new repository for the given model.
   *
   * @param string $model
   * @param string $basePath
   * @return array
   */
  public function create($model, $basePath) {
    $this->ensureModelExists($model);
    $this->ensureRepositoryDoesntAlreadytExist($model);
    $this->ensureRepositoryContractDoesntAlreadytExist($model);


    $repositoryStub = $this->getRepositoryStub();
    $contractStub = $this->getRepositoryContractStub();

    $this->_filesystem->put(
      $repositoryPath = $this->getRepositoryPath($model, $basePath),
      $this->populateRepositoryStub($model, $repositoryStub)
    );

    $this->_filesystem->put(
      $contractPath = $this->getRepositoryContractPath($model, $basePath),
      $this->populateRepositoryContractStub($model, $contractStub)
    );

    return [
      $repositoryPath, $contractPath
    ];
  }

  /**
   * Get the full path of the model's repository.
   *
   * @param string $model
   * @param string $path
   * @return string
   */
  protected function getRepositoryPath($model, $path) {
    $repositoriesBasePath = $path . '/' . config('repositories.repositories_base_path');
    if (!file_exists($repositoriesBasePath)) {
      mkdir($repositoriesBasePath, 0777, true);
    }
    return $repositoriesBasePath . '/' . $this->getRepositoryClassName($model) . '.php';
  }

  /**
   * Get the full path of the model's repository contract.
   *
   * @param string $model
   * @param string $path
   * @return string
   */
  protected function getRepositoryContractPath($model, $path) {
    $contractBasePath = $path . '/' . config('repositories.repository_contract_base_path');
    if (!file_exists($contractBasePath)) {
      mkdir($contractBasePath, 0777, true);
    }
    return $contractBasePath . '/' . $this->getRepositoryContractClassName($model) . '.php';
  }

  /**
   * Replace placeholders in repository stub.
   *
   * @param string $model
   * @param string $stub
   * @return string
   */
  protected function populateRepositoryStub($model, $stub) {
    $curriedPregReplace = curry('preg_replace');
    $replaceRepositoryClassName = $curriedPregReplace('/DummyClass/', $this->getRepositoryClassName($model));
    $replaceRepositoryContractClassName = $curriedPregReplace('/DummyContract/', $this->getRepositoryContractClassName($model));
    $replaceRepositoryContractFullyQualifiedNamespace = $curriedPregReplace('/DummyRepositoryContractNamespace/', $this->getRepositoryContractNamespace($model));
    $replaceModelFullyQualifiedNamespace = $curriedPregReplace('/DummyModelNamespace/', $this->getModelNamespace($model));
    $replaceModelClassName = $curriedPregReplace('/DummyModel/', $this->getClassName($model));
    $replaceRepositoryNamespace = $curriedPregReplace('/DummyRepositoryBaseNamespace/', $this->getRepositoryBaseNamespace());

    $createClass = compose(
      $replaceRepositoryClassName,
      $replaceRepositoryContractClassName,
      $replaceRepositoryContractFullyQualifiedNamespace,
      $replaceModelFullyQualifiedNamespace,
      $replaceModelClassName,
      $replaceRepositoryNamespace
    );

    return $createClass($stub);
  }

  /**
   * Replace placeholders in repository contract stub.
   *
   * @param string $model
   * @param string $stub
   * @return string
   */
  protected function populateRepositoryContractStub($model, $stub) {
    $curriedPregReplace = curry('preg_replace');
    $replaceRepositoryContractClassName = $curriedPregReplace('/DummyContract/', $this->getRepositoryContractClassName($model));
    $replaceRepositoryContractBaseNamespace = $curriedPregReplace('/DummyRepositoryContractBaseNamespace/', $this->getRepositoryContractBaseNamespace());

    $createClass = compose(
      $replaceRepositoryContractClassName,
      $replaceRepositoryContractBaseNamespace
    );

    return $createClass($stub);
  }

  /**
   * Get the repository stub file.
   *
   * @return string
   */
  protected function getRepositoryStub() {
    return $this->_filesystem->get($this->stubPath() . '/repository.stub');
  }

  /**
   * Get the repository stub file.
   *
   * @return string
   */
  protected function getRepositoryContractStub() {
    return $this->_filesystem->get($this->stubPath() . '/contract.stub');
  }

  /**
   * Get path to the stubs.
   *
   * @return string
   */
  public function stubPath() {
    return __DIR__ . '/stubs';
  }

  /**
   * Get the class name of the model name.
   *
   * @param string $model
   * @return string
   */
  protected function getClassName($model) {
    return Str::studly($model);
  }

  /**
   * Get the repository's class name.
   *
   * @param string $model
   * @return string
   */
  protected function getRepositoryClassName($model) {
    $modelPrefix = config('repositories.pluralise') ? Str::plural($model) : $model;
    return $modelPrefix . 'Repository';
  }

  /**
   * Get the repository contract's class name.
   *
   * @param string $model
   * @return string
   */
  protected function getRepositoryContractClassName($model) {
    $modelPrefix = config('repositories.pluralise') ? Str::plural($model) : $model;
    return $modelPrefix . 'RepositoryContract';
  }

  /**
   * Ensure that the model exists.
   *
   * @param string $model
   * @return void
   *
   * @throws \InvalidArgumentException
   */
  protected function ensureModelExists($model) {
    if (!class_exists($classNamespace = $this->getModelNamespace($model))) {
      throw new InvalidArgumentException("{$classNamespace} does not exist.");
    }
  }

  /**
   * Ensure that a repository for the given model does not already exist.
   *
   * @param string $model
   * @return void
   *
   * @throws \InvalidArgumentException
   */
  protected function ensureRepositoryDoesntAlreadytExist($model) {
    if (class_exists($classFullyQualified = $this->getRepositoryNamespace($model), false)) {
      throw new InvalidArgumentException("{$classFullyQualified} already exists.");
    }
  }

  /**
   * Ensure that a repository contract for the given model does not already exist.
   *
   * @param string $model
   * @return void
   *
   * @throws \InvalidArgumentException
   */
  protected function ensureRepositoryContractDoesntAlreadytExist($model) {
    if (class_exists($classFullyQualified = $this->getRepositoryContractNamespace($model), false)) {
      throw new InvalidArgumentException("{$classFullyQualified} already exists.");
    }
  }

  /**
   * Get the model's fully qualified class name.
   *
   * @param string $model
   * @return string
   */
  protected function getModelNamespace($model) {
    return config('repositories.base_application_namespace') . '\\' . config('repositories.model_base_path') . '\\' . $this->getClassName($model);
  }

  /**
   * Get the repository's fully qualified class name.
   *
   * @param string $model
   * @return string
   */
  protected function getRepositoryNamespace($model) {
    return config('repositories.base_application_namespace') . '\\' . config('repositories.repositories_base_namespace') . '\\' . $this->getRepositoryClassName($model);
  }

  /**
   * Get the repository contract's fully qualified class name.
   *
   * @param string $model
   * @return string
   */
  protected function getRepositoryContractNamespace($model) {
    return config('repositories.base_application_namespace') . '\\' . config('repositories.repository_contract_base_namespace') . '\\' . $this->getRepositoryContractClassName($model);
  }

  /**
   * Return repository base namespace.
   *
   * @return string
   */
  protected function getRepositoryBaseNamespace() {
    return config('repositories.base_application_namespace') . '\\' . config('repositories.repositories_base_namespace');
  }

  /**
   * Return repository contract base namespace.
   *
   * @return string
   */
  protected function getRepositoryContractBaseNamespace() {
    return config('repositories.base_application_namespace') . '\\' . config('repositories.repository_contract_base_namespace');
  }

  /**
   * Get the filesystem instance.
   *
   * @return \Illuminate\Filesystem\Filesystem
   */
  public function getFilesystem() {
    return $this->_filesystem;
  }
}