<?php
namespace Farzin\FilterMaker;

use function collect;
use function config;
use const DIRECTORY_SEPARATOR;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use const PHP_EOL;

class FilterMaker extends GeneratorCommand
{

    const NAME_SPACE = 'App\\Filters\\';
    const PATH = 'Filters/';

    protected $files;

    protected $filterInputs;

    protected $signature = 'make:filter';

    protected $name = 'make:filter';

    protected $description = 'Create a new Filter class';


    public function __construct(Filesystem $files)
    {
        parent::__construct($files);

        $this->loadFilters();
    }

    protected function loadFilters()
    {
        $this->filterInputs = config('filter-inputs');
    }

    public function fire()
    {
        collect($this->filterInputs)->each(function ($filterInputs, $filterName) {

            $name = $this->qualifyClass($filterName);

            $filePath = $this->getPath(self::PATH . $filterName);

            $this->makeDirectory($filePath);

            $this->files->put($filePath, $this->buildClass($name));

            $this->info( $filterName . ' created successfully.');

            $this->files->append($filePath, $this->createFunction($filterInputs));

        });
    }


    protected function createFunction(array $filterInputs)
    {
        $result = [];
        foreach ($filterInputs as $filterInput) {
            $funcName  = "\n";
            $funcName  .= "\t" . 'public function ' . Str::camel($filterInput) . '()' . PHP_EOL;
            $funcName .= "\t" . '{' . PHP_EOL;
            $funcName .= "\t" . '}';
            $result[] = $funcName;
        }
        return join("\n", $result);
    }

    protected function qualifyClass($name)
    {
        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '../stub/filter.stub';
    }
}