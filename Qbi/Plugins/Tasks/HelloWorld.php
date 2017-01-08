<?php

namespace Qbi\Plugins\Tasks;

class HelloWorld extends \Qbi\Plugins\Base
{
    public function init()
    {
        $this->setKeyword('hello');
        $this->setHelp('Says hello ever 10 seconds!');
        $this->setIntervalInSeconds(10);

        $action = function(string $event) {
            if ($this->checkInterval()) {
                $this->communicator->say("Hello, world!");
            }
        };

        $this->setAction($action);
    }

}
