<?php

namespace libraries;

class FileEdit
{

    protected array $imgArr = [];
    protected string $directory;

    public function addFile(string|bool $directory = false): array
    {

        if (!$directory) $this->directory = $_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR;
        else $this->directory = $directory;

        foreach ($_FILES as $key => $file) {

            if (is_array($file['name'])) {

                $fileArr = [];

                for ($i = 0; $i < count($file['name']); $i++) {

                    if (!empty($file['name'][$i])) {

                        $fileArr['name'] = $file['name'][$i];
                        $fileArr['type'] = $file['type'][$i];
                        $fileArr['tmp_name'] = $file['tmp_name'][$i];
                        $fileArr['error'] = $file['error'][$i];
                        $fileArr['size'] = $file['size'][$i];

                        $resultName = $this->createFile($fileArr);

                        if ($resultName) $this->imgArr[$key][] = $resultName;

                    }
                }

            } else {

                if (!empty($file['name'])) {

                    $resultName = $this->createFile($file);

                    if ($resultName) $this->imgArr[$key] = $resultName;

                }
            }
        }

        return $this->getFiles();

    }

    public function getFiles(): array
    {

        return $this->imgArr;

    }

    protected function createFile(array $file): bool|string
    {

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

        $fileName = pathinfo($file['name'], PATHINFO_FILENAME);

        $fileName = (new TextModify())->translit($fileName);

        $fileName = $this->checkFile($fileName, $fileExtension);

        $fileFullName = $this->directory . $fileName;

        if ($this->uploadFile($file['tmp_name'], $fileFullName)) {

            return $fileName;

        } else {

            return  false;

        }
    }

    protected function uploadFile(string $tmpName, string $destination): bool
    {

        if (move_uploaded_file($tmpName, $destination)) return true;

        return false;

    }

    protected function checkFile(string $fileName, string $fileExtension, string|bool $fileLastName = false): string
    {

        if (!file_exists($this->directory . $fileName . $fileLastName))
            return $fileName . $fileLastName . '.' . $fileExtension;

        return $this->checkFile($fileName, '_' . hash('crc32', time()));

    }

}