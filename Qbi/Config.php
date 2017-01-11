<?php

namespace Qbi;

class Config
{
    public function get(string $key) {
        // We always load directly from the file system so config can be changed at will.
        $configJson = file_get_contents('config.json');
        $config = json_decode($configJson, true);

        if (!$config) {
            throw new Exception('No valid config.json found!');
        }

        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
        } else {
            $keys = [$key];
        }

        foreach ($keys as $key) {
            if (isset($config[$key])) {
                $config = $config[$key];
            } else {
                return null;
            }
        }
        return $config;
    }
}
