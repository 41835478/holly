<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateAssetsVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:version {--config=assets : The filename of the assets configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update assets version configuration';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = (string) $this->option('config');

        $assets = $this->laravel['config']->get($config);
        $revisioned = $this->revisionAssets($assets);

        if ($assets !== $revisioned) {
            $this->updateAssetsConfigFile($config, $revisioned);

            $this->laravel['config'][$config] = $revisioned;
        }

        $this->info('Assets configuration '.(is_null($assets) ? 'created!' : 'updated!'));
    }

    /**
     * Revision assets.
     *
     * @param  array|null  $assets
     * @return array
     */
    protected function revisionAssets($assets)
    {
        $newAssets = [];

        if (is_array($assets)) {
            foreach ($assets as $file => $value) {
                if (is_numeric($file)) {
                    $file = $value;
                    $value = '0';
                }

                $path = public_path($file);

                if (is_file($path)) {
                    $value = substr(md5_file($path), 0, 10);
                } else {
                    $this->error("Revisioning file [{$file}] failed.");
                }

                $newAssets[$file] = $value;
            }
        }

        return $newAssets;
    }

    /**
     * Update assets config file.
     *
     * @param  string  $config
     * @param  array  $assets
     */
    protected function updateAssetsConfigFile($config, array $assets)
    {
        file_put_contents(
            config_path($config.'.php'),
            sprintf("<?php\n\nreturn %s;\n", var_export($assets, true))
        );
    }
}
