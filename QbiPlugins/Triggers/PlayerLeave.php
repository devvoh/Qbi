<?php

namespace QbiPlugins\Triggers;

class PlayerLeave
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addTrigger(
            ['left the game'],
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $pluginManager->getCommunicator()->say(
                    "And with a heavy heart we say goodbye to {$line->getPlayerName()}..."
                );
            }
        );
    }

}
