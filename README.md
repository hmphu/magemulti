# MAGENTO MULTI CLIENTS

> Something that similar to Magento Go service :)
> 
> This extension allow you host multiple clients on the same Magento codebase.
> Every single client has their own local.xml and modules/*.xml directory. That means you can setting separate databases, caching services and modules per clients.

## I. Installation

### Via modman

```bash
modman clone https://github.com/hmphu/magemulti.git
```

### Via composer

```json
{
    "require": {
        "hmphu/magemulti": "*",
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/hmphu/magemulti.git"
        }
    ]
}
```

## II. Structure

Every clients will have seperate `config, media, cache, report, ...` directories under the `clients` directory. This prevents file collisions and lets you use a single CDN domain.

### Config directories

```
{root}/clients/client1/etc
{root}/clients/client2/etc
...
```

### Media directories

```
{root}/clients/client1/media
{root}/clients/client2/media
...
```

### Var directories

```
{root}/clients/client1/var
{root}/clients/client1/var/cache
{root}/clients/client1/var/report
{root}/clients/client1/var/...

{root}/clients/client2/var
{root}/clients/client2/var/cache
{root}/clients/client2/var/report
{root}/clients/client2/var/...
...
```

## III. Server settings

1) **The `CLIENT_CODE` environment variable**

To make Magento work with the correct database and client's folder I had to modify the `index.php` and `Mage.php` files. This is done by two git patches in this repository and it will checks the `CLIENT_CODE` variable to know wich client this visited site is.

See index.php  Mage::app()/Mage::run() is intialized.

```
require_once MAGENTO_ROOT . '/app/MageMulti.php';
Mage::run(MageMulti::getRunCode(), MageMulti::getRunType(), MageMulti::getRunOptions());
```

2) **NGINX configuration**

```
server {
  listen 80;
  server_name foostore.dev;
  root /var/www;
  location ~ .php$ {
    fastcgi_pass  unix:/var/run/php5-fpm.sock;
    fastcgi_param CLIENT_CODE foostore;
  }
  .....
}
```
Most of this is typical Magento Nginx config. The important lines are: `fastcgi_param CLIENT_CODE foostore;`

3) **APACHE configuration**

```
<VirtualHost *:80>
  ServerName foostore.dev
  DocumentRoot /var/www
  SetEnv CLIENT_CODE foostore
</VirtualHost>
```
Most of this is typical Magento Apache config. The important lines are: `SetEnv CLIENT_CODE foostore`

*When you setup new domain and visit the site it will open the Magento Installation page and you can continue [setup your magento site](http://devdocs.magento.com/guides/m1x/install/installing_install.html). The module will create new directories and new `local.xml` file under the `clients` folder*

## IV. CRON configuration

Because Magento need to known the `CLIENT_CODE` to run exactly site so I had to created new `mcron.php` and `mcron.sh` files. This will looks for folders in `clients` folder which are `CLIENT_CODE`

So you have to setup your crontab to run `mcron.php` and `mcron.sh` instead of the default magento files (they are `cron.php` and `cron.sh`)

Example:

```bash
*/5 * * * * sh /var/www/mcron.sh
```

## V. To Do

- Tests and CI
- Shell script
- Make it work with [Aoe_Scheduler](https://github.com/AOEpeople/Aoe_Scheduler) module
- Bash script to create new client sites