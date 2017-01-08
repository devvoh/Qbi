<?php

namespace Qbi\Plugins\Commands;

class Spawn extends \Qbi\Plugins\Base
{
    public function init()
    {
        $this->setKeyword('s');
        $this->setHelp('Teleport back to spawn, for a soothing bath.');

        $action = function(string $event, \Qbi\Parser\Line $line) {
            $this->communicator->tp($line->getPlayerName(), -350, 64, 214);
            $this->communicator->say($line->getPlayerName() . ' panicked and has been teleported to spawn!');
        };

        $this->setAction($action);
    }
}
