<?php

namespace Stru\LumenGenerator\Common;

use Illuminate\Support\Str;

class GeneratorConfig
{
    /* Namespace variables */
    public $nsApp;
    public $nsModel;
    public $nsDataTables;
    public $nsModelExtend;

    public $nsAdminService;
    public $nsAdminController;

    public $nsApiService;
    public $nsApiController;

    public $nsController;
    public $nsBaseController;

    /* Path variables */
    public $pathModel;
    public $pathDataTables;

    public $pathAdminService;
    public $pathAdminController;
    public $pathAdminRoutes;

    public $pathApiService;
    public $pathApiController;
    public $pathApiRoutes;

    public $pathController;
    public $pathRoutes;

    /* Model Names */
    public $mName;
    public $mPlural;
    public $mCamel;
    public $mCamelPlural;
    public $mSnake;
    public $mSnakePlural;
    public $mDashed;
    public $mDashedPlural;
    public $mSlash;
    public $mSlashPlural;
    public $mHuman;
    public $mHumanPlural;

    public $connection = '';

    /* Generator Options */
    public $options;

    /* Prefixes */
    public $prefixes;

    /** @var CommandData */
    private $commandData;

    /* Command Options */
    public static $availableOptions = [
        'fieldsFile',
        'tableName',
        'fromTable',
        'ignoreFields',
        'save',
        'primary',
        'prefix',
        'paginate',
        'skip',
        'datatables',
        'relations',
        'plural',
        'connection',
    ];

    public $tableName;

    /** @var string */
    protected $primaryName;

    /* Generator AddOns */
    public $addOns;

    public function init(CommandData &$commandData, $options = null)
    {
        if (!empty($options)) {
            self::$availableOptions = $options;
        }
        if (strpos($commandData->modelName,'/') !== false){
            $this->mName = explode('/',$commandData->modelName)[1];
        } else {
            $this->mName = $commandData->modelName;
        }


        $this->prepareAddOns();
        $this->prepareOptions($commandData);
        $this->prepareModelNames();
        $this->preparePrefixes();
        $this->loadPaths();
        $this->prepareTableName();
        $this->preparePrimaryName();
        $this->loadNamespaces($commandData);
        $commandData = $this->loadDynamicVariables($commandData);
        $this->commandData = &$commandData;

    }

    public function loadNamespaces(CommandData &$commandData)
    {
        $prefix = $this->prefixes['ns'];

        if (!empty($prefix)) {
            $prefix = '\\'.$prefix;
        }

        $this->nsApp = $commandData->commandObj->getLaravel()->getNamespace();
        $this->nsApp = substr($this->nsApp, 0, strlen($this->nsApp) - 1);

        if (strpos($commandData->modelName,'/') !== false){
            $this->nsModel = config('lumen_generator.namespace.model', 'App\Models').'\\'.explode('/',$commandData->modelName)[0];
        } else {
            $this->nsModel = config('lumen_generator.namespace.model', 'App\Models').$prefix;
        }


        $this->nsDataTables = config('lumen_generator.namespace.datatables', 'App\DataTables').$prefix;
        $this->nsModelExtend = config(
            'lumen_generator.model_extend_class',
            'Illuminate\Database\Eloquent\Model'
        );

        if (strpos($commandData->modelName,'/') !== false){
            $this->nsAdminService = config('lumen_generator.namespace.admin_service', 'App\Services\Admin').'\\'.explode('/',$commandData->modelName)[0];
        } else {
            $this->nsAdminService = config('lumen_generator.namespace.admin_service','App\Services\Admin').$prefix;
        }

        if (strpos($commandData->modelName,'/') !== false){
            $this->nsAdminController = config('lumen_generator.namespace.admin_controller', 'App\Http\Controllers\Admin').'\\'.explode('/',$commandData->modelName)[0];
        } else {
            $this->nsAdminController = config('lumen_generator.namespace.admin_controller','App\Http\Controllers\Admin').$prefix;
        }

        if (strpos($commandData->modelName,'/') !== false){
            $this->nsApiService = config('lumen_generator.namespace.api_service', 'App\Services\Api').'\\'.explode('/',$commandData->modelName)[0];
        } else {
            $this->nsApiService = config('lumen_generator.namespace.api_service','App\Services\Api').$prefix;
        }

        if (strpos($commandData->modelName,'/') !== false){
            $this->nsApiController = config('lumen_generator.namespace.api_controller', 'App\Http\Controllers\API').'\\'.explode('/',$commandData->modelName)[0];
        } else {
            $this->nsApiController = config('lumen_generator.namespace.api_controller','App\Http\Controllers\API').$prefix;
        }

        $this->nsBaseController = config('lumen_generator.namespace.controller', 'App\Http\Controllers');
        $this->nsController = config('lumen_generator.namespace.controller', 'App\Http\Controllers').$prefix;

    }

