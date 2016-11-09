<?php
namespace Ohio\Core\Base\Commands;

use Ohio\Core\Base\Helper\OhioHelper;

use Illuminate\Console\Command;

/**
 * Class TestDBCommand
 *
 * Create and copy test DB in sqlite for easier testing
 *
 * @package TN\Cms\Command
 */
class TestDBCommand extends Command
{

    protected $signature = 'ohio-core:test-db';

    protected $description = 'create and seed test sqlite db';

    /**
     * Fire test DB create and copy command
     *
     * @return void|string
     */
    public function fire()
    {

        if ($this->option('env') != 'testing') {
            return $this->info('this command must with option --env=testing');
        }

        app()['config']->set('database.default', 'sqlite');
        app()['config']->set('database.connections.sqlite.database', 'database/testing/database.sqlite');

        $path = 'database/testing';

        $disk = OhioHelper::baseDisk();

        # replace test DB with empty DB
        $disk->delete("$path/database.sqlite");
        $disk->copy("$path/empty.sqlite", "$path/database.sqlite");

        # run migration on test DB
        $this->call('migrate', ['--env' => 'testing']);

        # seed the db
        $seeders = $disk->files('database/seeds');
        foreach ($seeders as $seeder) {
            if (str_contains($seeder, ['Seeder'])) {
                $seeder = str_replace(['database/seeds/', '.php'], '', $seeder);
                $this->call('db:seed', [
                    '--env' => 'testing',
                    '--class' => $seeder,
                ]);
            }
        }
    }

}