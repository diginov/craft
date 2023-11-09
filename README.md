# Craft CMS Starter

## Start a new project with DDEV

Read the DDEV [documentation](https://ddev.readthedocs.io/) to install it before proceeding.

```
mkdir my-craft-project
cd my-craft-project
ddev config --project-type=craftcms --php-version=8.2 --database=mariadb:10.6 --timezone=America/Toronto
ddev composer create -y --no-install --no-scripts diginov/craft
ddev composer install
ddev craft install
ddev launch /admin
```
