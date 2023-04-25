# **TinyUrl backend**


## Overview

TinyUrl is a URL shortening service just like Bitly.

From both website monthly (MLS & BDS):
- Traffic from short links: ~1M
- Post amount: 120-210
- Share-link-team size: 12 people <br />
  => Short link generated monthly: 210 * 12 = ~2520 <br />
  Redirect monthly: ~1M <br />
  => If short links only stored for 1 year DB limit should hold: 12 * 2520 * (1+365) = ~11M records
  (since each link have 1 record on table 'links' and 365 on 'logs')

For auditing (see amount of access) of each link, 3rd party analytic tools are used, 
a CronJob will crawl these data daily, this should reduce overhead for server 
since short links will be accessed by a big amount of traffic

Same reason of reducing overhead, redirects will be cached too


System design
![System design diagram](./infrastructure.png?raw=true "System design")

## Requirement & Installation

Create file .env with format like this

```sh
# Database information.
DB_DATABASE=
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_USERNAME=
DB_PASSWORD=
# Encryption keys
IV=<IV>
ENCRYPTION_KEY=<EncryptionKey>
ADMIN_EMAIL=
ADMIN_USERNAME=
ADMIN_PASSWORD=
```


Setup database, then run
```console
composer install
php artisan migrate
php artisan db:seed
sudo chmod -R 755 <folder>
chmod -R o+w <folder>/storage
php artisan key:generate
php artisan cache:clear 
php artisan config:clear
```


## License

Copyright © 2023 [MoliGroup](https://moligroup.co/), [MIT license](./LICENSE).
