<?php
namespace DG\Util\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @return array
     */
    public function getFiles()
    {
        return [
            'deploy',
            'deploy-backend',
            'deploy-frontend',
            'deploy-theme',
            'deploy-theme-blank',
            'deploy-theme-luma',
            'di',
            'grunt-theme',
            'grunt-theme-blank',
            'grunt-theme-luma'
        ];
    }
}
