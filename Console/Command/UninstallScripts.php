<?php
namespace DG\Util\Console\Command;

use DG\Util\Helper\Data;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
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
     * UninstallScripts constructor.
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
     * Unpublish files
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function unpublishFiles()
    {
       foreach ($this->scripts as $script) {
           $dir = $this->rootDir;
           $path = $script;
           
           $dir->delete($path);
       }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->unpublishFiles();
            $output->writeln('<info>Scripts have been successfully uninstalled.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
