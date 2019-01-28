<?php
namespace DG\Util\Console\Command;

use DG\Util\Helper\Data;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class InstallScripts extends Command
{
    const COMMAND_NAME = 'dg-util:install-scripts';
    const COMMAND_DESCRIPTION = 'Installs scripts that save you time and effort';

    /**
     * Name of "theme" input option
     */
    const THEME_OPTION = 'theme';

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
     * @var array
     */
    protected $sourceScripts;

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
        $this->sourceScripts = $this->helper->getSourceScripts();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->setDefinition([
                new InputOption(
                    self::THEME_OPTION,
                    '-t',
                    InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                    'Generate scripts for the specified themes.',
                    []
                )
            ]);

        parent::configure();
    }

    /**
     * Publish file
     *
     * @param WriteInterface $dir
     * @param string $sourcePath
     * @param string $destinationPath
     * @param string $method
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function publishFile(
        WriteInterface $dir,
        $sourcePath,
        $destinationPath,
        $method = 'symlink'
    ) {
        $result = false;

        if ($method == 'symlink') {
            $dir->delete($destinationPath);
            $result = $dir->createSymlink($sourcePath, $destinationPath, $dir);
        } else {
            if (!$dir->isExist($destinationPath)) {
                $result = $dir->copyFile($sourcePath, $destinationPath, $dir);
            }
        }

        $dir->changePermissions($destinationPath, 0777);

        return $result;
    }

    /**
     * Publish files
     *
     * @param array $themes
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function publishFiles($themes)
    {
        foreach ($this->scripts as $script) {
            $this->publishFile(
                $this->rootDir,
                $this->helper->getScriptRelativePath($script),
                $script
            );
        }

        foreach ($themes as $option) {
            foreach ($this->sourceScripts as $script) {
                $this->publishFile(
                    $this->rootDir,
                    $this->helper->getScriptRelativePath($script),
                    str_replace('blank', $option, $script),
                    'copy'
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $themes = $input->getOption(self::THEME_OPTION);
            $this->publishFiles($themes);
            $output->writeln('<info>Scripts have been successfully installed to Magento root folder.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