    public function loadPaths()
    {
        $prefix = $this->prefixes['path'];

        if (!empty($prefix)) {
            $prefix .= '/';
        }

        $this->pathModel = config('lumen_generator.path.model', app()->basePath('Models/')).$prefix;

        if (config('lumen_generator.ignore_model_prefix', false)) {
            $this->pathModel = config('lumen_generator.path.model', app()->path('Models/'));
        }

        $this->pathDataTables = config('lumen_generator.path.datatables', app()->path('DataTables/')).$prefix;

        $this->pathApiService = config('lumen_generator.path.api_service',app()->basePath('Services/')).$prefix;
        $this->pathApiController = config('lumen_generator.path.api_controller', app()->basePath('Http/Controllers/API/')).$prefix;
        $this->pathApiRoutes = config('lumen_generator.path.api_routes', base_path('routes/api.php'));

        $this->pathAdminService = config('lumen_generator.path.admin_service',app()->basePath('Services/')).$prefix;
        $this->pathAdminController = config('lumen_generator.path.admin_controller', app()->basePath('Http/Controllers/Admin/')).$prefix;
        $this->pathAdminRoutes = config('lumen_generator.path.admin_routes', base_path('routes/admin.php'));

        $this->pathController = config('lumen_generator.path.controller', app()->basePath('Http/Controllers/')).$prefix;
        $this->pathRoutes = config('lumen_generator.path.routes', base_path('routes/web.php'));
    }

