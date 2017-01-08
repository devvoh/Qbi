<?php

namespace QbiPlugins\Commands;

class HelloWorld extends \Qbi\Plugins\Base
{
    public function init()
    {
        $this->setKeyword('hello');
        $this->setHelp('Says hello!');

        $action = function(string $event, \Qbi\Parser\Line $line) {
            $this->communicator->say("Hello, world!");
        };

        $this->setAction($action);
    }
}
