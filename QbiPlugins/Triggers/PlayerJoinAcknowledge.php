<?php

namespace QbiPlugins\Triggers;

class PlayerJoinAcknowledge
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addTrigger(
            'PlayerJoinAcknowledge',
            ['joined the game'],
            function(\Qbi\Parser\Line $line) use ($pluginManager) {
                // We want to make sure the login process is finished, so we add a task to complete in 5 seconds time.
                $pluginManager->addTask(
                    'PlayerJoinAcknowledgeDelayed',
                    new \DateTime(), // means now
                    5, // We wait this amount in seconds before we start
                    function() use ($pluginManager, $line) {
                        // Attempt to get from the config, otherwise use the default values
                        $title    = "Hi, {playerName}!";
                        $subtitle = "Type 'q help' for commands.";
                        $color    = "gold";

                        $configTitle = $pluginManager->getConfig()->get('pluginSettings.triggers.PlayerJoinAcknowledge.title');
                        if ($configTitle) {
                            $title = $configTitle;
                            $title = str_replace('{playerName}', $line->getPlayerName(), $title);
                        }
                        $configSubtitle = $pluginManager->getConfig()->get('pluginSettings.triggers.PlayerJoinAcknowledge.subtitle');
                        if ($configSubtitle) {
                            $subtitle = $configSubtitle;
                            $subtitle = str_replace('{playerName}', $line->getPlayerName(), $subtitle);
                        }
                        $configColor = $pluginManager->getConfig()->get('pluginSettings.triggers.PlayerJoinAcknowledge.color');
                        if ($configColor) {
                            $color = $configColor;
                        }

                        $pluginManager->getCommunicator()->title(
                            $line->getPlayerName(),
                            $title,
                            "title",
                            $color
                        );
                        $pluginManager->getCommunicator()->title(
                            $line->getPlayerName(),
                            $subtitle,
                            "subtitle"
                        );
                    },
                    1 // run only once
                );

            }
        );
    }
}
