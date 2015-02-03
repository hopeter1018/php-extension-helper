<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hopeter1018\ExtensionHelper;

/**
 * Description of PhpExcel
 *
 * @version $id$
 * @author peter.ho
 */
class PhpExcel
{

    /**
     * @todo add "Header" cell
     * 
     * @param array $sheets <br />
     * array(<br />
     * &nbsp;&nbsp;'<b>Sheet Name 1</b>' => array(<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'<b>Column Header</b>' => '<b>fieldname</b>' or <b>Closure</b><br />
     * &nbsp;&nbsp;),<br />
     * );
     * @param string $creator
     */
    public static function Export($sheets, $filename = null, $creator = 'ZMS')
    {
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator($creator);

        $sheetIndex = 0;
        foreach ($sheets as $sheetName => $data) {
            if ($sheetIndex > 0) {
                $objPHPExcel->addSheet(new PHPExcel_Worksheet($objPHPExcel, $sheetName));
            }
            $objPHPExcel->setActiveSheetIndex($sheetIndex ++);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName);

            $index = 0;
            foreach ($data['columns'] as $header => $field) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($index ++, 1, $header);
            }

            $rowIndex = 2;
            foreach ($data['data'] as $sql_row) {
                $index = 0;
                foreach ($data['columns'] as $header => $field) {
                    if (is_callable($field)) {
                        $value = $field($sql_row);
                        if ($value instanceof PHPExcel_Cell_Hyperlink) {
                            $cellCoord = PHPExcel_Cell::stringFromColumnIndex($index) . $rowIndex;
                            $objPHPExcel->getActiveSheet()->setHyperlink($cellCoord, $value);
                            $objPHPExcel->getActiveSheet()->getStyle($cellCoord)->getFont()
                                ->setUnderline(true)
                                ->getColor()->setRGB('0000FF');

                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($index ++, $rowIndex, $value->getUrl());
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($index ++, $rowIndex, $value);
                        }
                    } elseif ($sql_row[$field] instanceof \DateTime) {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($index ++, $rowIndex, $sql_row[$field]->format(APP_MYSQL_DATE_TIME));
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($index ++, $rowIndex, $sql_row[$field]);
                    }
                }
                $rowIndex++;
            }
        }
        unset($field);
        unset($sql_row);
        unset($sheets);

        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $tempFilepath = tempnam(APP_SYSTEM_STORAGE, 'excel_');
        $objWriter->save($tempFilepath);
        $filename = (($filename === null) ? 'export' : $filename) . ".xls";

        header("Pragma: public");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); // browser must download file from server instead of cache
        header("Content-Type: application/force-download"); // force download dialog
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        if (strstr(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'), "MSIE")) { # workaround for IE filename bug with multiple periods / multiple dots in filename that adds square brackets to filename - eg. setup.abc.exe becomes setup[1].abc.exe
            $iefilename = preg_replace("/\./", "%2e", $filename, substr_count($filename, ".") - 1);
            header("Content-Disposition: attachment; filename=\"$iefilename\"");
        } else {
            header("Content-Disposition: attachment; filename=\"$filename\"");
        }
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($tempFilepath));
        ob_clean();
        flush();
        readfile($tempFilepath);
        sleep(0.5);
        unlink($tempFilepath);        
        exit;
    }

}
