<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/7/17
 * Time: 2:03 PM
 */

namespace rocketfirm\fileupload\widgets;


use rocketfirm\fileupload\assets\ImagePreviewAsset;
use rocketfirm\fileupload\UploadableInterface;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class ImagePreviewField
 *
 * @package app\components\widgets\imagepreview
 *
 * @property  string $fileField
 * @property string $previewImage
 *
 */
class ImagePreviewField extends InputWidget
{
    /**
     * @var UploadableInterface|Model
     */
    public $model;

    /**
     * @var string ID of image for preview file
     */
    public $previewImageId;

    /**
     * @var array
     */
    public $previewImageOptions = [];

    /**
     * Checks if model implements UploadableInterface
     *
     * @return bool
     */
    protected function isUploadable()
    {
        return $this->model instanceof UploadableInterface;
    }

    /**
     * @inheritdoc
     *
     * Provides asset registration and model validation
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->isUploadable()) {
            throw new InvalidConfigException('Model must implement UploadableInterface');
        }

        ImagePreviewAsset::register($this->view);

        if (empty($this->previewImageId)) {
            $this->previewImageId = $this->options['id'] . '-rf-preview';

            $this->view->registerJs(<<<JS
    $('#{$this->options['id']}').rfPreviewImage('preview-img-id');
JS
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->previewImage . $this->fileField;
    }

    /**
     * Renders preview image
     *
     * @return string
     */
    protected function getPreviewImage()
    {
        return Html::img($this->model->getFilePath('image'), ArrayHelper::merge(
            ['width' => '100%', 'id' => $this->previewImageId],
            $this->previewImageOptions
        ));
    }

    /**
     * Renders file input field
     *
     * @return string
     */
    protected function getFileField()
    {
        return Html::activeFileInput(
            $this->model, $this->attribute,
            ArrayHelper::merge(
                [
                    'class' => 'rf-img-file-preview',
                    'data-preview-img-id' => $this->previewImageId
                ],
                $this->options
            )
        );
    }

}