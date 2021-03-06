<?php
namespace rocketfirm\fileupload;

use rocketfirm\fileupload\helpers\FileHelper;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Class Uploadable
 *
 * @package rocketfirm\fileupload
 *
 * @property ActiveRecord $this
 */
trait Uploadable
{
    /**
     * Save file after upload and set attribute value
     *
     * @param UploadedFile $file
     * @param $attribute
     * @param bool $removeOld
     *
     * @return string
     */
    public function saveFile(UploadedFile $file, $attribute, $removeOld = true)
    {
        if ($removeOld) {
            $this->deleteFile($attribute);
        }
        $newName = FileHelper::saveUploaded($file, $this->tableName());
        $this->$attribute = $newName;

        return $newName;
    }

    /**
     * Get path to place where file was saved
     *
     * @param string $attribute
     * @param bool $abs
     * @param string $suffix
     *
     * @return string|bool string filename or false if there's no image
     */
    public function getFilePath($attribute, $abs = false, $suffix = null)
    {
        if (!$this->$attribute) {
            return false;
        }
        $path = $this->getStorageDir($abs) . '/';

        $pathinfo = pathinfo($this->$attribute);

        return $path . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . $suffix . '.' . $pathinfo['extension'];
    }

    /**
     * @param $attribute
     * @param null $suffix
     *
     * @return void
     */
    public function deleteFile($attribute, $suffix = null)
    {
        $path = $this->getFilePath($attribute, true, $suffix);
        if ($path && file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @param bool $abs
     *
     * @return string path
     */
    public function getStorageDir($abs = false)
    {
        return FileHelper::getStoragePath($abs, $this->tableName());
    }
}
