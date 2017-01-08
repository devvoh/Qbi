<?php

namespace Qbi;

class System
{
    protected $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function run(string $command) : array
    {
        exec($command, $return, $exitCode);

        return [
            'return'   => $return,
            'exitCode' => $exitCode
        ];
    }

    public function runOnScreen(string $command) : array
    {
        $screenName = $this->config->get('screen.name');
        $command = "screen -S {$screenName} -X stuff '{$command}\\r'";
        return $this->run($command);
    }
}
