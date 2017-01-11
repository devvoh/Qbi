<?php

namespace QbiPlugins\Triggers;

class UpdateKnownPlayers
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addTrigger(
            'UpdateKnownPlayers',
            ['joined the game', 'left the game'],
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $json = $pluginManager->getFile()->getContent('storage/known_players.json');
                $players = json_decode($json, true);

                if (!$players) {
                    $players = [];
                }

                if ($line->isPlayerJoining()) {
                    if (!array_key_exists($line->getPlayerName(), $players)) {
                        $players[$line->getPlayerName()]['timePlayed'] = 0;
                        $players[$line->getPlayerName()]['startPlaying'] = time();
                    } else {
                        $players[$line->getPlayerName()]['startPlaying'] = time();
                    }
                } elseif ($line->isPlayerLeaving()) {
                    $duration = time() - $players[$line->getPlayerName()]['startPlaying'];
                    $players[$line->getPlayerName()]['timePlayed'] += $duration;
                    unset($players[$line->getPlayerName()]['startPlaying']);
                }

                $json = json_encode($players);
                $pluginManager->getFile()->putContent('storage/known_players.json', $json);
            }
        );
    }
}
