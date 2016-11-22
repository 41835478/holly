# holly-app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ElfSundae/holly-app.svg?style=flat-square)](https://packagist.org/packages/elfsundae/holly-app)
[![Build Status](https://img.shields.io/travis/ElfSundae/holly-app/master.svg?style=flat-square)](https://travis-ci.org/ElfSundae/holly-app)
[![StyleCI](https://styleci.io/repos/70921317/shield)](https://styleci.io/repos/70921317)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/73870987-572f-4825-af66-2fc6efaebb5d.svg?style=flat-square)](https://insight.sensiolabs.com/projects/73870987-572f-4825-af66-2fc6efaebb5d)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/holly-app.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/holly-app)

**:warning: Work In Progress**

<!-- MarkdownTOC -->

- [Installation](#installation)
- [Development Notes](#development-notes)
- [Useful Links](#useful-links)

<!-- /MarkdownTOC -->

## Installation

You can create a holly-app based project using the Composer `create-project` command:

```sh
composer create-project elfsundae/holly-app:dev-master myapp
```

Or you may want to keep the holly-app up to date for your application with Git:

```sh
git clone path/to/your/app/repo.git myapp
cd myapp
git remote add upstream git@git.coding.net:ElfSundae/holly-app.git
git fetch upstream
git merge upstream/master
git push origin master
```

## Development Notes

- [Configure Supervisor][] for queue worker.
- Delete all git tags:
  ```sh
  # delete all remote tags
  git tag -l | xargs -n 1 git push --delete origin

  # delete all local tags
  git tag -l | xargs git tag -d
  ```
- Configure cron job: `$ crontab  -u www -e`
  ```
  * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
  ```

## Useful Links

- [DataTables](https://datatables.net)
- [Laravel DataTables](https://datatables.yajrabox.com)
- [Guzzle](http://docs.guzzlephp.org/en/latest/)
- [Goutte Client](https://github.com/FriendsOfPHP/Goutte)
- [Intervention Image](http://image.intervention.io)
- [jQuery Form](http://malsup.com/jquery/form/)
- [Simple QrCode](https://www.simplesoftware.io/docs/simple-qrcode/zh)
- [SweetAlert2](https://github.com/limonte/sweetalert2)

[Configure Supervisor]: https://laravel.com/docs/queues#supervisor-configuration
