<?php

/**
 * Created by PhpStorm.
 * User: Orlov
 * Time: 0:20
 */
class DeleteFiles {

    /**
     * @var string
     */
    protected $strParentDir;

    /**
     * @var array
     */
    protected $arrDirTree = [];

    /**
     * @var array
     */
    protected $arrRemovedFiles = [];

    /**
     * Принимаем только строку существующей директории и строим дерево папок
     * DeleteFiles constructor.
     * @param $dir
     * @throws Exception
     */
    public function __construct($dir) {
        if (!is_string($dir) || !is_dir($dir)) {
            throw new Exception('Directory error!');
        }

        if (substr($dir, -1) !== '/') {
            $dir .= '/';
        }

        $this->strParentDir = $this->arrDirTree[] = $dir;
        self::getTree($dir);
    }

    /**
     * Рекурсивно получаем массив вложенных директорий и помещаем в $this->arrDirTree
     * @param $dir
     */
    private function getTree($dir) {
        foreach (new DirectoryIterator($dir) as $name => $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot() && substr($fileInfo->getBasename(), 0, 1) !== '.') {
                $this->arrDirTree[] = $strDir = $fileInfo->getRealPath() . '/';
                self::getTree($strDir);
            }
        }
    }

    /**
     * Удаляем файлы с заданным расширением
     * @param null $strExt
     * @return array
     * @throws Exception
     */
    public function removeFilesByExt($strExt = null) {
        if ($strExt === null || !is_string($strExt)) {
            throw new Exception("Not string!!!");
        }

        foreach ($this->arrDirTree as $folder) {
            $files = glob("$folder*.$strExt");
            if (!empty($files)) {
                array_map("unlink", $files);
                $this->arrRemovedFiles = array_merge($this->arrRemovedFiles, $files);
            }
        }

        return $this->arrRemovedFiles;
    }
}