    public function loadDynamicVariables(CommandData &$commandData)
    {
        $commandData->addDynamicVariable('$NAMESPACE_APP$', $this->nsApp);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL$', $this->nsModel);
        $commandData->addDynamicVariable('$NAMESPACE_DATATABLES$', $this->nsDataTables);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL_EXTEND$', $this->nsModelExtend);

        $commandData->addDynamicVariable('$NAMESPACE_API_SERVICE$', $this->nsApiService);
        $commandData->addDynamicVariable('$NAMESPACE_API_CONTROLLER$', $this->nsApiController);

        $commandData->addDynamicVariable('$NAMESPACE_ADMIN_SERVICE$', $this->nsAdminService);
        $commandData->addDynamicVariable('$NAMESPACE_ADMIN_CONTROLLER$', $this->nsAdminController);

        $commandData->addDynamicVariable('$NAMESPACE_BASE_CONTROLLER$', $this->nsBaseController);
        $commandData->addDynamicVariable('$NAMESPACE_CONTROLLER$', $this->nsController);

        $commandData->addDynamicVariable('$TABLE_NAME$', $this->tableName);
        $commandData->addDynamicVariable('$TABLE_NAME_TITLE$', Str::studly($this->tableName));
        $commandData->addDynamicVariable('$PRIMARY_KEY_NAME$', $this->primaryName);

        $commandData->addDynamicVariable('$MODEL_NAME$', $this->mName);
        $commandData->addDynamicVariable('$MODEL_NAME_CAMEL$', $this->mCamel);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL$', $this->mPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_CAMEL$', $this->mCamelPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_SNAKE$', $this->mSnake);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_SNAKE$', $this->mSnakePlural);
        $commandData->addDynamicVariable('$MODEL_NAME_DASHED$', $this->mDashed);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_DASHED$', $this->mDashedPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_SLASH$', $this->mSlash);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_SLASH$', $this->mSlashPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_HUMAN$', $this->mHuman);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_HUMAN$', $this->mHumanPlural);
        $commandData->addDynamicVariable('$FILES$', '');

        $connectionText = '';
        if ($connection = $this->getOption('connection')) {
            $this->connection = $connection;
            $connectionText = stru_tab(4).'public $connection = "'.$connection.'";';
        }
        $commandData->addDynamicVariable('$CONNECTION$', $connectionText);

        if (!empty($this->prefixes['route'])) {
            $commandData->addDynamicVariable('$ROUTE_NAMED_PREFIX$', $this->prefixes['route'].'.');
            $commandData->addDynamicVariable('$ROUTE_PREFIX$', str_replace('.', '/', $this->prefixes['route']).'/');
            $commandData->addDynamicVariable('$RAW_ROUTE_PREFIX$', $this->prefixes['route']);
        } else {
            $commandData->addDynamicVariable('$ROUTE_PREFIX$', '');
            $commandData->addDynamicVariable('$ROUTE_NAMED_PREFIX$', '');
        }

        if (!empty($this->prefixes['ns'])) {
            $commandData->addDynamicVariable('$PATH_PREFIX$', $this->prefixes['ns'].'\\');
        } else {
            $commandData->addDynamicVariable('$PATH_PREFIX$', '');
        }

        if (!empty($this->prefixes['view'])) {
            $commandData->addDynamicVariable('$VIEW_PREFIX$', str_replace('/', '.', $this->prefixes['view']).'.');
        } else {
            $commandData->addDynamicVariable('$VIEW_PREFIX$', '');
        }

        if (!empty($this->prefixes['public'])) {
            $commandData->addDynamicVariable('$PUBLIC_PREFIX$', $this->prefixes['public']);
        } else {
            $commandData->addDynamicVariable('$PUBLIC_PREFIX$', '');
        }

        $commandData->addDynamicVariable(
            '$API_PREFIX$',
            config('lumen_generator.api_prefix', 'api')
        );

        $commandData->addDynamicVariable(
            '$API_VERSION$',
            config('lumen_generator.api_version', 'v1')
        );

        $commandData->addDynamicVariable('$SEARCHABLE$', '');

        return $commandData;
    }

    public function prepareTableName()
    {
        if ($this->getOption('tableName')) {
            $this->tableName = $this->getOption('tableName');
        } else {
            $this->tableName = $this->mSnakePlural;
        }
    }

    public function preparePrimaryName()
    {
        if ($this->getOption('primary')) {
            $this->primaryName = $this->getOption('primary');
        } else {
            $this->primaryName = 'id';
        }
    }

    public function prepareModelNames()
    {
        if ($this->getOption('plural')) {
            $this->mPlural = $this->getOption('plural');
        } else {
            $this->mPlural = Str::plural($this->mName);
        }

        $this->mCamel = Str::camel($this->mName);
        $this->mCamelPlural = Str::camel($this->mPlural);
        $this->mSnake = Str::snake($this->mName);
        $this->mSnakePlural = Str::snake($this->mPlural);
        $this->mDashed = str_replace('_', '-', Str::snake($this->mSnake));
        $this->mDashedPlural = str_replace('_', '-', Str::snake($this->mSnakePlural));
        $this->mSlash = str_replace('_', '/', Str::snake($this->mSnake));
        $this->mSlashPlural = str_replace('_', '/', Str::snake($this->mSnakePlural));
        $this->mHuman = Str::title(str_replace('_', ' ', Str::snake($this->mSnake)));
        $this->mHumanPlural = Str::title(str_replace('_', ' ', Str::snake($this->mSnakePlural)));
    }

    public function prepareOptions(CommandData &$commandData)
    {
        foreach (self::$availableOptions as $option) {
            $this->options[$option] = $commandData->commandObj->option($option);
        }

        if (isset($options['fromTable']) and $this->options['fromTable']) {
            if (!$this->options['tableName']) {
                $commandData->commandError('tableName required with fromTable option.');
                exit;
            }
        }

        if (empty($this->options['save'])) {
            $this->options['save'] = config('lumen_generator.options.save_schema_file', true);
        }

        if (empty($this->options['localized'])) {
            $this->options['localized'] = config('lumen_generator.options.localized', false);
        }

        if ($this->options['localized']) {
            $commandData->getTemplatesManager()->setUseLocale(true);
        }

        $this->options['softDelete'] = config('lumen_generator.options.softDelete', false);
        $this->options['repositoryPattern'] = config('lumen_generator.options.repository_pattern', true);
        $this->options['resources'] = config('lumen_generator.options.resources', true);
        if (!empty($this->options['skip'])) {
            $this->options['skip'] = array_map('trim', explode(',', $this->options['skip']));
        }

        if (!empty($this->options['datatables'])) {
            if (strtolower($this->options['datatables']) == 'true') {
                $this->addOns['datatables'] = true;
            } else {
                $this->addOns['datatables'] = false;
            }
        }
    }

    public function preparePrefixes()
    {
        $this->prefixes['route'] = explode('/', config('lumen_generator.prefixes.route', ''));
        $this->prefixes['path'] = explode('/', config('lumen_generator.prefixes.path', ''));
        $this->prefixes['view'] = explode('.', config('lumen_generator.prefixes.view', ''));
        $this->prefixes['public'] = explode('/', config('lumen_generator.prefixes.public', ''));

        if ($this->getOption('prefix')) {
            $multiplePrefixes = explode('/', $this->getOption('prefix'));

            $this->prefixes['route'] = array_merge($this->prefixes['route'], $multiplePrefixes);
            $this->prefixes['path'] = array_merge($this->prefixes['path'], $multiplePrefixes);
            $this->prefixes['view'] = array_merge($this->prefixes['view'], $multiplePrefixes);
            $this->prefixes['public'] = array_merge($this->prefixes['public'], $multiplePrefixes);
        }

        $this->prefixes['route'] = array_diff($this->prefixes['route'], ['']);
        $this->prefixes['path'] = array_diff($this->prefixes['path'], ['']);
        $this->prefixes['view'] = array_diff($this->prefixes['view'], ['']);
        $this->prefixes['public'] = array_diff($this->prefixes['public'], ['']);

        $routePrefix = '';

        foreach ($this->prefixes['route'] as $singlePrefix) {
            $routePrefix .= Str::camel($singlePrefix).'.';
        }

        if (!empty($routePrefix)) {
            $routePrefix = substr($routePrefix, 0, strlen($routePrefix) - 1);
        }

        $this->prefixes['route'] = $routePrefix;

        $nsPrefix = '';

        foreach ($this->prefixes['path'] as $singlePrefix) {
            $nsPrefix .= Str::title($singlePrefix).'\\';
        }

        if (!empty($nsPrefix)) {
            $nsPrefix = substr($nsPrefix, 0, strlen($nsPrefix) - 1);
        }

        $this->prefixes['ns'] = $nsPrefix;

        $pathPrefix = '';

        foreach ($this->prefixes['path'] as $singlePrefix) {
            $pathPrefix .= Str::title($singlePrefix).'/';
        }

        if (!empty($pathPrefix)) {
            $pathPrefix = substr($pathPrefix, 0, strlen($pathPrefix) - 1);
        }

        $this->prefixes['path'] = $pathPrefix;

        $viewPrefix = '';

        foreach ($this->prefixes['view'] as $singlePrefix) {
            $viewPrefix .= Str::camel($singlePrefix).'/';
        }

        if (!empty($viewPrefix)) {
            $viewPrefix = substr($viewPrefix, 0, strlen($viewPrefix) - 1);
        }

        $this->prefixes['view'] = $viewPrefix;

        $publicPrefix = '';

        foreach ($this->prefixes['public'] as $singlePrefix) {
            $publicPrefix .= Str::camel($singlePrefix).'/';
        }

        if (!empty($publicPrefix)) {
            $publicPrefix = substr($publicPrefix, 0, strlen($publicPrefix) - 1);
        }

        $this->prefixes['public'] = $publicPrefix;
    }

    public function overrideOptionsFromJsonFile($jsonData)
    {
        $options = self::$availableOptions;

        foreach ($options as $option) {
            if (isset($jsonData['options'][$option])) {
                $this->setOption($option, $jsonData['options'][$option]);
            }
        }

        // prepare prefixes than reload namespaces, paths and dynamic variables
        if (!empty($this->getOption('prefix'))) {
            $this->preparePrefixes();
            $this->loadPaths();
            $this->loadNamespaces($this->commandData);
            $this->loadDynamicVariables($this->commandData);
        }

        $addOns = ['swagger', 'tests', 'datatables'];

        foreach ($addOns as $addOn) {
            if (isset($jsonData['addOns'][$addOn])) {
                $this->addOns[$addOn] = $jsonData['addOns'][$addOn];
            }
        }
    }

    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        return false;
    }

    public function getAddOn($addOn)
    {
        if (isset($this->addOns[$addOn])) {
            return $this->addOns[$addOn];
        }

        return false;
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    public function prepareAddOns()
    {
        $this->addOns['swagger'] = config('lumen_generator.add_on.swagger', false);
        $this->addOns['tests'] = config('lumen_generator.add_on.tests', false);
        $this->addOns['datatables'] = config('lumen_generator.add_on.datatables', false);
        $this->addOns['menu.enabled'] = config('lumen_generator.add_on.menu.enabled', false);
        $this->addOns['menu.menu_file'] = config('lumen_generator.add_on.menu.menu_file', 'layouts.menu');
    }
}
