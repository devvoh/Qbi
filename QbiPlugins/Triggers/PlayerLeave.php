<?php

namespace QbiPlugins\Triggers;

class PlayerLeave extends \Qbi\Plugins\Base
{
    public function init()
    {
        $this->setKeyword('playerleave');
        $this->setMatchString('left the game');

        $action = function(string $event, \Qbi\Parser\Line $line) {
            if (!$this->matchesWithString($line->getString())) {
                return;
            }

            $this->communicator->say("Bye bye to {$line->getPlayerName()}!");
        };

        $this->setAction($action);
    }

}
