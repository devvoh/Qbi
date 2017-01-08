<?php

namespace Qbi\Plugins\Commands;

class Uptime extends \Qbi\Plugins\Base
{
    public function init()
    {
        $this->setKeyword('uptime');
        $this->setHelp('Show how long the server has been running.');

        $action = function(string $event, \Qbi\Parser\Line $line) {
            $now = time();
            $startedTime = filemtime('server.status');

            $string = \Qbi\Tool::niceDiffFromTimestamp($startedTime, $now);
            $this->communicator->say("The server has been running for {$string}.");
        };

        $this->setAction($action);
    }
}
