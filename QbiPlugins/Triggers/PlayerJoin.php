<?php

namespace QbiPlugins\Triggers;

class PlayerJoin
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addTrigger(
            ['joined the game'],
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                // We want to make sure the login process is finished, so we add a task to complete in 5 seconds time.
                $pluginManager->addTask(
                    new \DateTime(), // means now
                    5, // We wait this amount in seconds before we start
                    function() use ($pluginManager, $line) {
                        $pluginManager->getCommunicator()->say(
                            "Welcome to {$line->getPlayerName()}!"
                        );
                    },
                    1 // run only once
                );

            }
        );
    }
}
