# DG Util

## Introduction

Installs scripts that save you time and effort.

## Install

```
composer config repositories.dg/module-util git https://github.com/danielgheoltan/magento2-module-util.git
composer require dg/module-util:dev-master
php bin/magento setup:upgrade
php bin/magento module:enable DG_Util
php bin/magento cache:flush
php bin/magento dg-util:install-scripts
php bin/magento dg-util:config
```

## Remove

```
php bin/magento dg-util:uninstall-scripts
composer remove dg/module-util
php bin/magento cache:flush
```

## Update

```
composer update dg/module-util
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


deploy
------

1. **Enables maintenance mode**

   `php bin/magento maintenance:enable`
   
2. **Deletes the contents of the following directories:**

   * `generated`
   * `pub/static/adminhtml`
   * `pub/static/frontend`
   * `var/cache`
   * `var/page_cache`
   * `var/view_preprocessed`

3. **Flushes cache storage**

   `php bin/magento cache:flush`

4. **Updates required components**

   `composer update`

5. **Upgrades the Magento application, DB data, and schema**

   `php bin/magento setup:upgrade`

6. **Reindexes Data**

   `php bin/magento indexer:reindex`

7. **Creates resized product images**

   `php bin/magento catalog:images:resize`
   
8. **Deploys static view files**

   `php bin/magento setup:static-content:deploy en_US --no-html-minify -f`
   
9. **Disables maintenance mode**

   `php bin/magento maintenance:disable`

deploy-frontend
---------------

1. **Enables maintenance mode**

   `php bin/magento maintenance:enable`
   
2. **Deletes the contents of the following directories:**

   * `generated`
   * `pub/static/frontend`
   * `var/cache`
   * `var/page_cache`
   * `var/view_preprocessed`

3. **Flushes cache storage**

   `php bin/magento cache:flush`

4. **Upgrades the Magento application, DB data, and schema**

   `php bin/magento setup:upgrade`
  
5. **Deploys static view files for frontend area**

   `php bin/magento setup:static-content:deploy en_US --area="frontend" --no-html-minify -f`
   
6. **Disables maintenance mode**

   `php bin/magento maintenance:disable`

deploy-backend
--------------

1. **Enables maintenance mode**

   `php bin/magento maintenance:enable`
   
2. **Deletes the contents of the following directories:**

   * `generated`
   * `pub/static/adminhtml`
   * `var/cache`
   * `var/page_cache`
   * `var/view_preprocessed`

3. **Flushes cache storage**

   `php bin/magento cache:flush`

4. **Upgrades the Magento application, DB data, and schema**

   `php bin/magento setup:upgrade`
  
5. **Deploys static view files for frontend area**

   `php bin/magento setup:static-content:deploy en_US --area="adminhtml" --no-html-minify -f`
   
6. **Disables maintenance mode**

   `php bin/magento maintenance:disable`

deploy-grunt-theme \<theme\> \<locale\> \<grunt_theme\>
-------------------------------------------------------

1. **Deletes the contents of the following directories:**
   * `pub/static/frontend/<theme>/<locale>`
   * `var/cache`
   * `var/page_cache`
   * `var/view_preprocessed/less/frontend/<theme>/<locale>`
   * `var/view_preprocessed/pub/static/frontend/<theme>/<locale>`
   * `var/view_preprocessed/source/frontend/<theme>/<locale>`

3. **Republishes symlinks to the source files to the `pub/static/frontend/` directory**

   `grunt exec:<theme>`
 
4. **Deploys static view files**

   `php bin/magento setup:static-content:deploy <locale> --theme="<theme>" --no-html-minify -f` 
  
5. **Tracks the changes in the source files and recompiles CSS files**

   `grunt watch less:<grunt_theme>`

deploy-grunt-theme-blank
------------------------

`deploy-grunt-theme Magento/blank en_US blank`

deploy-grunt-theme-luma
-----------------------

`deploy-grunt-theme Magento/luma en_US luma`

deploy-theme \<theme\> \<locale\>
---------------------------------

1. **Deletes the contents of the following directories:**
   * `pub/static/frontend/<theme>/<locale>`
   * `var/cache`
   * `var/page_cache`
   * `var/view_preprocessed/less/frontend/<theme>/<locale>`
   * `var/view_preprocessed/pub/static/frontend/<theme>/<locale>`
   * `var/view_preprocessed/source/frontend/<theme>/<locale>`
 
2. **Deploys static view files**

   `php bin/magento setup:static-content:deploy <locale> --theme="<theme>" --no-html-minify -f` 

deploy-theme-blank
------------------

`deploy-theme Magento/blank en_US`

deploy-theme-luma
-----------------

`deploy-theme Magento/luma en_US`

grunt-theme \<grunt_theme\>
---------------------------

**Compiles CSS files** using the symlinks published in the `pub/static/frontend/` directory, **tracks the changes** in the source files and **recompiles CSS files**.

`grunt less:<grunt_theme> && grunt watch less:<grunt_theme>`

grunt-theme-blank
-----------------

`grunt-theme blank`

grunt-theme-luma
----------------

`grunt-theme luma`
