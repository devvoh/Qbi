<?php

namespace Qbi\Plugins\Triggers;

class PlayerJoin extends \Qbi\Plugins\Base
{
    public function init()
    {
        $this->setKeyword('playerjoin');
        $this->setMatchString('joined the game');

        $action = function(string $event, \Qbi\Parser\Line $line) {
            if (!$this->matchesWithString($line->getString())) {
                return;
            }

            $this->communicator->say("Welcome to {$line->getPlayerName()}!");
        };

        $this->setAction($action);
    }
}
