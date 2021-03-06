<?php 

declare(strict_types=1);

namespace AndersBjorkland\MaintenanceExtension\Service;

use DateTime;

class OpcacheStatusParser
{
    public static function getArray():array
    {
        $opcache = [];
        $opcacheEnabled = false;
        $opcacheVersion = "";
        $opcacheStatus = false;

        if (function_exists('opcache_get_status')) {
            $opcacheStatusTemp = opcache_get_status();
            $opcacheConfigs = opcache_get_configuration();

            if ($opcacheStatusTemp && $opcacheConfigs) {
                $opcacheEnabled = true;
                $opcacheStatus["full"] = $opcacheStatusTemp["cache_full"];
                $opcacheStatus["free_memory"] = $opcacheStatusTemp["memory_usage"]["free_memory"] * 1.25 * (10 ** (-7));

                $timeOfRestart = $opcacheStatusTemp["opcache_statistics"]["last_restart_time"];
                if ($timeOfRestart) {
                    $opcacheStatus["last_restart_time"] = (new DateTime())->setTimestamp($timeOfRestart)->format("Y-m-d H:i:s");
                } else {
                    $opcacheStatus["last_restart_time"] = "none";
                }

                if ($opcacheConfigs) {
                    $opcacheStatus["automatic_validation"] = $opcacheConfigs["directives"]["opcache.validate_timestamps"];
                    $opcacheStatus["max_accelerated_files"] = $opcacheConfigs["directives"]["opcache.max_accelerated_files"];

                    if (key_exists("version", $opcacheConfigs)) {
                        if (key_exists("opcache_product_name", $opcacheConfigs["version"])) {
                            $opcacheVersion = $opcacheConfigs["version"]["opcache_product_name"];
                        }
                        if (key_exists("version", $opcacheConfigs["version"])) {
                            $opcacheVersion .= " " . $opcacheConfigs["version"]["version"];
                        }
                    }
                }
            }
        }

        $opcache["enabled"] = $opcacheEnabled;
        $opcache["version"] = $opcacheVersion;
        $opcache["status"] = $opcacheStatus;
        return $opcache;
    }
}