<?php

Artisan::command('generate-ide-helpers', function () {
    if ($this->laravel->bound('command.ide-helper.generate')) {
        $this->call('clear-compiled');
        $this->call('ide-helper:generate');
        $this->call('ide-helper:models', ['-R' => true, '-N' => true]);
    }
})->describe('Generate IDE helper files');
