<?php

namespace iutbay\yii2\mm\components;

use Yii;
use yii\helpers\Url;

class FileSystem extends \yii\base\Component
{

    /**
     * @var \League\Flysystem\Filesystem
     */
    public $fs;

    /**
     * @var string
     */
    public $directorySeparator = '/';

    /**
     * @var boolean
     */
    public $local;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->local = ($this->fs instanceof \creocoder\flysystem\LocalFilesystem);
    }

    /**
     * @param string $path
     * @param boolean $recursive
     * @return array
     */
    public function listContents($path = '', $recursive = false)
    {
        $contents = $this->fs->listContents($path, $recursive);
        return $this->filterContents($contents, $recursive);
    }

    /**
     * @param array $contents
     * @return array
     */
    protected function filterContents($contents, $recursive = false)
    {
        $new = [];
        foreach ($contents as $f) {
            if ($recursive && isset($f['type']) && $f['type'] === 'dir') {
                continue;
            }
            if (isset($f['basename'])) {
                if (preg_match('#^\.#', $f['basename']))
                    continue;
            }
            if (isset($f['extension'])) {
                if (in_array($f['extension'], ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                    //$f['thumb'] = Url::to(Yii::$app->imageCache->thumbSrc($this->sourceUrl . '/' . $f['path']), true);
                }
            }
            $new[] = $f;
        }
        return $new;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->fs, $method], $parameters);
    }

}
