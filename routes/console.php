<?php

Artisan::command('generate-ide-helpers', function () {
    if ($this->laravel->bound('command.ide-helper.generate')) {
        $this->call('clear-compiled');
        $this->call('ide-helper:generate');
        $this->call('ide-helper:models', ['-R' => true, '-N' => true]);
    }
})->describe('Generate IDE helper files');

Artisan::command('db-backup', function () {
    $database = config('database.default');

    $this->call('db:backup', [
        '--database' => $database,
        '--destination' => 'local',
        '--compression' => 'gzip',
        '--destinationPath' => 'db-backup/'.$database.'_'.date('Ymd_His').'.sql',
    ]);
})->describe('Backup the default database.');
