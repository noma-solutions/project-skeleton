<?php

use Robo\Tasks;

/**
 * Tasks for better project's development based on Robo.
 */
class RoboFile extends Tasks
{
    /**
     * @var string
     */
    protected $projectPath;

    /**
     * @var string
     */
    protected $projectConfigPath;

    /**
     * @var array
     */
    protected $config;

    public function __construct()
    {
        $this->projectPath = dirname(__FILE__);
        $this->projectConfigPath = $this->projectPath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'project.json';
        $this->readConfig();
    }

    /**
     * Initializes the plain Symfony 4.4 project source structure, default configuration and standard files.
     */
    public function init()
    {
        $this->createProjectConfiguration();
    }

    public function createProjectConfiguration()
    {
        $name = $this->config['name'] ?? null;
        if (!$name) {
            do {
                $name = $this->ask('Project name (i.e. `Demo Project`:');
            } while (!$name);

            $this->config['name'] = $name;
        }

        $nameDash =  $this->config['name-dash']  ?? null;
        if (!$nameDash) {
            $nameDash = $this->createNameDash($name);
            $this->config['name-dash'] = $nameDash;
        }

        $suggestedNamespace = $this->suggestNamespace($name);

        $number = $this->config['number'] ?? null;
        if (!$number) {
            do {
                $number = $this->ask('Project number (i.e. `ns01143`):');
            } while (!$number);

            $this->config['number'] = $number;
        }

        $namespace = $this->config['namespace'] ?? null;
        if (!$namespace) {
            do {
                $namespace = $this->askDefault('Namespace:', $suggestedNamespace);
            } while (!$namespace);

            $this->config['namespace'] = $namespace;
        }

        $this->writeln('Saving configuration: '.$this->projectConfigPath);
        $this->taskWriteToFile($this->projectConfigPath)
            ->text(json_encode($this->config, JSON_PRETTY_PRINT))
            ->run();
    }

    private function suggestNamespace($name)
    {
        $exploded = explode(' ', $name);
        $ucfirst = array_map(
            function ($namePart) {
                return ucfirst($namePart);
            },
            $exploded
        );

        return implode('', $ucfirst);
    }

    private function createNameDash($name)
    {
        $exploded = explode(' ', $name);
        $ucfirst = array_map(
            function ($namePart) {
                return strtolower($namePart);
            },
            $exploded
        );

        return implode('-', $ucfirst);
    }

    private function readConfig()
    {
        if (file_exists($this->projectConfigPath)) {
            $this->config = json_decode(file_get_contents($this->projectConfigPath), true);
        } else {
            $this->config = [
                'name' => null,
                'number' => null,
                'namespace' => null,
                'skeleton' => [],
            ];
        }
    }

}
