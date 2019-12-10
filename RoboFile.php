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
        if ($this->config['initialized'] ?? false) {
            if ($this->askDefault(
                    'Project already initialized. Are you sure you want to run init again? [y/N]',
                    'n'
                ) !== 'y') {
                return;
            }
        }

        $this->config['initialized'] = true;

        $this->createProjectConfiguration();
        $this->createDirectories();
        $this->initTemplateFiles();
        $this->replaceContents();
        $this->removeFilesAndDirs();

        $this->taskComposerDumpAutoload()
            ->run();
    }

    /**
     * Generowanie kluczy OpenSSL dla JWT
     */
    function initJwt($env)
    {
        $publicKeyPath = $this->projectPath.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'jwt'.DIRECTORY_SEPARATOR.$env.DIRECTORY_SEPARATOR.'public.pem';
        $privateKeyPath = $this->projectPath.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'jwt'.DIRECTORY_SEPARATOR.$env.DIRECTORY_SEPARATOR.'private.pem';

        if (file_exists($privateKeyPath) === false) {
            $this->say('Generowanie klucza JWT');

            $this->taskExecStack()
                ->stopOnFail()
                ->exec('openssl genrsa -out data/jwt/private.pem -aes256 4096')
                ->run();

            $this->say(
                'Proszę uzupełnić parametr jwt_key_pass_phrase w pliku app/config/parameters.yml zgodnie z ustawionym powyżej hasłem.'
            );
        } else {
            $this->say('KLucz prywatny JWT już istnieje.');
        }

        if (file_exists($publicKeyPath) === false) {
            $this->say('Generowanie klucza publicznego JWT');

            $this->taskExecStack()
                ->stopOnFail()
                ->exec('openssl rsa -pubout -in data/jwt/private.pem -out data/jwt/public.pem')
                ->run();

            $this->say(
                'Proszę uzupełnić parametr jwt_key_pass_phrase w pliku app/config/parameters.yml zgodnie z ustawionym powyżej hasłem.'
            );
        } else {
            $this->say('KLucz publiczny JWT już istnieje.');
        }

        $this->taskFilesystemStack()->chmod($publicKeyPath, 0644)
            ->chmod($privateKeyPath, 0644)->run();
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

        $nameDash = $this->config['name-dash'] ?? null;
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

    private function replaceConfigVariables($text)
    {
        $numberInt = (int)str_replace('ns', '', $this->config['number']);

        return str_replace(
            [
                "[namespace]",
                "[name]",
                "[name-dash]",
                "[number-int]",
                "[number]",
            ],
            [
                $this->config['namespace'],
                $this->config['name'],
                $this->config['name-dash'],
                $numberInt,
                $this->config['number'],
            ],
            $text
        );
    }

    private function removeFilesAndDirs()
    {
        $directories = $this->config['skeleton']['remove'] ?? [];

        $task = $this->taskFilesystemStack();

        foreach ($directories as $directory) {
            $directory = $this->projectPath.DIRECTORY_SEPARATOR.$this->replaceConfigVariables($directory);

            if (file_exists($directory)) {
                $task->remove($directory);
            }
        }

        $task->run();
    }

    private function createDirectories()
    {
        $directories = $this->config['skeleton']['directories'] ?? [];

        $task = $this->taskFilesystemStack();

        foreach ($directories as $directory) {
            $directory = $this->projectPath.DIRECTORY_SEPARATOR.$this->replaceConfigVariables($directory);

            if (!file_exists($directory)) {
                $task->mkdir($directory)
                    ->touch($directory.DIRECTORY_SEPARATOR.'.gitkeep');
            }

        }

        $task->run();
    }

    private function initTemplateFiles()
    {
        $templateFiles = $this->config['skeleton']['template-files'] ?? [];

        foreach ($templateFiles as $file) {
            $srcPath = $this->projectPath.DIRECTORY_SEPARATOR.$file['template'];
            $force = $file['force'] ?? false;

            if (file_exists($srcPath)) {
                $contents = file_get_contents($srcPath);
                $contents = $this->replaceConfigVariables($contents);
                $destPath = $this->projectPath.DIRECTORY_SEPARATOR.$file['dest'];
                $destPath = $this->replaceConfigVariables($destPath);

                if (!file_exists($destPath) || $force) {
                    $this->taskWriteToFile($destPath)
                        ->text($contents)
                        ->run();
                }
            }
        }
    }

    private function replaceContents()
    {
        $replaces = $this->config['skeleton']['replace'] ?? [];

        foreach ($replaces as $replace) {
            $srcPath = $this->projectPath.DIRECTORY_SEPARATOR.$replace['src'];
            $from = $replace['find'];
            $to = $this->replaceConfigVariables($replace['replace']);

            $this->taskReplaceInFile($srcPath)
                ->from($from)
                ->to($to)
                ->run();
        }
    }

}
