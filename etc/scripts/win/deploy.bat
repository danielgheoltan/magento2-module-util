@ECHO OFF
CLS

:: ----------------------------------------------------------------------------

:: Enable maintenance mode
CALL php bin/magento maintenance:enable

:: Cleanup
RMDIR /S /Q "generated" "pub/static/adminhtml" "pub/static/frontend" "var/cache" "var/page_cache" "var/view_preprocessed" 2>NUL
DEL /Q /F "pub\static\deployed_version.txt" 2>NUL

:: Flush cache
CALL php bin/magento cache:flush

:: Update required components
CALL composer update

:: Run setup scripts
CALL php bin/magento setup:upgrade

:: Reindex
CALL php bin/magento indexer:reindex

:: Resize images
CALL php bin/magento catalog:images:resize

:: Deploy static view files
CALL php bin/magento setup:static-content:deploy en_US --no-html-minify -f

:: Disable maintenance mode
CALL php bin/magento maintenance:disable

:: ----------------------------------------------------------------------------

:: Force execution to quit at the end of the "main" logic
EXIT /B %ERRORLEVEL%
