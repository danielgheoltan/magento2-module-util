<?php
namespace DG\Util\Console\Command;

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
     * DeployTheme constructor.
     *
     * @param ThemeCollectionFactory $collectionFactory
     */
    public function __construct(
        ThemeCollectionFactory $collectionFactory
    ) {
        parent::__construct();

        $this->_collectionFactory = $collectionFactory;
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
                    ['all']
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
        $availableThemes = $this->_collectionFactory->create()->loadRegisteredThemes();

        foreach ($availableThemes as $availableTheme) {
            if (($availableTheme->getCode() == $theme) || ('Magento/backend' == $theme)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Deploy themes
     *
     * @param OutputInterface $output
     * @param array $themes
     * @param array $languages
     */
    private function deployThemes($output, $themes, $languages)
    {
        foreach ($themes as $theme) {
            if (!$this->existsTheme($theme)) {
                $output->writeln('');
                $output->writeln('<error>' . sprintf(__('Theme %s is not registered.'), $theme) . '</error>');
                continue;
            }

            foreach ($languages as $language) {
                $output->writeln('');
                $output->writeln('<info>Theme: ' . $theme . '</info>');
                $output->writeln('<info>Language: ' . $language . '</info>');

                $cmd = sprintf(
                    'php bin/magento setup:static-content:deploy %s --theme %s --no-html-minify -f',
                    $language,
                    $theme
                );

                $descriptor = [
                    0 => ['pipe', 'r'], // stdin is a pipe that the child will read from
                    1 => ['pipe', 'w'], // stdout is a pipe that the child will write to
                    2 => ['pipe', 'w']  // stderr is a pipe that the child will write to
                ];

                flush();

                $process = proc_open($cmd, $descriptor, $pipes, realpath('./'), []);

                if (is_resource($process)) {
                    while ($s = fgets($pipes[1])) {
                        print $s;
                        flush();
                    }
                }
            }
        }

        $output->writeln('');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $themes = $input->getOption(self::THEME_OPTION);
            $languages = $input->getOption(self::LANGUAGE_OPTION);

            $this->deployThemes($output, $themes, $languages);

            $output->writeln('<info>' . __('Done.') . '</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
