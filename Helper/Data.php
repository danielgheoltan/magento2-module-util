<?php
namespace DG\Util\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir;
use Magento\Framework\OsInfo;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Dir\Reader
     */
    protected $moduleReader;

    /**
     * @var OsInfo
     */
    protected $osInfo;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $rootDir;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Dir\Reader $reader
     * @param OsInfo $osInfo
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        Dir\Reader $reader,
        OsInfo $osInfo,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);

        $this->moduleReader = $reader;
        $this->osInfo = $osInfo;
        $this->rootDir = $directoryList->getRoot();
    }

    /**
     * Get absolute path to this module
     *
     * @return string
     */
    private function getModuleAbsoluteDir()
    {
        return $this->moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, $this->_getModuleName());
    }

    /**
     * Get relative path to this module
     *
     * @return string
     */
    private function getModuleRelativeDir()
    {
        return str_replace($this->rootDir, '', $this->getModuleAbsoluteDir());
    }

    /**
     * Get script relative path by file name
     *
     * @param $fileName
     * @return string
     */
    public function getScriptRelativePath($fileName)
    {
        return $this->getModuleRelativeDir() .
            '/scripts/' .
            ($this->osInfo->isWindows() ? 'win' : 'linux') .
            '/' .
            $fileName;
    }

    /**
     * Append extension to a script file name
     *
     * @param $fileName
     * @return string
     */
    private function appendScriptExtension($fileName)
    {
        if ($this->osInfo->isWindows()) {
            $fileName .= '.bat';
        }

        return $fileName;
    }

    /**
     * Get scripts
     *
     * @return array
     */
    public function getScripts()
    {
        $scripts = [
            'deploy',
            'deploy-backend',
            'deploy-frontend',
            'deploy-grunt-theme',
            'deploy-grunt-theme-blank',
            'deploy-grunt-theme-luma',
            'deploy-theme',
            'deploy-theme-blank',
            'deploy-theme-luma',
            'di',
            'grunt-theme',
            'grunt-theme-blank',
            'grunt-theme-luma'
        ];

        return array_map([$this, 'appendScriptExtension'], $scripts);
    }

    /**
     * Get source scripts
     *
     * @return array
     */
    public function getSourceScripts()
    {
        $scripts = [
            'deploy-grunt-theme-blank',
            'deploy-theme-blank',
            'grunt-theme-blank'
        ];

        return array_map([$this, 'appendScriptExtension'], $scripts);
    }
}
