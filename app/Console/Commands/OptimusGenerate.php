<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Jenssegers\Optimus\Energon;

class OptimusGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimus:generate {--show : Display the values instead of modifying files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Optimus prime values';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $values = array_combine(['prime', 'inverse', 'random'], Energon::generate());

        if ($this->option('show')) {
            return $this->printValues($values);
        }

        $this->setValuesInEnvironmentFile($values);

        $this->laravel['config']['optimus'] = $values;

        $this->printValues($values);
        $this->info('Optimus values set successfully.');
    }

    /**
     * Print Optimus values.
     *
     * @param  array  $values
     */
    protected function printValues($values, $method = 'comment')
    {
        foreach ($values as $key => $value) {
            $key = Str::title($key);
            $this->{$method}("{$key}: {$value}");
        }
    }

    /**
     * Set Optimus values in the environment file.
     *
     * @param  array  $values
     */
    protected function setValuesInEnvironmentFile($values)
    {
        $content = file_get_contents($this->laravel->environmentFilePath());

        foreach ($values as $key => $value) {
            $name = 'OPTIMUS_'.strtoupper($key);
            $text = "{$name}={$value}";

            $content = preg_replace("#{$name}=.*#", $text, $content, -1, $replaceCount);

            if (0 === $replaceCount) {
                $content .= $text.PHP_EOL;
            }
        }

        file_put_contents($this->laravel->environmentFilePath(), $content);
    }
}
