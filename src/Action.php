<?php
/**
 * created: 2017
 *
 * @author    bchatard
 * @license   MIT
 */


class Action
{

    const XPATH_VERSION = '/plist/dict/key[text()="version"]/following-sibling::string[1]/text()';

    const WORKFLOW_SOURCE = 'https://raw.githubusercontent.com/bchatard/jetbrains-alfred-workflow/master/package/JetBrains%20-%20Open%20project.alfredworkflow';

    private $allowedActions = [
        'cache:clean'  => 'cacheClean',
        'check:update' => 'checkUpdate',
    ];

    /**
     * @var string
     */
    private $action;

    public function __construct($action)
    {
        date_default_timezone_set('UTC');

        error_reporting(0); // hide all errors (not safe at all, but if a warning occur, it break the response)

        $this->action = $action;
    }

    /**
     * @param null $query
     * @return string
     */
    public function execute($query = null)
    {
        if (array_key_exists($this->action, $this->allowedActions)) {
            $function = $this->allowedActions[$this->action];

            return $this->$function($query);
        }

        return "unknown action {$this->action}";
    }

    /**
     * @return string
     */
    private function cacheClean()
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

    /**
     * @return string
     */
    private function checkUpdate()
    {
        $cacheDir = $_SERVER['alfred_workflow_cache'] . '/tmp';
        if (!mkdir($cacheDir) && !is_dir($cacheDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $cacheDir));
        }

        $localVersion = $_SERVER['alfred_workflow_version'];

        // alfred workflow is like a zip file
        copy(self::WORKFLOW_SOURCE, "{$cacheDir}/tmp.zip");

        $message = "Sorry, I can't find current version. Please try again later or report an issue.";

        $zip = new ZipArchive();
        if ($zip->open("{$cacheDir}/tmp.zip")) {
            $zip->extractTo($cacheDir, 'info.plist');
            $zip->close();

            $infoXml = new SimpleXMLElement("{$cacheDir}/info.plist", null, true);
            $currentVersion = $infoXml->xpath(self::XPATH_VERSION);
            if (count($currentVersion)) {
                $currentVersion = $currentVersion[0]->__toString();
                if (version_compare($localVersion, $currentVersion, '<')) {
                    $message = 'A new release is available!';
                } else {
                    $message = 'You are up to date!';
                }

                unlink("{$cacheDir}/info.plist");
            }
        }

        unlink("{$cacheDir}/tmp.zip");

        return $message;
    }

}
