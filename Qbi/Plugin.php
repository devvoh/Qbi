<?php

namespace Qbi;

use Qbi\Console\Output;
use Qbi\Parser\Line;

class Plugin
{
    protected $config;
    protected $output;
    protected $hook;

    /** @var \Qbi\Plugins\Base[][] */
    protected $plugins = [
        'Commands' => [],
        'Triggers' => [],
        'Tasks'    => [],
    ];

    /** @var \Qbi\Plugins\Base[] */
    protected $triggers = [];

    /** @var \Qbi\Plugins\Base[] */
    protected $tasks = [];

    public function __construct(
        Config $config,
        Output $output,
        Hook   $hook
    ) {
        $this->config = $config;
        $this->output = $output;
        $this->hook   = $hook;
    }

    public function init()
    {
        $this->loadPlugins('Commands');
        $this->loadPlugins('Triggers');
        $this->loadPlugins('Tasks');
    }

    public function loadPlugins($pluginType)
    {
        $this->output->writeDateIfEnabled();

        $pluginTypeString = str_pad($pluginType, 10, ".");

        $this->output->write("- {$pluginTypeString}...");
        $plugins = $this->config->get(strtolower($pluginType));
        if (!$plugins || count($plugins) === 0) {
            $this->output->write("0 loaded");
            $this->output->newline();
            return;
        }

        foreach ($plugins as $pluginName) {
            $className = "\\QbiPlugins\\{$pluginType}\\{$pluginName}";
            $class = DI::get($className);
            $class->init();

            $this->plugins[$pluginType][] = $class;
        }

        $this->output->write(count($this->plugins[$pluginType]) . " loaded");
        $this->output->newline();
    }

    public function getCommandsHelp()
    {
        $help = [];
        foreach ($this->plugins['Commands'] as $command) {
            $help[$command->getKeyword()] = $command->getHelp();
        }
        return $help;
    }

    public function handleLineAsCommand(Line $line)
    {
        $this->hook->trigger('qbi_plugin_commands_' . $line->getCommandString(), $line);
    }

    public function handleLineAsTrigger(Line $line)
    {
        foreach ($this->plugins['Triggers'] as $plugin) {
            $this->hook->trigger('qbi_plugin_triggers_' . $plugin->getKeyword(), $line);
        }
    }

    public function runTasks()
    {
        foreach ($this->plugins['Tasks'] as $plugin) {
            $this->hook->trigger('qbi_plugin_tasks_' . $plugin->getKeyword());
        }
    }
}
