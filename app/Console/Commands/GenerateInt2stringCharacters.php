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
    protected $signature = 'key:int2string-characters
        {--show : Display the characters instead of modifying the config file}
        {--c|characters=0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ : Generate with custom characters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the "support.int2string_characters" configuration';

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

        $this->setCharactersInConfigFile($characters);

        $this->laravel['config']['support.int2string_characters'] = $characters;

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
     * Set the characters in the config file.
     *
     * @param  string  $characters
     */
    protected function setCharactersInConfigFile($characters)
    {
        $file = $this->laravel->configPath().'/support.php';

        file_put_contents($file, str_replace(
            "'int2string_characters' => '".$this->laravel['config']['support.int2string_characters']."'",
            "'int2string_characters' => '{$characters}'",
            file_get_contents($file)
        ));
    }
}
