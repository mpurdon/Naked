<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Application\Configuration;

use Naked\Application\Configuration;

/**
 * Builds a configuration object based on the current environment
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Builder
{
    /**
     * @var string
     */
    protected $sectionToLoad;

    /**
     * @var Naked\Application\Environment $environment
     */
    protected $environment;

    /**
     * Constructor
     *
     * @Inject
     * @param Naked\Application\Environment $environment
     */
    public function __construct(\Naked\Application\Environment $environment)
    {
        $this->environment = $environment;
        $this->setSectionToLoad();
    }

    /**
     * Determine which section we want to load
     */
    protected function setSectionToLoad()
    {
        if ($this->environment->isDevelopment()) {
            $this->sectionToLoad = 'development';
        } else {
            $this->sectionToLoad = 'production';
        }
    }

    /**
     * Build a configuration object
     */
    public function build()
    {
        $configuration = new Configuration();
        $this->load($configuration);

        return $configuration;
    }

    /**
     * Load the application configuration based on the current environment
     */
    protected function load($configuration)
    {
        $this->loadFrameworkConfiguration($configuration);
        $this->loadModuleConfigurations($configuration);
        $configuration->setReadOnly();
    }

    /**
     * Determine which section of the ini file we should load
     *
     * @return string
     */
    protected function getSectionToLoad()
    {
        return $this->sectionToLoad;
    }

    /**
     * Load the main configuration from the base configuration file
     */
    protected function loadFrameworkConfiguration($configuration)
    {
        $configFile = $this->environment->getPath('configuration');
        $configFile .= DIRECTORY_SEPARATOR . 'configuration.ini';

        $options = parse_ini_file($configFile, true);
        $sections = array_keys($options);

        if (!$sections) {
            return false;
        }

        $sectionMap = $this->getSectionMapping($sections);

        // Don't bother processing if we can't find the section we want to load
        if (!array_key_exists($this->getSectionToLoad(), $sectionMap)) {
            throw new \RuntimeException('Could not find a ' . $this->getSectionToLoad() . ' section in the configuration sections ' . implode(',', $sections));
        }

        foreach ($sections as $section) {
            if (strpos($section, ':') === false) {
                continue;
            }

            list($name,$parent) = explode(':', $section);
            $name = trim($name);
            $nameSectionMap = $sectionMap[$name];

            $parent = trim($parent);
            $parentSectionMap = $sectionMap[$parent];

            if (isset($options[$parentSectionMap])) {
                $options[$nameSectionMap] = array_merge($options[$parentSectionMap], $options[$nameSectionMap]);
            }
        }

        $configuration->init($options[$sectionMap[$this->getSectionToLoad()]]);
        return true;
    }

    /**
     * Get the map of sections to keys
     *
     * @param array $sections
     * @return array
     */
    protected function getSectionMapping($sections)
    {
        $sectionMap = array();

        foreach($sections as $section) {
            $cutOff = strpos($section, ':');
            if ($cutOff === false) {
                $cutOff = strlen($section);
            }
            $name = substr($section, 0, $cutOff);
            $sectionMap[trim($name)] = $section;
        }

        return $sectionMap;
    }

    /**
     * Load the module configurations from the module configuration files
     *
     * @todo Get module based configurations working
     * @param Naked\Application\Configuration $configuration
     */
    protected function loadModuleConfigurations($configuration)
    {
    }
}
