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

                $arguments = $line->getCommandArguments();
                if (!empty($arguments[0])) {
                    $playerName = $arguments[0];
                } else {
                    $playerName = $line->getPlayerName();
                }

                if (!isset($players[$playerName])) {
                    $pluginManager->getCommunicator()->say(
                        "I have no record of a player called `{$playerName}`."
                    );
                    return;
                }

                // If 'startPlaying' exists as a key, the player is supposedly currently online.
                if (isset($players[$playerName]['startPlaying'])) {
                    $playedSession = time() - $players[$playerName]['startPlaying'];

                    $timePlayed        = Tool::niceDiffFromSeconds(
                        $players[$playerName]['timePlayed'] + $playedSession
                    );
                    $timePlayedSession = Tool::niceDiffFromSeconds(
                        $playedSession
                    );

                    $pluginManager->getCommunicator()->say(
                        "{$playerName} has played for {$timePlayedSession} this session and {$timePlayed} in total."
                    );
                } else {
                    $timePlayed = Tool::niceDiffFromSeconds($players[$playerName]['timePlayed']);
                    $pluginManager->getCommunicator()->say(
                        "{$playerName} has played for {$timePlayed} in total."
                    );
                }
            },
            ['playerName']
        );
    }
}
