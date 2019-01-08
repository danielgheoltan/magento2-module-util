# DG Util

## Introduction

Installs scripts that save you time and effort.

| Windows                  | Linux                  | Description |
| ------------------------ | ---------------------- | ----------- |
| `deploy.bat`             | `./deploy`             | ...         |
| `deploy-backend.bat`     | `./deploy-backend`     | ...         |
| `deploy-frontend.bat`    | `./deploy-frontend`    | ...         |
| `deploy-theme.bat`       | `./deploy-theme`       | ...         |
| `deploy-theme-blank.bat` | `./deploy-theme-blank` | ...         |
| `deploy-theme-luma.bat`  | `./deploy-theme-luma`  | ...         |
| `di.bat`                 | `./di`                 | ...         |
| `grunt-theme.bat`        | `./grunt-theme`        | ...         |
| `grunt-theme-blank.bat`  | `./grunt-theme-blank`  | ...         |
| `grunt-theme-luma.bat`   | `./grunt-theme-luma`   | ...         |

## Install

```
composer config repositories.dg/module-util git https://github.com/danielgheoltan/magento2-module-util.git
composer require dg/module-util:dev-master
php bin/magento setup:upgrade
php bin/magento module:enable DG_Util
php bin/magento cache:flush
php bin/magento dg-util:install-scripts
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
