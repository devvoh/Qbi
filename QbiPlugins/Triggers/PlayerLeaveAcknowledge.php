<?php

namespace QbiPlugins\Triggers;

class PlayerLeaveAcknowledge
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addTrigger(
            'PlayerLeaveAcknowledge',
            ['left the game'],
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $pluginManager->getCommunicator()->say(
                    "And with a heavy heart we say goodbye to {$line->getPlayerName()}..."
                );
            }
        );
    }

}
