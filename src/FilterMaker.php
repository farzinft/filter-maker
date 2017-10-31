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

    protected $description = 'Create a new Filter class';


    public function __construct(Filesystem $files)
    {
        parent::__construct($files);

        $this->loadFilters();
    }

    protected function loadFilters()
    {
        $this->filterInputs = config('filter-maker');
    }

    public function fire()
    {
        collect($this->filterInputs)->each(function ($filterInputs, $filterName) {

            $name = $this->qualifyClass($filterName);

            $filePath = $this->getPath(self::PATH . $filterName);

            if (!$this->files->exists($filePath)) {
                $this->makeDirectory($filePath);
                $this->files->put($filePath, $this->buildClass($name));
                $this->info($filterName . ' created successfully.');
                $this->writeInputFunctions($filePath, $filterInputs);
                $this->info( join("\n", $filterInputs) . PHP_EOL . 'created successfully.');
            }else {
                $this->editFile($filePath, $filterInputs);
                $this->info($filterName . ' Modified');
            }

        });
    }


    protected function writeInputFunctions($filePath, $filterInputs)
    {
        $contents = explode("\n",$this->files->get($filePath));

        foreach ($contents as $lineNum => &$content) {

            if($lineNum == 12) {
                $content = $this->createFunction($filterInputs, $content);
            }
            if($lineNum == 8) {
                $content = $this->createMethodNamesProperty($filterInputs);
            }
        }

        $this->files->put($filePath, implode("\n", $contents));
    }

    protected function editFile($filePath, $filterInputs)
    {
        $methodNamesProperty = $this->createMethodNamesProperty($filterInputs);
        $contents = explode("\n", $this->files->get($filePath));
        $result = [];

        foreach ($filterInputs as $filterInput) {
            if (!strpos($this->files->get($filePath), Str::camel($filterInput))) {
                $funcName = "\n";
                $funcName .= "\t" . 'public function ' . Str::camel($filterInput) . '($value)' . PHP_EOL;
                $funcName .= "\t" . '{' . PHP_EOL;
                $funcName .= "\t" . '}';
                $result[] = $funcName;
            }
        }
        foreach ($contents as $line => $content) {
            if(str_contains($content, 'protected $mapMethodNames')) {
                $contents[$line] = $methodNamesProperty;
            }
        }

        $stringContents =  join("\n", $result);
        end($contents);
        $lastLine = key($contents);
        array_splice($contents, $lastLine, -1, $stringContents);
        file_put_contents($filePath, join("\n", $contents));
    }

    protected function createMethodNamesProperty(array $filterInputs)
    {
        $filterInputs = array_map(function ($input) {
            return "'{$input}'";
        }, $filterInputs);
         return "\t" . 'protected $mapMethodNames = [' . join(',', $filterInputs) . '];';
    }

    protected function createFunction(array $filterInputs)
    {
        $result = [];
        foreach ($filterInputs as $filterInput) {
                $funcName  = "\n";
                $funcName  .= "\t" . 'public function ' . Str::camel($filterInput) . '($value)' . PHP_EOL;
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