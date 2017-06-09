# Holly

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ElfSundae/holly.svg?style=flat-square)](https://packagist.org/packages/elfsundae/holly)
[![Build Status](https://img.shields.io/travis/ElfSundae/holly/master.svg?style=flat-square)](https://travis-ci.org/ElfSundae/holly)
[![StyleCI](https://styleci.io/repos/70877647/shield)](https://styleci.io/repos/70877647)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/73870987-572f-4825-af66-2fc6efaebb5d.svg?style=flat-square)](https://insight.sensiolabs.com/projects/73870987-572f-4825-af66-2fc6efaebb5d)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/holly.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/holly)

**:warning: Work In Progress**

An app framework built with [Laravel][].

## Installation

- You can create a Holly based project using the Composer `create-project` command:

    ```sh
    composer create-project elfsundae/holly:dev-master myapp
    ```

- Or you may want to keep the Holly framework up to date for your application with Git:

    ```sh
    git remote add upstream git@github.com:ElfSundae/holly.git
    git fetch upstream --no-tags
    git merge upstream/master  # --allow-unrelated-histories
    git push origin master
    ```

## Development Notes

- [Configure Supervisor][] for queue workers.
- Configure cron job: `$ crontab  -u www -e`

    ```
    * * * * * /usr/bin/php /data/www/myapp/artisan schedule:run >> /dev/null 2>&1
    ```

## Useful Links

- [Faker](https://github.com/fzaninotto/Faker)
- [Optimus](https://github.com/jenssegers/optimus) ID obfuscation based on Knuth's multiplicative hashing method.
- [UUID Generator](https://github.com/ramsey/uuid)
- [Hashids](https://github.com/vinkla/laravel-hashids) Generate YouTube-like IDs from numbers.
- [DataTables](https://datatables.net)
- [Laravel DataTables](https://datatables.yajrabox.com)
- [Guzzle](http://docs.guzzlephp.org/en/latest/) An extensible PHP HTTP client.
- [Goutte](https://github.com/FriendsOfPHP/Goutte) PHP Web Scraper.
- [Intervention Image](http://image.intervention.io) PHP image handling and manipulation library.
- [Simple QrCode](https://www.simplesoftware.io/docs/simple-qrcode/zh)
- [App Store Receipt Validator](https://github.com/aporat/store-receipt-validator) PHP receipt validator for Apple iTunes, Google Play and Amazon App Store.
- [jQuery Form](http://malsup.com/jquery/form/)
- [SweetAlert2](https://github.com/limonte/sweetalert2)

[Laravel]: https://laravel.com
[Configure Supervisor]: https://laravel.com/docs/queues#supervisor-configuration
