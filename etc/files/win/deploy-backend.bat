@ECHO OFF
CLS

:: ----------------------------------------------------------------------------

:: Enable maintenance mode
CALL php bin/magento maintenance:enable

:: Cleanup
RMDIR /S /Q "generated" "pub/static/adminhtml" "var/cache" "var/page_cache" "var/view_preprocessed" 2>NUL
DEL /Q /F "pub\static\deployed_version.txt" 2>NUL

:: Flush cache
CALL php bin/magento cache:flush

:: Run setup scripts
CALL php bin/magento setup:upgrade

:: Deploy static files
CALL php bin/magento setup:static-content:deploy en_US --area="adminhtml" --no-html-minify -f

:: Disable maintenance mode
CALL php bin/magento maintenance:disable

:: ----------------------------------------------------------------------------

:: Force execution to quit at the end of the "main" logic
EXIT /B %ERRORLEVEL%
