<?php
/**
 * created: 2017
 *
 * @author    bchatard
 * @license   MIT
 */

require_once __DIR__ . '/lib/Item.php';
require_once __DIR__ . '/lib/Result.php';
require_once __DIR__ . '/lib/ProjectName.php';

class Project
{

    const PATH_RECENT_PROJECT_DIRECTORIES = '/options/recentProjectDirectories.xml';
    const PATH_RECENT_PROJECTS = '/options/recentProjects.xml';
    const PATH_RECENT_SOLUTIONS = '/options/recentSolutions.xml';

    const XPATH_RECENT_PROJECT_DIRECTORIES = "//component[@name='RecentDirectoryProjectsManager']/option[@name='recentPaths']/list/option/@value";
    const XPATH_RECENT_PROJECTS = "//component[@name='RecentProjectsManager']/option[@name='recentPaths']/list/option/@value";
    const XPATH_RECENT_SOLUTIONS = "//component[@name='RiderRecentProjectsManager']/option[@name='recentPaths']/list/option/@value";

    const XPATH_PROJECT_NAME = "(//component[@name='ProjectView']/panes/pane[@id='ProjectPane']/subPane/PATH/PATH_ELEMENT/option/@value)[1]";
    const XPATH_PROJECT_NAME_ALT = "(//component[@name='ProjectView']/panes/pane[@id='ProjectPane']/subPane/expand/path/item[contains(@type, ':ProjectViewProjectNode')]/@name)[1]";
    const XPATH_PROJECT_NAME_AS = "((/project/component[@name='ChangeListManager']/ignored[contains(@path, '.iws')]/@path)[1])";
    // doesn't works: http://php.net/manual/en/simplexmlelement.xpath.php#93730
//    const XPATH_PROJECT_NAME_AS = "substring-before(((/project/component[@name='ChangeListManager']/ignored[contains(@path, '.iws')]/@path)[1]), '.iws')";


    /**
     * @var string
     */
    private $jetbrainsApp;
    /**
     * @var Result
     */
    private $result;
    /**
     * @var string
     */
    private $jetbrainsAppPath;
    /**
     * @var string
     */
    private $jetbrainsAppConfigPath;
    /**
     * @var bool
     */
    private $debug;
    /**
     * @var string
     */
    private $debugFile;

    /**
     * @var string
     */
    private $cacheDir;


