@ECHO OFF
CLS

:: ----------------------------------------------------------------------------

SET THEME=%1
SET LOCALE=%2

:: ----------------------------------------------------------------------------

RMDIR /S /Q "pub/static/frontend/%THEME%/%LOCALE%" ^
            "var/cache" ^
            "var/page_cache" ^
            "var/view_preprocessed/less/frontend/%THEME%/%LOCALE%" ^
            "var/view_preprocessed/pub/static/frontend/%THEME%/%LOCALE%" ^
            "var/view_preprocessed/source/frontend/%THEME%/%LOCALE%" 2>NUL

:: ----------------------------------------------------------------------------

CALL php bin/magento setup:static-content:deploy %LOCALE% --theme="%THEME%" --no-html-minify -f

:: ----------------------------------------------------------------------------

:: Force execution to quit at the end of the "main" logic
EXIT /B %ERRORLEVEL%
