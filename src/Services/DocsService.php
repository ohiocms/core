<?php

namespace Belt\Core\Services;

use Belt, View;
use Matrix\Exception;

class DocsService
{

    use Belt\Core\Behaviors\HasConfig;
    use Belt\Core\Behaviors\HasDisk;

    /**
     * @var string
     */
    protected $configPath = 'belt.docs';

    /**
     * @var string
     */
    protected $version;

    /**
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @param bool $stripped
     * @return mixed|string
     */
    public function getVersion($stripped = false)
    {
        return $stripped ? str_replace('.', '', $this->version) : $this->version;
    }

    /**
     *
     */
    public function generate($version)
    {

        $this->setVersion($version);

        if (!$this->getVersion()) {
            throw new \Exception('version required');
        }

        $raw_src_path = 'resources/docs/raw';

        $compiled_src_path = 'resources/docs/published';

        $image_copy_path = 'public/images/docs';

        $this->disk()->deleteDirectory($compiled_src_path);

//        $files = $this->disk()->allFiles($raw_src_path . '/img');
//
//        foreach ($files as $raw_path) {
//            $image_path = str_replace_first($raw_src_path . '/img', $image_copy_path, $raw_path);
//            $this->disk()->put($image_path, $this->disk()->get($raw_path));
//        }

        $files = $this->disk()->allFiles($raw_src_path);

        foreach ($files as $raw_path) {
            $filename = basename($raw_path);
            if (!str_contains($filename, '-md')) {
                continue;
            }
            $compiled_path = str_replace_first($raw_src_path, $compiled_src_path, $raw_path);
            $compiled_path = str_replace(['.md.blade.php', '.blade.php'], '.md', $compiled_path);
            $compiled_path = str_replace(['-md'], '', $compiled_path);
            $compiled_path = str_replace($this->getVersion(true), $this->getVersion(), $compiled_path);
            $this->disk()->put($compiled_path, $this->contents($raw_path));
        }
    }

    /**
     * @param $path
     * @return mixed|string
     */
    public function contents($path)
    {
        dump($path);

        //$contents = $this->disk()->get($path);

        $view_path = str_replace(["resources/docs/raw/{$this->getVersion(true)}/", '.md.blade.php', '.blade.php'], '', $path);
        $view_path = str_replace(['/'], '.', $view_path);
        $view_path = sprintf('belt-docs::%s.%s', $this->getVersion(true), $view_path);

        dump($view_path);

        $contents = View::make($view_path, ['version' => $this->getVersion()])->render();

//        foreach ((array) $this->config('vars', []) as $key => $value) {
//            $contents = str_replace(sprintf('{{%s}}', $key), $value, $contents);
//        }

        return $contents;
    }

}