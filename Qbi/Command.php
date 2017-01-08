<?php

namespace Qbi;

use Qbi\Parser\Line;

class Command
{
    protected $config;
    protected $hook;

    /** @var \Qbi\Plugins\Base[] */
    protected $commands = [];

    public function __construct(
        Config $config,
        Hook   $hook
    ) {
        $this->config = $config;
        $this->hook = $hook;
    }

    public function init() : int
    {
        $commands = $this->config->get('commands');
        if (!$commands) {
            return 0;
        }

        foreach ($commands as $commandString) {
            $className = "\\Qbi\\Plugins\\Commands\\{$commandString}";
            $class = DI::get($className);
            $class->init();

            $this->commands[] = $class;
        }

        return count($this->commands);
    }

    public function getCommandsHelp()
    {
        $help = [];
        foreach ($this->commands as $command) {
            $help[$command->getKeyword()] = $command->getHelp();
        }
        return $help;
    }

    public function handleLine(Line $line) : int
    {
        return $this->hook->trigger('qbi_plugin_commands_' . $line->getCommandString(), $line);
    }
}
