<?php

namespace tests\unit\helpers;

use Codeception\Specify;
use Codeception\Test\Unit;
use rocketfirm\fileupload\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class FileHelperTest
 * @package tests\unit\helpers
 */
class FileHelperTest extends Unit
{
    use Specify;

    public function _before()
    {
        FileHelper::$storageDir = 'tests';
        \Yii::setAlias('@tests', dirname(__DIR__, 2));

        /**
         * Мокаем класс Security чтобы выдавал постоянныое имя при гереации случайно строки
         */
        $security = $this->getMockBuilder('yii\base\Security')->getMock();
        $security->expects($this->any())->method('generateRandomString')->with($this->logicalOr(2,
            5))->willReturn('temppath');
        \Yii::$app->set('security', $security);
    }

    public function _after()
    {
        $this->tester->cleanDir('tests/tmp');
    }

    public function testDownload()
    {

        $this->specify("Error download file", function () {
            $this->assertFalse(FileHelper::download('http://www.bad.url', 'tests/tmp/temp'));
        });

        $this->specify("Success download file", function () {
            $this->assertTrue(FileHelper::download('http://google.com/favicon.ico', 'tests/tmp/favicon.ico'));

            $this->assertFileExists('tests/tmp/favicon.ico');
        });

        $this->specify("Fail download file with try count", function () {
            $this->assertFalse(FileHelper::download('http://www.bad.url', 'tests/tmp/tmp', 2));
        });
    }

    public function testGenerateName()
    {
        $this->specify('Generate name without suffix', function () {
            $time = time();
            $this->assertEquals($time . 'temppath.jpg', FileHelper::generateName('jpg'));
        });

        $this->specify('Generate with suffix', function () {
            $time = time();

            $this->assertEquals($time . 'temppath-suffix.jpg', FileHelper::generateName('jpg', 'suffix'));
        });
    }

    public function testGenerateSubdir()
    {
        $this->specify('Create main directory if it is not exists', function () {
            FileHelper::generateSubdir('tests/tmp/test');
            $this->assertDirectoryExists('tests/tmp/test');
        });

        $this->specify('Create subdirectory', function () {
            $this->assertContains('temppath', FileHelper::generateSubdir('tests/tmp'));
            $this->assertDirectoryExists('tests/tmp/temppath');
        });
    }

    public function testGenerateSubdirWithRename()
    {
        $this->specify('Create subdirectory name with "ad" name have to rename', function () {
            $security = $this->getMockBuilder('yii\base\Security')->getMock();
            $security->expects($this->once())->method('generateRandomString')->with(2)->willReturn('ad');
            \Yii::$app->set('security', $security);

            $this->assertContains('da', FileHelper::generateSubdir('tests/tmp', false));
        });
    }

    public function testSaveUploaded()
    {
        $this->markTestIncomplete();
        $uploadedFile = new UploadedFile;
        $uploadedFile->error = UPLOAD_ERR_OK;
        $uploadedFile->name = "image.png";
        $uploadedFile->tempName = \Yii::getAlias('@tests') . '/_data/image.png';
        $uploadedFile->type = "image/png";
        $uploadedFile->size = 4096;

        $this->specify('SaveFile on upload', function () use ($uploadedFile) {
            $this->assertFileExists('tests/tmp/' .
                FileHelper::saveUploaded($uploadedFile, 'tmp', false)
            );
        });

        $this->specify('SaveFile on upload with subdir', function () use ($uploadedFile) {
            $this->assertFileExists('tests/tmp/' . FileHelper::saveUploaded($uploadedFile,
                    'tmp', true));
        });
    }

    public function testSaveFromUrl()
    {
        $this->specify('SaveFile on URL http://google.com/favicon.ico', function () {
            $this->assertFileExists('tests/tmp/' . FileHelper::saveFromUrl('https://google.com/favicon.ico',
                    'tmp', false));
        });

        $this->specify('SaveFile on URL http://google.com/favicon.ico with subdir', function () {
            $this->assertFileExists('tests/tmp/' . FileHelper::saveFromUrl('https://google.com/favicon.ico',
                    'tmp', true));
        });
    }

    public function testGetStoragePath()
    {
        $this->specify('Get relative path without sub dir', function () {
            $this->assertEquals('/tests', FileHelper::getStoragePath());
        });

        $this->specify('Get relative path with sub dir', function () {
            $this->assertEquals('/tests/tmp', FileHelper::getStoragePath(false, 'tmp'));
        });

        $this->specify('Get absolute path with sub dir', function () {
            $this->assertEquals(\Yii::getAlias('@tests') . '/tmp', FileHelper::getStoragePath(true, 'tmp'));
        });
    }

    public function testGetImageSize()
    {
        $this->specify('Get image size as null id file not found', function () {
            $this->assertNull(FileHelper::getImageSize('bat/path'));
        });

        $this->specify('Get image size 240*40', function () {
            $this->assertArraySubset([240, 40], FileHelper::getImageSize('tests/_data/image.png'));
        });
    }
}