# **TinyUrl backend**


## Overview

TinyUrl is a URL shortening service and a link management platform.

## Requirement & Installation

Create file .env with format like this

```sh
...
# Database information.
DB_DATABASE=shortlink
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_USERNAME=
DB_PASSWORD=
# Encryption keys
IV=<IV>
ENCRYPTION_KEY=<EncryptionKey>
...
```
The rest of .env is the same as a standard Laravel .env.


Setup database, then run
```console
composer install
php artisan migrate
```


## License

Copyright © 2023 [MoliGroup](https://moligroup.co/), [MIT license](./LICENSE).
