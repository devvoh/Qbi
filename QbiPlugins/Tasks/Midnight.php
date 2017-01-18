<?php

namespace QbiPlugins\Tasks;

class Midnight
{
    public function init(\Qbi\PluginManager $pluginManager)
    {
        $pluginManager->addTask(
            'Midnight',
            new \DateTime('yesterday 00:00:00'), // Time midnight, and yesterday so it'll run on first check.
            \Qbi\Tool::secondsFromDays(1), // Run every day.
            function () use ($pluginManager) {
                $pluginManager->getCommunicator()->say(
                    "Its midnight! Might want to consider going to bed!"
                );
            }
        );
    }
}
