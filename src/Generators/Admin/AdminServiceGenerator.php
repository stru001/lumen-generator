<?php

namespace Stru\LumenGenerator\Generators\Admin;

use Stru\LumenGenerator\Common\CommandData;
use Stru\LumenGenerator\Generators\BaseGenerator;
use Stru\LumenGenerator\Utils\FileUtil;

class AdminServiceGenerator extends BaseGenerator
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
            $this->path = $commandData->config->pathAdminService.$path_fileName[0].'/';
            $this->fileName = $path_fileName[1].'AdminService.php';
        } else {
            $this->path = $commandData->config->pathAdminService;
            $this->fileName = $this->commandData->modelName.'AdminService.php';
        }

    }

    public function generate()
    {
        if ($this->commandData->getOption('repositoryPattern')) {
            $templateName = 'admin_service';
        } else {
            $templateName = 'model_dmin_service';
        }

        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }

        $templateData = get_template("admin.service.$templateName", 'lumen-generator');

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = $this->fillDocs($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nAdmin Service created: ");
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
