<?php

namespace rocketfirm\fileupload\helpers;

use yii\base\Exception;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

/**
 * Class FileHelper
 *
 * Includes methods for working with
 *
 * @package rocketfirm\fileupload\helpers
 */
class FileHelper extends BaseFileHelper
{
    public static $storageDir = 'media';

    /**
     * Download file from url or path and save it to $path
     *
     * @param string $url
     * @param string $path
     * @param int $tryCount
     *
     * @return bool
     */
    public static function download($url, $path, $tryCount = 1)
    {
        $content = @file_get_contents($url);
        if ($content === false) {
            $tryCount--;
            if ($tryCount) {
                return self::download($url, $path, $tryCount);
            }

            return false;
        }
        file_put_contents($path, $content);

        return true;
    }

    /**
     * @param $ext
     * @param null $suffix
     *
     * @return string
     */
    public static function generateName($ext, $suffix = null)
    {
        if (!$suffix) {
            return time() . \Yii::$app->security->generateRandomString(5) . '.' . $ext;
        }

        return time() . \Yii::$app->security->generateRandomString(5) . '-' . $suffix . '.' . $ext;
    }

    /**
     * @param $path
     * @param bool $check
     *
     * @return string
     */
    public static function generateSubdir($path, $check = true)
    {
        if ($check && !is_dir($path)) {
            self::mkdir($path, 0777, true);
        }
        $subdir = \Yii::$app->security->generateRandomString(2);
        //images with "ad" in path get blocked by adblocker
        if ($subdir === 'ad') {
            $subdir = 'da';
        }
        if ($path && $check && !is_dir($path . '/' . $subdir)) {
            self::mkdir($path . '/' . $subdir, 0777, true);
        }

        return $subdir;
    }

    /**
     * @param UploadedFile $file
     * @param string $path
     * @param bool $subdir
     *
     * @return string
     * @throws \Exception
     */
    public static function saveUploaded(UploadedFile $file, $path, $subdir = true)
    {
        $path = static::getStoragePath(true, $path);
        if ($subdir) {
            $subdir = static::generateSubdir($path);
        } else {
            $subdir = '';
            if (!is_dir($path)) {
                if (!is_dir(dirname($path))) {
                    self::mkdir(dirname($path), 0775, true);
                }
                self::mkdir($path, 0775, true);
            }
        }
        $newName = static::generateName($file->extension);
        $result = $subdir ? $subdir . '/' . $newName : $newName;
        $saved = $file->saveAs($path . '/' . $result);
        if (!$saved) {
            throw new \Exception('Could not save uploaded file: ' . $file->error . ' to path ' . $path . '/' . $result);
        }

        return $result;
    }

    /**
     * @param string $url
     * @param string $path
     * @param bool $subdir
     *
     * @return string
     * @throws Exception
     */
    public static function saveFromUrl($url, $path, $subdir = true)
    {
        $path = static::getStoragePath(true, $path);
        if ($subdir) {
            $subdir = static::generateSubdir($path);
        } else {
            $subdir = '';
            if (!is_dir($path)) {
                if (!is_dir(dirname($path))) {
                    self::mkdir(dirname($path), 0775, true);
                }
                self::mkdir($path, 0775, true);
            }
        }
        $rawExt = pathinfo($url, PATHINFO_EXTENSION);
        $extension = '';
        if ($rawExt) {
            $extension = $rawExt;
        }

        $newName = static::generateName($extension);

        $result = $subdir ? $subdir . '/' . $newName : $newName;

        $content = file_get_contents($url);
        if ($content === false) {
            throw new Exception('Could not load file from url: ' . $url);
        }

        $saved = file_put_contents($path . '/' . $result, $content);

        if (!$saved) {
            throw new Exception('Could not save file from url:' . $path . '/' . $result);
        }

        return $result;
    }

    /**
     * Get path to save file
     *
     * @param bool $abs
     * @param null $name
     *
     * @return bool|string
     */
    public static function getStoragePath($abs = false, $name = null)
    {
        if ($abs) {
            $path = \Yii::getAlias('@' . static::$storageDir);
        } else {
            $path = '/' . self::$storageDir;
        }
        if (!$name) {
            return $path;
        } else {
            return $path . '/' . trim($name, '/');
        }
    }

    /**
     * Get image sizes
     *
     * @param string $path
     * @param bool $makeAbs
     *
     * @return array|null
     */
    public static function getImageSize($path, $makeAbs = false)
    {
        $fullPath = $makeAbs ? (\Yii::getAlias('@webroot') . $path) : $path;
        if (!file_exists($fullPath)) {
            return null;
        } else {
            $size = getimagesize($fullPath);

            return [$size[0], $size[1]];
        }
    }

    /**
     * Remove file from directory
     *
     * @param string $dir
     */
    public static function rmdirContent($dir)
    {
        if (!$dh = opendir($dir)) {
            return;
        }
        while (false !== ($obj = readdir($dh))) {
            if ($obj === '.' || $obj === '..') {
                continue;
            }
            if (is_dir($dir . '/' . $obj)) {
                self::rmdirContent($dir . '/' . $obj);
            } else {
                unlink($dir . '/' . $obj);
            }
        }
        closedir($dh);
    }

    /**
     * Create directory
     *
     * @param string $pathname
     * @param int $mode
     * @param bool $recursive
     * @param resource|null $context
     *
     * @return bool
     * @throws Exception
     */
    public static function mkdir($pathname, $mode = 0777, $recursive = false)
    {
        if (!mkdir($pathname, $mode, $recursive) && !is_dir($pathname)) {
            throw new Exception('Error on create directory ' . $pathname);
        }

        return true;
    }
}
