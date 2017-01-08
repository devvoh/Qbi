<?php

/**
 * Borrowed from Parable - https://github.com/devvoh/parable
 */

namespace Qbi;

class Hook
{
    /** @var array */
    protected $hooks = [];

    public function into(string $event, callable $callable) : Hook
    {
        $this->hooks[$event][] = $callable;
        return $this;
    }

    public function trigger(string $event, &$payload = null) : int
    {
        // Disallow calling a trigger on global hooks
        if ($event === '*') {
            return 0;
        }

        // Get all global hooks
        $globalHooks = [];
        if (isset($this->hooks['*']) && count($this->hooks['*']) > 0) {
            $globalHooks = $this->hooks['*'];
        }

        // Check if the event exists and has closures to call
        if (!isset($this->hooks[$event]) || count($this->hooks[$event]) == 0) {
            // There are no specific hooks, but maybe there's global hooks?
            if (count($globalHooks) === 0) {
                // There is nothing to do here
                return 0;
            }
            $hooks = $globalHooks;
        } else {
            $hooks = $this->hooks[$event];
            $hooks = array_merge($hooks, $globalHooks);
        }

        // All good, let's call those closures
        foreach ($hooks as $closure) {
            $closure($event, $payload);
        }
        return count($hooks);
    }
}