<?php

namespace Qbi;

class Task
{
    protected $config;
    protected $hook;

    /** @var \Qbi\Plugins\Base[] */
    protected $tasks = [];

    public function __construct(
        Config $config,
        Hook   $hook
    ) {
        $this->config = $config;
        $this->hook   = $hook;
    }

    public function init() : int
    {
        $tasks = $this->config->get('tasks');
        if (!$tasks) {
            return 0;
        }

        foreach ($tasks as $taskString) {
            $className = "\\Qbi\\Plugins\\Tasks\\{$taskString}";
            $class = DI::get($className);
            $class->init();

            $this->tasks[] = $class;
        }

        return count($this->tasks);
    }

    public function start()
    {
        foreach ($this->tasks as $task) {
            $this->hook->trigger('qbi_plugin_tasks_' . $task->getKeyword());
        }
    }
}
