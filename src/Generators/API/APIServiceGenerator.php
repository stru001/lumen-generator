<?php

namespace Stru\LumenGenerator\Generators\API;

use Stru\LumenGenerator\Common\CommandData;
use Stru\LumenGenerator\Generators\BaseGenerator;
use Stru\LumenGenerator\Utils\FileUtil;

class APIServiceGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $fileName;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;

        if (strpos($commandData->modelName,'/') !== false){
            $path_fileName = explode('/',$commandData->modelName);
            $this->path = $commandData->config->pathApiService.$path_fileName[0].'/';
            $this->fileName = $path_fileName[1].'APIService.php';
        } else {
            $this->path = $commandData->config->pathApiService;
            $this->fileName = $this->commandData->modelName.'APIService.php';
        }

    }

    public function generate()
    {
        if ($this->commandData->getOption('repositoryPattern')) {
            $templateName = 'api_service';
        } else {
            $templateName = 'model_api_service';
        }

        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }

        $templateData = get_template("api.service.$templateName", 'lumen-generator');

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = $this->fillDocs($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nAPI Service created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    private function fillDocs($templateData)
    {
        $methods = ['controller', 'index', 'store', 'show', 'update', 'destroy'];

        if ($this->commandData->getAddOn('swagger')) {
            $templatePrefix = 'controller_docs';
            $templateType = 'swagger-generator';
        } else {
            $templatePrefix = 'api.docs.controller';
            $templateType = 'lumen-generator';
        }

        foreach ($methods as $method) {
            $key = '$DOC_'.strtoupper($method).'$';
            $docTemplate = get_template($templatePrefix.'.'.$method, $templateType);
            $docTemplate = fill_template($this->commandData->dynamicVars, $docTemplate);
            $templateData = str_replace($key, $docTemplate, $templateData);
        }

        return $templateData;
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->fileName)) {
            $this->commandData->commandComment('API Controller file deleted: '.$this->fileName);
        }
    }
}
