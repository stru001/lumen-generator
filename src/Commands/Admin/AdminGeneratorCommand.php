<?php

namespace Stru\LumenGenerator\Commands\Admin;

use Stru\LumenGenerator\Commands\BaseCommand;
use Stru\LumenGenerator\Common\CommandData;

class AdminGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'stru:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full CRUD Admin for given model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_ADMIN);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $this->generateCommonItems();

        $this->generateAdminItems();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments(), []);
    }
}
