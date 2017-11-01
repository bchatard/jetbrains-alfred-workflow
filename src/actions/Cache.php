<?php
/**
 * created: 2017
 *
 * @author    bchatard
 * @license   MIT
 */


class Cache
{

    /**
     * @return string
     */
    public static function clean()
    {
        $cacheDir = $_SERVER['alfred_workflow_cache'];
        if (is_writable($cacheDir)) {
            $caches = glob("{$cacheDir}/*.json");
            if (count($caches)) {
                foreach ($caches as $cache) {
                    unlink($cache);
                }

                return 'Cache cleaned';
            }
        }

        return 'No cache';
    }

}
