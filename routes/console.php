<?php

Artisan::command('ide-helper:generate-helpers', function () {
    if ($this->laravel->bound('command.ide-helper.generate')) {
        $this->call('ide-helper:generate');
        $this->call('ide-helper:models', ['-R' => true, '-N' => true]);
    }
})->describe('Generate IDE helper files');

Artisan::command('ide-helpers', function () {
    $this->call('clear-compiled');
    $this->call('ide-helper:generate-helpers');
    $this->call('optimize');
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

Artisan::command('sync-git-upstream {branch=master}', function ($branch) {
    foreach ([
        "git fetch upstream --no-tags",
        "git merge upstream/$branch",
        ] as $cmd) {
        $this->comment('$ '.$cmd);
        shell_exec($cmd);
    }
})->describe('Sync git upstream.');
