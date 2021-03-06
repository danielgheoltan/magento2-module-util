# DG Util

## Introduction

Installs scripts that save you time and effort.

## Install

```
composer require danielgheoltan/magento2-module-util
php bin/magento setup:upgrade
php bin/magento module:enable DG_Util
php bin/magento cache:flush
php bin/magento dg-util:install-scripts
php bin/magento dg-util:config
```

The ```dg-util:install-scripts``` command will install a set of default handy scripts in Magento 2 root folder (see [below](#commands)).
These scripts are symlinked to the corresponding scripts in ```vendor/dg/module-util/etc/scripts``` directory.
\
\
You can also install custom scripts for several themes using the ```--theme``` argument.
For example:

```
php bin/magento dg-util:install-scripts --theme=first --theme=second  
```

will create the following scripts:

| Windows                         | Linux                   |
| ------------------------------- | ----------------------- |
| `deploy-grunt-theme-first.bat`  | `./deploy-grunt-first`  |
| `deploy-grunt-theme-second.bat` | `./deploy-grunt-second` |
| `deploy-theme-first.bat`        | `./deploy-theme-first`  |
| `deploy-theme-second.bat`       | `./deploy-theme-second` |
| `grunt-theme-first.bat`         | `./grunt-theme-first`   |
| `grunt-theme-second.bat`        | `./grunt-theme-second`  |

## Remove

```
php bin/magento dg-util:uninstall-scripts
composer remove danielgheoltan/magento2-module-util
php bin/magento cache:flush
```

## Update

```
composer update danielgheoltan/magento2-module-util
php bin/magento cache:flush
```

## Commands

| Windows                        | Linux                        |
| ------------------------------ | ---------------------------- |
| `deploy.bat`                   | `./deploy`                   | 
| `deploy-backend.bat`           | `./deploy-backend`           |
| `deploy-frontend.bat`          | `./deploy-frontend`          |
| `deploy-grunt-theme.bat`       | `./deploy-grunt-theme`       |
| `deploy-grunt-theme-blank.bat` | `./deploy-grunt-theme-blank` |
| `deploy-grunt-theme-luma.bat`  | `./deploy-grunt-theme-luma`  |
| `deploy-theme.bat`             | `./deploy-theme`             |
| `deploy-theme-blank.bat`       | `./deploy-theme-blank`       |
| `deploy-theme-luma.bat`        | `./deploy-theme-luma`        |
| `di.bat`                       | `./di`                       |
| `grunt-theme.bat`              | `./grunt-theme`              |
| `grunt-theme-blank.bat`        | `./grunt-theme-blank`        |
| `grunt-theme-luma.bat`         | `./grunt-theme-luma`         |

## deploy

> <details>
>     <summary>Details</summary>
>
> 1. **Enables maintenance mode**
> 
>    `php bin/magento maintenance:enable`
>
> 2. **Deletes the contents of the following directories:**
>
>    * `generated`
>    * `pub/static/adminhtml`
>    * `pub/static/frontend`
>    * `var/cache`
>    * `var/page_cache`
>    * `var/view_preprocessed`
>
> 3. **Flushes cache storage**
>
>    `php bin/magento cache:flush`
>
> 4. **Updates required components**
>
>    `composer update`
>
> 5. **Upgrades the Magento application, DB data, and schema**
>
>    `php bin/magento setup:upgrade`
>
> 6. **Reindexes Data**
>
>    `php bin/magento indexer:reindex`
>
> 7. **Deploys static view files**
>
>    `php bin/magento setup:static-content:deploy en_US --no-html-minify -f`
>
> 8. **Disables maintenance mode**
>
>    `php bin/magento maintenance:disable`
> </details>

## deploy-frontend

> <details>
>     <summary>Details</summary>
>
> 1. **Enables maintenance mode**
>
>    `php bin/magento maintenance:enable`
>   
> 2. **Deletes the contents of the following directories:**
>
>    * `generated`
>    * `pub/static/frontend`
>    * `var/cache`
>    * `var/page_cache`
>    * `var/view_preprocessed`
>
> 3. **Flushes cache storage**
>
>    `php bin/magento cache:flush`
>
> 4. **Upgrades the Magento application, DB data, and schema**
>
>    `php bin/magento setup:upgrade`
>  
> 5. **Deploys static view files for frontend area**
>
>    `php bin/magento setup:static-content:deploy en_US --area="frontend" --no-html-minify -f`
>
> 6. **Disables maintenance mode**
>
>    `php bin/magento maintenance:disable`
> </details>

## deploy-backend

> <details>
>     <summary>Details</summary>
>
> 1. **Enables maintenance mode**
>
>    `php bin/magento maintenance:enable`
>
> 2. **Deletes the contents of the following directories:**
>
>    * `generated`
>    * `pub/static/adminhtml`
>    * `var/cache`
>    * `var/page_cache`
>    * `var/view_preprocessed`
>
> 3. **Flushes cache storage**
>
>    `php bin/magento cache:flush`
>
> 4. **Upgrades the Magento application, DB data, and schema**
>
>    `php bin/magento setup:upgrade`
>
> 5. **Deploys static view files for adminhtml area**
>
>    `php bin/magento setup:static-content:deploy en_US --area="adminhtml" --no-html-minify -f`
>
> 6. **Disables maintenance mode**
>
>    `php bin/magento maintenance:disable`
> </details>

## deploy-grunt-theme

> Usage:
>
> ```deploy-grunt-theme <theme> <locale> <grunt_theme>```
>
> <details>
>     <summary>Details</summary>
>
> 1. **Deletes the contents of the following directories:**
>
>    * `pub/static/frontend/<theme>/<locale>`
>    * `var/cache`
>    * `var/page_cache`
>    * `var/view_preprocessed/less/frontend/<theme>/<locale>`
>    * `var/view_preprocessed/pub/static/frontend/<theme>/<locale>`
>    * `var/view_preprocessed/source/frontend/<theme>/<locale>`
>
> 3. **Republishes symlinks to the source files to the `pub/static/frontend/` directory**
>
>    `grunt exec:<theme>`
>
> 4. **Deploys static view files**
>
>    `php bin/magento setup:static-content:deploy <locale> --theme="<theme>" --no-html-minify -f` 
>
> 5. **Tracks the changes in the source files and recompiles CSS files**
>
>    `grunt watch less:<grunt_theme>`
> </details>

## deploy-grunt-theme-blank

> <details>
>     <summary>Details</summary>
>
> \
> Executes `deploy-grunt-theme Magento/blank en_US blank`.
> </details>

## deploy-grunt-theme-luma

> <details>
>     <summary>Details</summary>
>
> \
> Executes `deploy-grunt-theme Magento/luma en_US luma`.
> </details>

## deploy-theme

> Usage:
>
> ```deploy-theme <theme> <locale>```
>
> <details>
>     <summary>Details</summary>
>
> 1. **Deletes the contents of the following directories:**
>
>     * `pub/static/frontend/<theme>/<locale>`
>     * `var/cache`
>     * `var/page_cache`
>     * `var/view_preprocessed/less/frontend/<theme>/<locale>`
>     * `var/view_preprocessed/pub/static/frontend/<theme>/<locale>`
>     * `var/view_preprocessed/source/frontend/<theme>/<locale>`
> 
> 2. **Deploys static view files**
>
>    `php bin/magento setup:static-content:deploy <locale> --theme="<theme>" --no-html-minify -f` 
> </details>

## deploy-theme-blank

> <details>
>     <summary>Details</summary>
>
> \
> Executes `deploy-theme Magento/blank en_US`.
> </details>
    
## deploy-theme-luma

> <details>
>     <summary>Details</summary>
>
> \
> Executes `deploy-theme Magento/luma en_US`.
> </details>

## di

> <details>
>     <summary>Details</summary>
>
> 1. **Enables maintenance mode**
>
>    `php bin/magento maintenance:enable`
>   
> 2. **Deletes the contents of the following directories:**
>
>    * `generated`
>    * `var/di`
>
> 3. **Flushes cache storage**
>
>    `php bin/magento cache:flush`
>
> 4. **Generates DI configuration and all missing classes that can be auto-generated**
>
>    `php bin/magento setup:di:compile`
>  
> 5. **Disables maintenance mode**
>
>    `php bin/magento maintenance:disable`
> </details>

## grunt-theme

> Usage:
>
> ```grunt-theme <grunt_theme>```
>
> <details>
>     <summary>Details</summary>
>
> \
> **Compiles CSS files** using the symlinks published in the `pub/static/frontend/` directory, **tracks the changes** in the source files and **recompiles CSS files**.
>
> `grunt less:<grunt_theme> && grunt watch less:<grunt_theme>`
> </details>

## grunt-theme-blank

> <details>
>     <summary>Details</summary>
>
> \
> Executes `grunt-theme blank`.
> </details>

## grunt-theme-luma

> <details>
>     <summary>Details</summary>
>
> \
> Executes `grunt-theme luma`.
> </details>
