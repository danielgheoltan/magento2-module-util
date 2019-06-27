<?php
namespace DG\Util\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeployTheme extends Command
{
    const COMMAND_NAME = 'dg-util:deploy-theme';
    const COMMAND_DESCRIPTION = 'Deploy theme';

    /**
     * Key for theme option
     */
    const THEME_OPTION = 'theme';

    /**
     * Key for languages option
     */
    const LANGUAGE_OPTION = 'language';

    /**
     * @var ThemeCollectionFactory
     */
    private $_collectionFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $staticDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @var InputInterface
     */
    public $input;

    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * DeployTheme constructor.
     *
     * @param ThemeCollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        ThemeCollectionFactory $collectionFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct();

        $this->_collectionFactory = $collectionFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->filesystem = $filesystem;
        $this->staticDirectory = $filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
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
                    'Generate files only for the specified themes.',
                    []
                ),
                new InputOption(
                    self::LANGUAGE_OPTION,
                    '-l',
                    InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                    'Generate files only for the specified languages.',
                    []
                )
            ]);

        parent::configure();
    }

    /**
     * Check if theme exists
     *
     * @param $theme
     *
     * @return bool
     */
    private function existsTheme($theme)
    {
        // TODO: Review this method

        $availableThemes = $this->_collectionFactory->create()->loadRegisteredThemes();

        foreach ($availableThemes as $availableTheme) {
            if (($availableTheme->getCode() == $theme) || ('Magento/backend' == $theme)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Empty directory
     *
     * @param $write
     * @param $path
     */
    private function emptyDirectory($write, $path)
    {
        if ($write->isExist($path)) {
            $write->delete($path);
            $this->output->writeln('<info>' . sprintf(__('Folder %s has been deleted.'), $path) . '</info>');
        } else {
            // $this->output->writeln('<comment>' . sprintf(__('Folder %s does not exist.'), $path) . '</comment>');
        }
    }

    /**
     * Clean and flush cache
     */
    private function clearAndFlushCache()
    {
        /*
        $types = [
            'config',
            'layout',
            'block_html',
            'full_page',
            'translate'
        ];
        */

        $types = array_keys($this->_cacheTypeList->getTypes());

        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }

        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    /**
     * Clean static files in pub/static and var/view_preprocessed directories
     *
     * @param array $themes
     * @param array $languages
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function clearStaticFiles($themes, $languages)
    {
        foreach ($themes as $theme) {
            if (count($languages)) {
                foreach ($languages as $language) {
                    $this->emptyDirectory($this->staticDirectory, 'frontend/' . $theme . '/' . $language);
                    $this->emptyDirectory($this->varDirectory, DirectoryList::TMP_MATERIALIZATION_DIR . '/less/frontend/' . $theme . '/' . $language);
                    $this->emptyDirectory($this->varDirectory, DirectoryList::TMP_MATERIALIZATION_DIR . '/pub/static/frontend/' . $theme . '/' . $language);
                    $this->emptyDirectory($this->varDirectory, DirectoryList::TMP_MATERIALIZATION_DIR . '/source/frontend/' . $theme . '/' . $language);
                }
            } else {
                $this->emptyDirectory($this->staticDirectory, 'frontend/' . $theme);
                $this->emptyDirectory($this->varDirectory, DirectoryList::TMP_MATERIALIZATION_DIR . '/less/frontend/' . $theme);
                $this->emptyDirectory($this->varDirectory, DirectoryList::TMP_MATERIALIZATION_DIR . '/pub/static/frontend/' . $theme);
                $this->emptyDirectory($this->varDirectory, DirectoryList::TMP_MATERIALIZATION_DIR . '/source/frontend/' . $theme);
            }
        }
    }

    /**
     * Execute commands
     *
     * @param array $commands
     */
    private function executeCommands($commands)
    {
        $descriptor = [
            0 => ['pipe', 'r'], // stdin is a pipe that the child will read from
            1 => ['pipe', 'w'], // stdout is a pipe that the child will write to
            2 => ['pipe', 'w']  // stderr is a pipe that the child will write to
        ];

        foreach ($commands as $command) {
            flush();

            $process = proc_open($command, $descriptor, $pipes, realpath('./'), []);

            if (is_resource($process)) {
                while ($s = fgets($pipes[1])) {
                    print $s;
                    flush();
                }
            }
        }
    }

    /**
     * Deploy theme
     *
     * @param array $theme
     * @param array $languages
     */
    private function deployTheme($theme, $languages)
    {
        foreach ($languages as $language) {
            $commands = [];

            $this->output->writeln('');
            $this->output->writeln('<info>' . sprintf(__('Theme: %s'), $theme) . '</info>');
            $this->output->writeln('<info>' . sprintf(__('Language: %s'), $language) . '</info>');

            $commands []= sprintf(
                'php bin/magento setup:static-content:deploy %s --theme %s --no-html-minify -f',
                $language,
                $theme
            );

            $this->executeCommands($commands);
        }
    }

    /**
     * Deploy themes
     *
     * @param array $themes
     * @param array $languages
     */
    private function deployThemes($themes, $languages)
    {
        foreach ($themes as $theme) {
            if (!$this->existsTheme($theme)) {
                $this->output->writeln('');
                $this->output->writeln('<comment>' . sprintf(__('Theme %s is not registered.'), $theme) . '</comment>');
                continue;
            }

            $this->deployTheme($theme, $languages);
        }

        $this->output->writeln('');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        try {
            $themes = $input->getOption(self::THEME_OPTION);
            $languages = $input->getOption(self::LANGUAGE_OPTION);

            $this->clearStaticFiles($themes, $languages);
            $this->clearAndFlushCache();
            $this->deployThemes($themes, $languages);

            $output->writeln('<info>' . __('Done.') . '</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
