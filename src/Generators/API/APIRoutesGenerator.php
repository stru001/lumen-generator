<?php

namespace Stru\LumenGenerator\Generators\API;

use Illuminate\Support\Str;
use Stru\LumenGenerator\Common\CommandData;
use Stru\LumenGenerator\Generators\BaseGenerator;
use Stru\LumenGenerator\Utils\FileUtil;

class APIRoutesGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $routeContents;

    /** @var string */
    private $routesTemplate;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathApiRoutes;

        $this->routeContents = file_get_contents($this->path);

        if (!empty($this->commandData->config->prefixes['route'])) {
            $routesTemplate = get_template('api.routes.prefix_routes', 'lumen-generator');
        } else {
            $routesTemplate = get_template('api.routes.routes', 'lumen-generator');
        }

        $this->routesTemplate = fill_template($this->commandData->dynamicVars, $routesTemplate);
    }

    public function generate()
    {
        $this->routeContents .= "\n\n".$this->routesTemplate;

        $existingRouteContents = file_get_contents($this->path);

        if (strpos($this->commandData->modelName,'/')){
            $path_file = explode('/',$this->commandData->modelName);
            $model_name = strtolower($path_file[1]);
        } else {
            $model_name = strtolower($this->commandData->modelName);
        }

        if (Str::contains($existingRouteContents, '$router->group([\'prefix\' => \''.$model_name.'\']')) {
            $this->commandData->commandObj->info('api router '.$model_name.' is already exists, Skipping Adjustment.');

            return;
        }

        file_put_contents($this->path, $this->routeContents);

        $this->commandData->commandComment("\n".' api routes added.');
    }

    public function rollback()
    {
        if (Str::contains($this->routeContents, $this->routesTemplate)) {
            $this->routeContents = str_replace($this->routesTemplate, '', $this->routeContents);
            file_put_contents($this->path, $this->routeContents);
            $this->commandData->commandComment('api routes deleted');
        }
    }
}
