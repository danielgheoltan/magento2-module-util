<?php
namespace DG\Util\Console\Command;

use DG\Util\Helper\Data;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\OsInfo;
use Magento\Framework\Module\Dir;

class InstallScripts extends Command
{
    const COMMAND_NAME = 'dg-util:install-scripts';
    const COMMAND_DESCRIPTION = 'Installs scripts that save you time and effort';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var OsInfo
     */
    protected $osInfo;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $moduleDir;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $rootDir;

    /**
     * @var String
     */
    protected $sourcePath;

    /**
     * @var String
     */
    protected $targetPath;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Dir\Reader
     */
    protected $moduleReader;

    /**
     * @var array
     */
    protected $files;

    /**
     * @param Data $helper
     * @param OsInfo $osInfo
     * @param Filesystem $filesystem
     * @param Dir\Reader $reader
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function __construct(
        Data $helper,
        OsInfo $osInfo,
        Filesystem $filesystem,
        Dir\Reader $reader
    ) {
        parent::__construct();

        $this->helper = $helper;
        $this->osInfo = $osInfo;
        $this->filesystem = $filesystem;
        $this->moduleReader = $reader;
        $this->rootDir = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->moduleDir = $this->moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'DG_Util');
        $this->sourcePath = str_replace($this->rootDir->getAbsolutePath(),'', $this->moduleDir);
        $this->files = $this->helper->getFiles();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * Publish file
     *
     * @param WriteInterface $dir
     * @param string $sourcePath
     * @param string $destinationPath
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function publishFile(
        WriteInterface $dir,
        $sourcePath,
        $destinationPath
    ) {
        $dir->delete($destinationPath);
        $symlink = $dir->createSymlink($sourcePath, $destinationPath, $dir);
        $dir->changePermissions($destinationPath, 0777);

        return $symlink;
    }

    /**
     * Publish files
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function publishFiles()
    {
        foreach ($this->files as $file) {
            if ($this->osInfo->isWindows()) {
                $file .= '.bat';
            }
            $dir = $this->rootDir;
            $sourcePath = $this->sourcePath . '/files/' . ($this->osInfo->isWindows() ? 'win' : 'linux') . '/' . $file;
            $destinationPath = $file;
            $this->publishFile($dir, $sourcePath, $destinationPath);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->publishFiles();
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
