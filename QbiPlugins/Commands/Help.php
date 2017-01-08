<?php

namespace QbiPlugins\Commands;

class Help extends \Qbi\Plugins\Base
{
    public function init()
    {
        $this->setKeyword('help');
        $this->setHelp('This screen.');

        $action = function(string $event, \Qbi\Parser\Line $line) {
            $commandsHelp = $this->plugin->getCommandsHelp();

            $maxLength = 36;

            $this->communicator->tellRaw($line->getPlayerName(), "Qbi " . \Qbi\Application::VERSION . " -- Help");
            $this->communicator->tellRaw($line->getPlayerName(), str_repeat('-', $maxLength));
            $this->communicator->tellRaw($line->getPlayerName(), "Qbi is a supervisor. It keeps the server online in case");
            $this->communicator->tellRaw($line->getPlayerName(), "it should crash. It also offers some commands.");
            $this->communicator->tellRaw($line->getPlayerName(), str_repeat('-', $maxLength));

            foreach ($commandsHelp as $command => $help) {
                $this->communicator->tellRaw($line->getPlayerName(), "q {$command}");
            }
        };

        $this->setAction($action);
    }
}
