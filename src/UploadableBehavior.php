<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 2/22/17
 * Time: 12:07 PM
 */

namespace rocketfirm\fileupload;


use app\components\interfaces\UploadableInterface;
use rocketfirm\engine\ActiveRecord;
use yii\base\Behavior;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class UploadableBehavior
 * @package app\components\behaviors
 */
class UploadableBehavior extends Behavior
{
    /**
     * @var Model|UploadableInterface
     */
    public $owner;

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'initUploadedFiles',
            ActiveRecord::EVENT_AFTER_DELETE => 'removeFiles',
        ];
    }

    /**
     * Инициализирует сущности UploadedFile
     */
    public function initUploadedFiles()
    {
        foreach ($this->fields as $fileField => $dbField) {
            $this->owner->$fileField = UploadedFile::getInstance($this->owner, $fileField) ?? UploadedFile::getInstanceByName($fileField);

            if (!empty($this->owner->$fileField)) {
                $this->owner->$dbField = $this->owner->saveFile($this->owner->$fileField, $dbField);
            }
        }
    }

    /**
     * Удаление файлов, при удалении модели
     */
    public function removeFiles()
    {
        foreach ($this->fields as $fileField => $dbField) {
            $this->owner->deleteFile($dbField);
        }
    }
}