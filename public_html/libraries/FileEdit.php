<?php

namespace libraries;

class FileEdit
{

    protected array $imgArr = [];
    protected string $directory;
    protected bool $uniqueFile = true;

    public function addFile(string $directory = ''): array
    {

        $directory = trim($directory, ' /');

        $directory .= '/';

        $this->setDirectory($directory);

        foreach ($_FILES as $key => $file) {

            if (is_array($file['name'])) {

                $fileArr = [];

                foreach ($file['name'] as $i => $value) {

                    if (!empty($file['name'][$i])) {

                        $fileArr['name'] = $file['name'][$i];
                        $fileArr['type'] = $file['type'][$i];
                        $fileArr['tmp_name'] = $file['tmp_name'][$i];
                        $fileArr['error'] = $file['error'][$i];
                        $fileArr['size'] = $file['size'][$i];

                        $resultName = $this->createFile($fileArr);

                        if ($resultName) $this->imgArr[$key][$i] = $directory . $resultName;

                    }
                }

            } else {

                if (!empty($file['name'])) {

                    $resultName = $this->createFile($file);

                    if ($resultName) $this->imgArr[$key] = $directory . $resultName;

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

            return false;

        }
    }

    protected function uploadFile(string $tmpName, string $destination): bool
    {

        if (move_uploaded_file($tmpName, $destination)) return true;

        return false;

    }

    protected function checkFile(string $fileName, string $fileExtension, string|bool $fileLastName = false): string
    {

        if (!file_exists($this->directory . $fileName . $fileLastName) || !$this->uniqueFile)
            return $fileName . $fileLastName . '.' . $fileExtension;

        return $this->checkFile($fileName, $fileExtension,
            '_' . hash('crc32', time() . mt_rand(1, 1000)));

    }

    public function setUniqueFile($value): void
    {

        $this->uniqueFile = (bool)$value;

    }

    public function setDirectory(string $directory): void
    {

        $this->directory = $_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . $directory;

        if (!file_exists($this->directory)) mkdir($this->directory, 0777, true);

    }

}