    public function __construct($jetbrainsApp)
    {
        date_default_timezone_set('UTC');

        error_reporting(0); // hide all errors (not safe at all, but if a warning occur, it break the response)

        $this->jetbrainsApp = $jetbrainsApp;
        $this->result = new Result();

        $this->debug = isset($_SERVER['jb_debug']) ? (bool)$_SERVER['jb_debug'] : false;

        $this->cacheDir = $_SERVER['alfred_workflow_cache'];
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0750);
        }

        if ($this->debug) {
            $this->result->enableDebug();

            $this->debugFile = $this->cacheDir . '/debug_' . date('Ymd') . '.log';

            $this->log('PHP version: ' . PHP_VERSION);
            $this->log("Received bin: {$this->jetbrainsApp}");
        }
    }

    public function search($query)
    {
        $this->log("\n" . __FUNCTION__ . "({$query})");
        $query = $this->parseQuery($query);

        $hasQuery = !($query === '');
        try {
            $this->checkJetbrainsApp();
            $projectsData = $this->getProjectsData();

            foreach ($projectsData as $project) {
                if ($hasQuery) {
                    if (stripos($project['name'], $query) !== false
                        || stripos($project['basename'], $query) !== false
                    ) {
                        $this->addProjectItem($project['name'], $project['path']);
                    }
                } else {
                    $this->addProjectItem($project['name'], $project['path']);
                }
            }


        } catch (\Exception $e) {
            $this->addErrorItem($e);
        }

        if (!$this->result->hasItems()) {
            if ($hasQuery) {
                $this->addNoProjectMatchItem($query);
            } else {
                $this->addNoResultItem();
            }
        }

        $this->addDebugItem();

        $this->log("Projects: {$this->result->__toString()}");

        return $this->result;
    }

    private function parseQuery($query)
    {
        $query = str_replace('\ ', ' ', $query);

        return trim($query);
    }

    private function getProjectsData()
    {
        $this->log("\n" . __FUNCTION__);

        // @todo: manage cache

        if (is_readable($this->jetbrainsAppConfigPath . self::PATH_RECENT_PROJECT_DIRECTORIES)) {
            $this->log(' Work with: ' . self::PATH_RECENT_PROJECT_DIRECTORIES);
            $file = $this->jetbrainsAppConfigPath . self::PATH_RECENT_PROJECT_DIRECTORIES;
            $xpath = self::XPATH_RECENT_PROJECT_DIRECTORIES;
        } elseif (is_readable($this->jetbrainsAppConfigPath . self::PATH_RECENT_PROJECTS)) {
            $this->log(' Work with: ' . self::PATH_RECENT_PROJECTS);
            $file = $this->jetbrainsAppConfigPath . self::PATH_RECENT_PROJECTS;
            $xpath = self::XPATH_RECENT_PROJECTS;
        } elseif (is_readable($this->jetbrainsAppConfigPath . self::PATH_RECENT_SOLUTIONS)) {
            $this->log(' Work with: ' . self::PATH_RECENT_SOLUTIONS);
            $file = $this->jetbrainsAppConfigPath . self::PATH_RECENT_SOLUTIONS;
            $xpath = self::XPATH_RECENT_SOLUTIONS;
        } else {
            throw new \RuntimeException("Can't find 'options' XML in '{$this->jetbrainsAppConfigPath}'", 100);
        }

        $projectsData = [];

        $optionXml = new SimpleXMLElement($file, null, true);
        $optionElements = $optionXml->xpath($xpath);

        $this->log(' Project Paths:');
        $this->log($optionElements);

        /** @var SimpleXMLElement $optionElement */
        foreach ($optionElements as $optionElement) {
            if ($optionElement->value) {
                $path = str_replace('$USER_HOME$', $_SERVER['HOME'], $optionElement->value->__toString());

                $this->log("\nProcess {$path}");

                if (is_dir($path)) {
                    $name = $this->getProjectName($path);
                    if ($name) {
                        $projectsData[] = [
                            'name'     => $name,
                            'path'     => $path,
                            'basename' => basename($path),
                        ];
                    } else {
                        $this->log("  Can't find project name");
                    }
                } else {
                    $this->log(" {$path} doesn't exists");
                }
            }
        }

        $this->log('Projects Data:');
        $this->log($projectsData);

        return $projectsData;
    }

    private function getProjectName($path)
    {
        $this->log(__FUNCTION__);

        $logger = function ($message) {
            $this->log($message);
        };

        $getProjectName = new ProjectName();

        $case = [
            "{$path}/.idea/name"          => 'getViaName',
            "{$path}/.idea/.name"         => 'getViaDotName',
            "{$path}/.idea/.iml"          => 'getViaDotIml',
            "{$path}/.idea/workspace.xml" => 'getViaWorkspace',
            '.sln'                        => 'getViaDotSln',
        ];

        foreach ($case as $argPath => $method) {
            if ($projectName = $getProjectName->$method($argPath, $logger)) {
                return $projectName;
            }
        }

        return false;
    }

    private function checkJetbrainsApp()
    {
        $this->log("\n" . __FUNCTION__);
        $handle = @fopen($this->jetbrainsApp, 'rb');
        if ($handle) {
            while (($row = fgets($handle)) !== false) {
                if (strpos($row, 'RUN_PATH =') === 0) {
                    $jetbrainsAppPath = str_replace('RUN_PATH = u', '', $row);
                    $jetbrainsAppPath = trim($jetbrainsAppPath);
                    $jetbrainsAppPath = trim($jetbrainsAppPath, "'");
                    if (is_dir($jetbrainsAppPath) && is_readable($jetbrainsAppPath)) {
                        $this->jetbrainsAppPath = $jetbrainsAppPath;

                        $this->log("App path: {$this->jetbrainsAppPath}");
                    }
                }
                if (strpos($row, 'CONFIG_PATH =') === 0) {
                    $jetbrainsAppConfigPath = str_replace('CONFIG_PATH = u', '', $row);
                    $jetbrainsAppConfigPath = trim($jetbrainsAppConfigPath);
                    $jetbrainsAppConfigPath = trim($jetbrainsAppConfigPath, "'");
                    if (is_dir($jetbrainsAppConfigPath) && is_readable($jetbrainsAppConfigPath)) {
                        $this->jetbrainsAppConfigPath = $jetbrainsAppConfigPath;

                        $this->log("App config path: {$this->jetbrainsAppConfigPath}");
                    }
                }

                if ($this->jetbrainsAppPath && $this->jetbrainsAppConfigPath) {
                    $this->result->addVariable('bin', $this->jetbrainsApp);

                    break;
                }

            }
            if (!$this->jetbrainsAppPath) {
                throw new \RuntimeException("Can't find application path for '{$this->jetbrainsApp}'");
            }
            if (!$this->jetbrainsAppConfigPath) {
                throw new \RuntimeException("Can't find application configuration path for '{$this->jetbrainsApp}'");
            }
        } else {
            throw new \InvalidArgumentException("Can't find command line launcher for '{$this->jetbrainsApp}'");
        }
    }

    private function addProjectItem($name, $path)
    {
        $item = new Item();
        $item->setUid($name)
             ->setTitle($name)
             ->setMatch($name)
             ->setSubtitle($path)
             ->setArg($path)
             ->setAutocomplete($name)
             ->setIcon($this->jetbrainsAppPath, 'fileicon')
             ->setText($path, $path)
             ->setVariables('name', $name);

        $this->result->addItem($item);
    }

    private function addNoProjectMatchItem($query)
    {
        $item = new Item();
        $item->setUid('not_found')
             ->setTitle("No project match '{$query}'")
             ->setSubtitle("No project match '{$query}'")
             ->setArg('')
             ->setAutocomplete('')
             ->setValid(false)
             ->setIcon($this->jetbrainsAppPath, 'fileicon');

        $this->result->addItem($item);

        $this->log('No project match');
    }

    private function addNoResultItem()
    {
        $item = new Item();
        $item->setUid('none')
             ->setTitle("Can't find projects")
             ->setSubtitle('check configuration or contact developer')
             ->setArg('')
             ->setAutocomplete('')
             ->setValid(false)
             ->setIcon($this->jetbrainsAppPath, 'fileicon');

        $this->result->addItem($item);

        $this->log('No results');
    }

    /**
     * @param \Exception $e
     */
    private function addErrorItem($e)
    {

        $item = new Item();
        $item->setUid("e_{$e->getCode()}")
             ->setTitle($e->getMessage())
             ->setSubtitle('Please enable log and contact developer')
             ->setArg('')
             ->setAutocomplete('')
             ->setValid(false)
             ->setIcon(($e instanceof \RuntimeException) ? 'AlertStopIcon.icns' : 'AlertCautionIcon.icns')
             ->setText($e->getTraceAsString());

        $this->result->addItem($item);

        $this->log($e);
    }

    public function addDebugItem()
    {
        if ($this->debug) {
            $item = new Item();
            $item->setUid('debug')
                 ->setTitle("Debug file: {$this->debugFile}")
                 ->setSubtitle('Add this file to your issue - ⌘+C to get the path')
                 ->setArg('')
                 ->setAutocomplete('')
                 ->setValid(false)
                 ->setIcon('AlertNoteIcon.icns')
                 ->setText($this->debugFile);

            $this->result->addItem($item);
        }
    }

    private function log($message)
    {
        if ($this->debug) {
            if ($message instanceof \Exception) {
                $message = $message->__toString();
            } elseif (is_object($message) || is_array($message)) {
                $message = print_r($message, true);
            }

            $message .= "\n";

            file_put_contents($this->debugFile, $message, FILE_APPEND);
        }
    }

}
