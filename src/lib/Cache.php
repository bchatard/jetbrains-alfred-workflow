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
     * @var string
     */
    private $cacheFile;

    /**
     * Cache lifetime in seconds
     *
     * @var int
     */
    private $cacheLifetime;

    public function __construct($cacheFile)
    {
        $this->cacheFile = $cacheFile;
        $this->cacheLifetime = isset($_SERVER['jb_cache_lifetime']) ? (int)$_SERVER['jb_cache_lifetime'] : 3600;
    }

    public function getProjectsData()
    {
        if (is_readable($this->cacheFile)
            && (filemtime($this->cacheFile) > strtotime("-{$this->cacheLifetime} seconds"))
        ) {
            return json_decode(file_get_contents($this->cacheFile), true);
        }

        return [];
    }

    public function setProjectsData($projectsData)
    {
        $projectsData = json_encode($projectsData);

        file_put_contents($this->cacheFile, $projectsData, LOCK_EX);
    }

}
