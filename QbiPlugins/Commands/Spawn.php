<?php

namespace QbiPlugins\Commands;

class Spawn
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addCommand(
            'Spawn',
            ['spawn', 's'],
            'This will teleport back to spawn.',
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $spawnConfig = $pluginManager->getConfig()->get('pluginSettings.commands.Spawn');

                if (!$spawnConfig || !$spawnConfig['x'] || !$spawnConfig['y'] || !$spawnConfig['z']) {
                    $pluginManager->getCommunicator()->tellRawWithPrefix(
                        $line->getPlayerName(),
                        "Spawn isn't configured properly. Tell the server admin to add the coordinates to Qbi's configuration!"
                    );
                    return;
                }

                $pluginManager->getCommunicator()->tp(
                    $line->getPlayerName(),
                    $spawnConfig['x'],
                    $spawnConfig['y'],
                    $spawnConfig['z']
                );

                $message = $line->getPlayerName() . ' panicked and has been teleported to spawn!';
                if ($spawnConfig['message']) {
                    $message = str_replace('{playerName}', $line->getPlayerName(), $spawnConfig['message']);
                }
                $pluginManager->getCommunicator()->say($message);
            }
        );
    }
}
