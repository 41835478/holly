<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateInt2stringCharacters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'int2string:generate-characters
        {--show : Display the characters instead of modifying the config file}
        {--c|characters=0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ : Generate with custom characters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the "support.int2string" configuration';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $characters = $this->generateRandomCharacters($this->option('characters'));

        if ($this->option('show')) {
            return $this->comment($characters);
        }

        $this->setCharactersInEnvironmentFile($characters);

        $this->laravel['config']['support.int2string'] = $characters;

        $this->info("Characters [$characters] set successfully.");
    }

    /**
     * Generate random characters.
     *
     * @return string
     */
    protected function generateRandomCharacters($characters)
    {
        return str_shuffle(count_chars($characters, 3));
    }

    /**
     * Set the characters in the environment file.
     *
     * @param  string  $characters
     */
    protected function setCharactersInEnvironmentFile($characters)
    {
        $content = file_get_contents($this->laravel->environmentFilePath());

        $text = 'INT2STRING_CHARACTERS='.$characters;

        $content = preg_replace('#^INT2STRING_CHARACTERS=.*$#m', $text, $content, -1, $replaceCount);

        if (0 === $replaceCount) {
            $content .= $text.PHP_EOL;
        }

        file_put_contents($this->laravel->environmentFilePath(), $content);
    }
}
