<?php

namespace Stru\LumenGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Stru\LumenGenerator\Common\CommandData;
use Stru\LumenGenerator\Generators\Admin\AdminControllerGenerator;
use Stru\LumenGenerator\Generators\Admin\AdminRoutesGenerator;
use Stru\LumenGenerator\Generators\Admin\AdminServiceGenerator;
use Stru\LumenGenerator\Generators\API\APIControllerGenerator;
use Stru\LumenGenerator\Generators\API\APIRoutesGenerator;
use Stru\LumenGenerator\Generators\API\APIServiceGenerator;
use Stru\LumenGenerator\Generators\ModelGenerator;
use Stru\LumenGenerator\Utils\FileUtil;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BaseCommand extends Command
{
    /**
     * The command Data.
     *
     * @var CommandData
     */
    public $commandData;

    /**
     * @var Composer
     */
    public $composer;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->composer = app()['composer'];
    }

    public function handle()
    {
        $this->commandData->modelName = $this->argument('model');

        $this->commandData->initCommandData();
        $this->commandData->getFields();
    }

    public function generateCommonItems()
    {

        if (!$this->isSkip('model')) {
            $modelGenerator = new ModelGenerator($this->commandData);
            $modelGenerator->generate();
        }
    }

    public function generateAPIItems()
    {
        if (!$this->isSkip('controllers') and !$this->isSkip('api_controller')) {
            $controllerGenerator = new APIControllerGenerator($this->commandData);
            $controllerGenerator->generate();
        }

        if (!$this->isSkip('service')) {
            $serviceGenerator = new APIServiceGenerator($this->commandData);
            $serviceGenerator->generate();
        }

        if (!$this->isSkip('routes') and !$this->isSkip('api_routes')) {
            $routesGenerator = new APIRoutesGenerator($this->commandData);
            $routesGenerator->generate();
        }

    }

    public function generateAdminItems()
    {
        if (!$this->isSkip('controllers') and !$this->isSkip('admin_controller')) {
            $controllerGenerator = new AdminControllerGenerator($this->commandData);
            $controllerGenerator->generate();
        }

        if (!$this->isSkip('service')) {
            $serviceGenerator = new AdminServiceGenerator($this->commandData);
            $serviceGenerator->generate();
        }

        if (!$this->isSkip('routes') and !$this->isSkip('admin_routes')) {
            $routesGenerator = new AdminRoutesGenerator($this->commandData);
            $routesGenerator->generate();
        }

    }
    
    public function isSkip($skip)
    {
        if ($this->commandData->getOption('skip')) {
            return in_array($skip, (array) $this->commandData->getOption('skip'));
        }

        return false;
    }

    private function saveSchemaFile()
    {
        $fileFields = [];

        foreach ($this->commandData->fields as $field) {
            $fileFields[] = [
                'name'        => $field->name,
                'dbType'      => $field->dbInput,
                'htmlType'    => $field->htmlInput,
                'validations' => $field->validations,
                'searchable'  => $field->isSearchable,
                'fillable'    => $field->isFillable,
                'primary'     => $field->isPrimary,
                'inForm'      => $field->inForm,
                'inIndex'     => $field->inIndex,
                'inView'      => $field->inView,
            ];
        }

        foreach ($this->commandData->relations as $relation) {
            $fileFields[] = [
                'type'     => 'relation',
                'relation' => $relation->type.','.implode(',', $relation->inputs),
            ];
        }

        $path = config('lumen_generator.path.schema_files', resource_path('model_schemas/'));

        $fileName = $this->commandData->modelName.'.json';

        if (file_exists($path.$fileName) && !$this->confirmOverwrite($fileName)) {
            return;
        }
        FileUtil::createFile($path, $fileName, json_encode($fileFields, JSON_PRETTY_PRINT));
        $this->commandData->commandComment("\nSchema File saved: ");
        $this->commandData->commandInfo($fileName);
    }

    private function saveLocaleFile()
    {
        $locales = [
            'singular' => $this->commandData->modelName,
            'plural'   => $this->commandData->config->mPlural,
            'fields'   => [],
        ];

        foreach ($this->commandData->fields as $field) {
            $locales['fields'][$field->name] = Str::title(str_replace('_', ' ', $field->name));
        }

        $path = config('lumen_generator.path.models_locale_files', base_path('resources/lang/en/models/'));

        $fileName = $this->commandData->config->mCamelPlural.'.php';

        if (file_exists($path.$fileName) && !$this->confirmOverwrite($fileName)) {
            return;
        }
        $content = "<?php\n\nreturn ".var_export($locales, true).';'.\PHP_EOL;
        FileUtil::createFile($path, $fileName, $content);
        $this->commandData->commandComment("\nModel Locale File saved: ");
        $this->commandData->commandInfo($fileName);
    }

    /**
     * @param $fileName
     * @param string $prompt
     *
     * @return bool
     */
    protected function confirmOverwrite($fileName, $prompt = '')
    {
        $prompt = (empty($prompt))
            ? $fileName.' already exists. Do you want to overwrite it? [y|N]'
            : $prompt;

        return $this->confirm($prompt, false);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['fieldsFile', null, InputOption::VALUE_REQUIRED, 'Fields input as json file'],
            ['jsonFromGUI', null, InputOption::VALUE_REQUIRED, 'Direct Json string while using GUI interface'],
            ['plural', null, InputOption::VALUE_REQUIRED, 'Plural Model name'],
            ['tableName', null, InputOption::VALUE_REQUIRED, 'Table Name'],
            ['fromTable', null, InputOption::VALUE_NONE, 'Generate from existing table'],
            ['ignoreFields', null, InputOption::VALUE_REQUIRED, 'Ignore fields while generating from table'],
            ['save', null, InputOption::VALUE_NONE, 'Save model schema to file'],
            ['primary', null, InputOption::VALUE_REQUIRED, 'Custom primary key'],
            ['prefix', null, InputOption::VALUE_REQUIRED, 'Prefix for all files'],
            ['paginate', null, InputOption::VALUE_REQUIRED, 'Pagination for index.blade.php'],
            ['skip', null, InputOption::VALUE_REQUIRED, 'Skip Specific Items to Generate (migration,model,controllers,api_controller,scaffold_controller,repository,requests,api_requests,scaffold_requests,routes,api_routes,scaffold_routes,views,tests,menu,dump-autoload)'],
            ['datatables', null, InputOption::VALUE_REQUIRED, 'Override datatables settings'],
            ['relations', null, InputOption::VALUE_NONE, 'Specify if you want to pass relationships for fields'],
            ['softDelete', null, InputOption::VALUE_NONE, 'Soft Delete Option'],
            ['forceMigrate', null, InputOption::VALUE_NONE, 'Specify if you want to run migration or not'],
            ['localized', null, InputOption::VALUE_NONE, 'Localize files.'],
            ['repositoryPattern', null, InputOption::VALUE_REQUIRED, 'Repository Pattern'],
            ['connection', null, InputOption::VALUE_REQUIRED, 'Specify connection name'],
            ['jqueryDT', null, InputOption::VALUE_NONE, 'Generate listing screen into JQuery Datatables'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'Singular Model name'],
        ];
    }
}
