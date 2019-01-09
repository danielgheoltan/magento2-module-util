@ECHO OFF
CLS

:: ----------------------------------------------------------------------------

:: Enable maintenance mode
CALL php bin/magento maintenance:enable

:: Cleanup
RMDIR /S /Q "generated" "var/di" 2>NUL

:: Flush cache
CALL php bin/magento cache:flush

:: Compile code
CALL php bin/magento setup:di:compile

:: Disable maintenance mode
CALL php bin/magento maintenance:disable

:: ----------------------------------------------------------------------------

:: Force execution to quit at the end of the "main" logic
EXIT /B %ERRORLEVEL%
