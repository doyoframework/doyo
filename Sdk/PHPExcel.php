<?php

namespace Sdk;

use Core\Util;
use PHPExcel_IOFactory;

class PHPExcel
{

    private static $handle = null;

    public function __construct()
    {
        if (self::$handle == null) {
            require_once 'PHPExcel/PHPExcel.php';
            self::$handle = new \PHPExcel();
        }
    }

    public function export()
    {

    }

    /**
     * @param $inputFileName
     * @param $type
     * @return array
     * @throws \Exception\HTTPException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function import($inputFileName, $type)
    {
        if ($type == 'xlsx' || $type == 'xls') {

            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

        } else if ($type == 'csv') {

            $objReader = PHPExcel_IOFactory::createReader('CSV')
                ->setDelimiter(',')
                ->setInputEncoding('UTF-8')
                ->setEnclosure('"')
                ->setSheetIndex(0);

            $objPHPExcel = $objReader->load($inputFileName);

        } else {
            throw Util::HTTPException('仅支持xls,xlsx,csv格式。');
        }

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        return $sheetData;
    }
}