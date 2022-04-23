The skeleton of [Yii 2](https://www.yiiframework.com) application best for rapidly creating your projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://img.shields.io/packagist/v/ensostudio/yii2-app.svg)
](https://packagist.org/packages/ensostudio/yii2-app)
[![Total Downloads](https://img.shields.io/packagist/dt/ensostudio/yii2-app.svg)](https://packagist.org/packages/ensostudio/yii2-app)

DIRECTORY STRUCTURE
-------------------

- `assets`        the source scripts, styles and etc.
- `config`        the application configurations
- `messages`      the I18n translations
- `migrations`    the database migrations (namespace `app\migrations`)
- `modules`       the application modules (namespace `app\modules`)
- `public`        the front-end entry script and assets
  - `backend`     the backend-end entry script and assets
- `runtime`       the cache and logs
- `src`           the PHP sources (classes, interfaces and traits in namespace `app`)
  - `commands`    the CLI controllers (running by `yii` script)
  - `controllers` the Web controllers
  - `models`      the models
- `tests`         the unit tests
- `vendor`        the required 3rd-party packages
- `views`         the views/templates
  - `mails`       the e-mail views
  - `layouts`     the page layouts

    
REQUIREMENTS
------------

The minimum requirement by this project template that your server supports PHP 7.4.

INSTALLATION
------------

### Install via Composer

If you do not have Composer, you may install it by following the instructions at
[getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~bash
composer create-project --prefer-dist ensostudio/yii2-app your-app
~~~

Now you should be able to access the application through the following URL, assuming `your-app` is the directory
directly under the Web root.

~~~
http://localhost/your-app/public/
~~~

CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data.

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config` directory to customize your application as required.

TESTING
-------

Tests are located in `tests` directory. They are developed with [PHPUnit](https://phpunit.readthedocs.io).

Edit the file `config/test.php` and `tests/bootstrap.php` to configure your test application.

Tests can be executed by running

```bash
composer run tests
```
