<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 2/22/17
 * Time: 12:18 PM
 */

namespace rocketfirm\fileupload;

use yii\web\UploadedFile;

/**
 * Interface UploadableInterface
 * @package app\components\interfaces
 */
interface UploadableInterface
{
    /**
     * @param UploadedFile $file
     * @param $attribute
     * @param bool $removeOld
     * @return mixed
     */
    public function saveFile(UploadedFile $file, $attribute, $removeOld = true);

    /**
     * @param $attribute
     * @param bool $abs
     * @param null $mode
     * @return mixed
     */
    public function getFilePath($attribute, $abs = false, $mode = null);

    /**
     * @param $attribute
     * @param null $mode
     * @return mixed
     */
    public function deleteFile($attribute, $mode = null);

    /**
     * @param bool $abs
     * @return mixed
     */
    public function getStorageDir($abs = false);
}