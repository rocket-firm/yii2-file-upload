<?php
namespace rocketfirm\fileupload;

use yii\web\UploadedFile;

/**
 * Interface UploadableInterface
 * @package rocketfirm\fileupload
 */
interface UploadableInterface
{
    /**
     * @param UploadedFile $file
     * @param string $attribute
     * @param bool $removeOld
     *
     * @return mixed
     */
    public function saveFile(UploadedFile $file, $attribute, $removeOld = true);

    /**
     * @param $attribute
     * @param bool $abs
     * @param string|null $suffix
     *
     * @return mixed
     */
    public function getFilePath($attribute, $abs = false, $suffix = null);

    /**
     * @param string $attribute
     * @param string|null $suffix
     *
     * @return mixed
     */
    public function deleteFile($attribute, $suffix = null);

    /**
     * @param bool $abs
     *
     * @return mixed
     */
    public function getStorageDir($abs = false);
}