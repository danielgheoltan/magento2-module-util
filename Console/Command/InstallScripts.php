<?php
namespace DG\Util\Console\Command;

use DG\Util\Helper\Data;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $rootDir;

    /**
     * @var array
     */
    protected $scripts;

    /**
     * InstallScripts constructor.
     *
     * @param Data $helper
     * @param Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Data $helper,
        Filesystem $filesystem
    ) {
        parent::__construct();

        $this->helper = $helper;
        $this->filesystem = $filesystem;
        $this->rootDir = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->scripts = $this->helper->getScripts();
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
        foreach ($this->scripts as $script) {
            $dir = $this->rootDir;
            $sourcePath = $this->helper->getScriptRelativePath($script);
            $destinationPath = $script;

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
            $output->writeln('<info>Scripts have been successfully installed to Magento root folder.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
