<?php
namespace DG\Util\Console\Command;

use DG\Util\Helper\Data;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\ResourceConnection;

class Config extends Command
{
    const COMMAND_NAME = 'dg-util:config';
    const COMMAND_DESCRIPTION = 'Apply configuration settings';

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * Config constructor.
     *
     * @param ResourceConnection $resource
     * @param CacheManager $cacheManager
     */
    public function __construct(
        ResourceConnection $resource,
        CacheManager $cacheManager
    ) {
        parent::__construct();

        $this->resource = $resource;
        $this->cacheManager = $cacheManager;
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
     * Apply configuration settings
     */
    protected function config()
    {
        $tableName = $this->resource->getTableName('core_config_data');
        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        // Set Session Lifetime
        $connection->rawQuery("INSERT INTO `" . $tableName . "` (scope, scope_id, path, value) VALUES ('default', 0, 'admin/security/session_lifetime', 604800) ON DUPLICATE KEY UPDATE `value` = 604800;");

        // Disable Sign Static Files
        $connection->rawQuery("INSERT INTO `" . $tableName . "` (scope, scope_id, path, value) VALUES ('default', 0, 'dev/static/sign', 0) ON DUPLICATE KEY UPDATE `value` = 0;");

        // Allow Symlinks
        $connection->rawQuery("INSERT INTO `" . $tableName . "` (scope, scope_id, path, value) VALUES ('default', 0, 'dev/template/allow_symlink', 1) ON DUPLICATE KEY UPDATE `value` = 1;");

        // Disable WYSIWYG Editor by Default
        $connection->rawQuery("INSERT INTO `" . $tableName . "` (scope, scope_id, path, value) VALUES ('default', 0, 'cms/wysiwyg/enabled', 'hidden') ON DUPLICATE KEY UPDATE `value` = 'hidden';");

        // Set Admin Startup Page
        $connection->rawQuery("INSERT INTO `" . $tableName . "` (scope, scope_id, path, value) VALUES ('default', 0, 'admin/startup/menu_item_id', 'Magento_Config::system_config') ON DUPLICATE KEY UPDATE `value` = 'Magento_Config::system_config';");

        // Disable Some Cache Types
        $this->cacheManager->setEnabled(['layout', 'block_html', 'full_page'], false);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->config();
            $output->writeln('<info>Configuration settings have been successfully applied.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
