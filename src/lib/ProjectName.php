<?php
/**
 * created: 2017
 *
 * @author    bchatard
 * @license   MIT
 */


class ProjectName
{

    private $listXpathProjectName = [
        'value' => Project::XPATH_PROJECT_NAME,
        'name'  => Project::XPATH_PROJECT_NAME_ALT,
    ];

    public function getViaName($path, callable $logger)
    {
        if (is_readable($path)) {
            $logger('  Work with .idea/name');

            return file_get_contents($path);
        }

        return false;
    }

    public function getViaDotName($path, callable $logger)
    {
        if (is_readable($path)) {
            $logger('  Work with .idea/.name');

            return file_get_contents($path);
        }

        return false;
    }

    public function getViaDotIml($path, callable $logger)
    {
        $imlFile = glob($path);
        if (count($imlFile) === 1) {
            $logger('  Work with .iml');

            return basename($imlFile[0], '.iml');
        }

        return false;
    }

    public function getViaWorkspace($path, callable $logger)
    {
        if (is_readable($path)) {
            $logger('  Work with .idea/workspace.xml');
            $workspaceXml = new SimpleXMLElement($path, null, true);

            foreach ($this->listXpathProjectName as $field => $xpath) {
                $logger("    try with {$xpath} || $field");
                $nameElements = $workspaceXml->xpath($xpath);
                if (count($nameElements) > 0 && isset($nameElements[0]->$field)) {
                    return $nameElements[0]->$field->__toString();
                }
            }

            $nameElements = $workspaceXml->xpath(Project::XPATH_PROJECT_NAME_AS);
            if (count($nameElements) > 0 && isset($nameElements[0]->path)) {
                $logger('    try with AS');

                return trim(trim($nameElements[0]->path->__toString()), '.iws');
            }
        }

        return false;
    }

    public function getViaDotSln($path, callable $logger)
    {
        if (strpos($path, '.sln') !== false) {
            $logger('  Work with .sln');

            return basename($path, '.sln');
        }

        return false;
    }

}
