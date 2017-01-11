<?php

namespace QbiPlugins\Commands;

use Qbi\Tool;

class Played
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addCommand(
            'Played',
            ['played'],
            "Show how long you've played.",
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                $json = $pluginManager->getFile()->getContent('storage/known_players.json');
                $players = json_decode($json, true);

                $playedSession = time() - $players[$line->getPlayerName()]['startPlaying'];

                $timePlayed        = Tool::niceDiffFromSeconds(
                    $players[$line->getPlayerName()]['timePlayed'] + $playedSession
                );
                $timePlayedSession = Tool::niceDiffFromSeconds(
                    $playedSession
                );

                $pluginManager->getCommunicator()->say(
                    "You have played for {$timePlayedSession} this session and {$timePlayed} in total."
                );
            }
        );
    }
}
