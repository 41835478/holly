<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateApiTokenKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-token-key {--show : Display the key instead of modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the api token key';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        if ($this->option('show')) {
            return $this->comment($key);
        }

        $this->setKeyInEnvironmentFile($key);

        $this->laravel['config']['support.api.token.key'] = $key;

        $this->info("Api token key [$key] set successfully.");
    }

    /**
     * Generate a random key for the api token.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return str_random(32);
    }

    /**
     * Set the api token key in the environment file.
     *
     * @param  string  $key
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $content = file_get_contents($this->laravel->environmentFilePath());

        $text = 'API_TOKEN_KEY='.$key;

        $content = preg_replace('#API_TOKEN_KEY=.*#', $text, $content, -1, $replaceCount);

        if (0 === $replaceCount) {
            $content .= $text.PHP_EOL;
        }

        file_put_contents($this->laravel->environmentFilePath(), $content);
    }
}
