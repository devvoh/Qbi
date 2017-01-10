<?php

namespace Qbi;

use Qbi\Console\Output;
use Qbi\Parser\Line;

class PluginManager
{
    protected $config;
    protected $communicator;
    protected $output;
    protected $hook;

    protected $commands = [];
    protected $triggers = [];
    protected $tasks    = [];

    public function __construct(
        Config $config,
        Communicator $communicator,
        Output $output,
        Hook $hook
    ) {
        $this->config       = $config;
        $this->communicator = $communicator;
        $this->output       = $output;
        $this->hook         = $hook;
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
        $plugins = $this->config->get('plugins.' . strtolower($pluginType));
        if (!$plugins || count($plugins) === 0) {
            $this->output->write("0 loaded");
            $this->output->newline();

            return;
        }

        $count = 0;
        foreach ($plugins as $pluginName) {
            $className = "\\QbiPlugins\\{$pluginType}\\{$pluginName}";
            $class = DI::get($className);
            $class->init($this);
            $count++;
        }

        $this->output->write("{$count} loaded");
        $this->output->newline();
    }

    public function getCommands() : array
    {
        return $this->commands;
    }

    /**
     * Commands are player chat-triggered. Application::QBI_COMMAND [keyword] will activate.
     *
     * @param string[] $keywords
     * @param string   $description
     * @param callable $callable
     */
    public function addCommand(array $keywords, string $description, callable $callable)
    {
        $this->commands[] = [
            'keywords'    => $keywords,
            'description' => $description,
            'callable'    => $callable,
        ];
    }

    /**
     * Triggers can't be activated by players, but only by non-player chat strings in the log.
     *
     * @param string[] $triggerStrings
     * @param callable $callable
     */
    public function addTrigger(array $triggerStrings, callable $callable)
    {
        $this->triggers[] = [
            'triggerStrings' => $triggerStrings,
            'callable'       => $callable,
        ];
    }

    /**
     * Tasks are triggered simply by intervalSeconds elapsing since intervalStart, and will run for runCount times.
     * A runTimes value of 0 (default) means indefinitely.
     *
     * Some Commands or Triggers might add a Task to the PluginManager to delay an action without blocking Qbi from
     * handling other events.
     *
     * @param \DateTime $intervalStart
     * @param int       $intervalSeconds
     * @param callable  $callable
     * @param int       $runTimes
     */
    public function addTask(\DateTime $intervalStart, int $intervalSeconds, callable $callable, $runTimes = 0)
    {
        $this->tasks[] = [
            'intervalStart'   => $intervalStart->getTimestamp(),
            'intervalSeconds' => $intervalSeconds,
            'callable'        => $callable,
            'runTimes'        => $runTimes,
            'runCount'        => 0,
            'lastRan'         => $intervalStart->getTimestamp(),
        ];
    }

    public function handleLineAsCommand(Line $line) : bool
    {
        foreach ($this->commands as $command) {
            if ($this->isMatchingCommand($command, $line)) {
                $callable = $command['callable'];
                $callable($line);
                return true;
            }
        }
        return false;
    }

    public function isMatchingCommand(array $command, Line $line) : bool
    {
        return in_array($line->getCommandString(), $command['keywords']);
    }

    public function handleLineAsTrigger(Line $line) : bool
    {
        foreach ($this->triggers as $trigger) {
            if ($this->isMatchingTrigger($trigger, $line)) {
                $callable = $trigger['callable'];
                $callable($line);
                return true;
            }
        }
        return false;
    }

    public function isMatchingTrigger(array $trigger, Line $line) : bool
    {
        foreach ($trigger['triggerStrings'] as $triggerString) {
            $partStart = substr($line->getString(), 0, strlen($triggerString));
            $partEnd   = substr($line->getString(), -strlen($triggerString));

            if ($partStart === $triggerString || $partEnd === $triggerString) {
                return true;
            }
        }
        return false;
    }

    public function getOutput() : Output
    {
        return $this->output;
    }

    public function getCommunicator() : Communicator
    {
        return $this->communicator;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }

    public function runTasks() {
        if (count($this->tasks) == 0) {
            return false;
        }

        $now_timestamp = time();
        foreach ($this->tasks as $key => $task) {
            $diff_in_seconds = $now_timestamp - $task['lastRan'];
            if ($task['lastRan'] !== $now_timestamp
                && $diff_in_seconds % $task['intervalSeconds'] === 0
            ) {
                $callable = $task['callable'];
                $callable();

                $this->tasks[$key]['lastRan'] = $now_timestamp;

                $task['runCount']++;
                if ($task['runTimes'] !== 0 && $task['runCount'] >= $task['runTimes']) {
                    echo 'UNSET';
                    unset($this->tasks[$key]);
                }
            }
        }
        return true;
    }

}
