<?php
namespace Framework;

use Psr\Http\Message\UploadedFileInterface;
use Intervention\Image\ImageManager;

class Upload
{

    protected $path;

    protected $formats;

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $file, ?string $oldFile = null):?string
    {
        if ($file->getError() === UPLOAD_ERR_OK) 
        {

        $this->delete($oldFile);
        $targetPath = $this->addCopySuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
        $dirname= pathinfo($targetPath, PATHINFO_DIRNAME);

        if (!file_exists($dirname)) 
        {
            mkdir($dirname, 777, true);
        }

        $file->moveTo($targetPath);
        $this->generateFormats($targetPath);
        return pathinfo($targetPath)['basename'];

        }

        return null;
    }

    private function addCopySuffix(string $targetPath) :string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        return $targetPath;
    }

    public function delete(?string $oldFile = null):void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $size) {
                if (file_exists($this->getPathWithSuffix($oldFile, $format))) {
                    unlink($this->getPathWithSuffix($oldFile, $format));
                }
            }
        }
    }

    private function getPathWithSuffix(string $path, string $suffix):string
    {
        $info = pathinfo($path);

        return $info['dirname'] . DIRECTORY_SEPARATOR .
        $info['filename'] . '_' . $suffix .'.'. $info['extension'];
    }

    private function generateFormats($targetPath)
    {
        foreach ($this->formats as $format => $size) {
            $destination = $this->getPathWithSuffix($targetPath, $format);

            $manager = new ImageManager(['driver'=>'gd']);
            [$width,$height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
        }
    }
}
