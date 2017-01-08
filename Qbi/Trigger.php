<?php

namespace Qbi;

use Qbi\Parser\Line;

class Trigger
{
    protected $config;
    protected $hook;

    /** @var \Qbi\Plugins\Base[] */
    protected $triggers = [];

    public function __construct(
        Config $config,
        Hook   $hook
    ) {
        $this->config = $config;
        $this->hook = $hook;
    }

    public function init() : int
    {
        $triggers = $this->config->get('triggers');
        if (!$triggers) {
            return 0;
        }

        foreach ($triggers as $triggerString) {
            $className = "\\Qbi\\Plugins\\Triggers\\{$triggerString}";
            $class = DI::get($className);
            $class->init();

            $this->triggers[] = $class;
        }

        return count($this->triggers);
    }

    public function handleLine(Line $line)
    {
        foreach ($this->triggers as $trigger) {
            $this->hook->trigger('qbi_plugin_triggers_' . $trigger->getKeyword(), $line);
        }
    }
}
