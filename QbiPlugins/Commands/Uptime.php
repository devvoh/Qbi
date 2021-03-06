<?php

namespace QbiPlugins\Commands;

class Uptime
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addCommand(
            'Uptime',
            ['uptime', 'up'],
            'Show how long the server has been running.',
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $now = time();
                $startedTime = filemtime('storage/server.status');

                $string = \Qbi\Tool::niceDiffFromTimestamp($startedTime, $now);
                $pluginManager->getCommunicator()->say("The server has been running for {$string}.");
            }
        );
    }
}
