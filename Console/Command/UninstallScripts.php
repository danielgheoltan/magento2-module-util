<?php
namespace DG\Util\Console\Command;

use DG\Util\Helper\Data;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\OsInfo;
use Magento\Framework\Module\Dir;

class UninstallScripts extends Command
{
    const COMMAND_NAME = 'dg-util:uninstall-scripts';
    const COMMAND_DESCRIPTION = 'Uninstalls scripts that save you time and effort';

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
    protected $rootDir;

    /**
     * @var Filesystem
     */
    protected $filesystem;

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
        $this->rootDir = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
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
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function unPublishFiles()
    {
       foreach ($this->files as $file) {
           if ($this->osInfo->isWindows()) {
               $file .= '.bat';
           }
           $dir = $this->rootDir;
           $dir->delete($file);
       }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->unPublishFiles();
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
