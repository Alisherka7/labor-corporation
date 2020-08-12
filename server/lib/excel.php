<?php
class ExcelNode
{
    public $eng;
    public $num;
    public $data;
}

include_once "./lib/PHPExcel/Classes/PHPExcel.php";
// include_once (Ss_SOURCES . "Tools.php");
/**

 * IsFile
 * GetEEFromEI
 * GetNumSumByArray
 * DivideDataForSolicitudExcel
 * TranslateSolicitudDatas
 * SelectSolicitudByName
 * GetWithoutSeparator
 */

/**
 * formDivision 폼 이름에 따라 다른 값 추가
 * 수정필요
 */

function GetExcel($bag)
{
    $originExcelPath = "./resources/excel.xls";
    $path = "./uploads-dummy/excel.xls";
    // A == 65
    // Z == 90
    $alphabets = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $datas = [];
    $count = 0;
    foreach ($bag['items'] as $item_key => $item_value) {
        $key_data = [];
        $value_data = [];

        $key_data['eng'] = $alphabets[$count];
        $key_data['num'] = '1';
        $key_data['data'] = $item_key;

        $value_data['eng'] = $alphabets[$count];
        $value_data['num'] = '2';
        $value_data['data'] = $item_value;
        $datas[] = $key_data;
        $datas[] = $value_data;
        $count++;
    }
    $objPHPExcel = SetExcelObjectByArray($originExcelPath, $datas);
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    $objWriter->save($path);

    return $path;
}

function GetCover($bag)
{

    $originExcelPath = './resources/cover.xls';
    $path = './uploads-dummy/cover.xls';
    $datas = [];
    // $datas[] = ["eng"=>"B", "num"=> "11", "data"=> $sendTime];
    $datas[] = ["eng" => "F", "num" => "11", "data" => $bag["managerContact"]];
    $datas[] = ["eng" => "B", "num" => "12", "data" => $bag["managerFullName"]];
    $datas[] = ["eng" => "B", "num" => "19", "data" => $bag["corpFax"]];
    $datas[] = ["eng" => "F", "num" => "19", "data" => $bag["corpName"]];
    $datas[] = ["eng" => "B", "num" => "20", "data" => $bag["corpContact"]];
    $datas[] = ["eng" => "C", "num" => "21", "data" => $bag["pageCount"]];
    $datas[] = ["eng" => "B", "num" => "23", "data" => $bag["companyName"]];
    $datas[] = ["eng" => "C", "num" => "26", "data" => $bag["formName"]];
    $objPHPExcel = SetExcelObjectByArray($originExcelPath, $datas);

    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

    $objWriter->save($path);

    return $path;
}

function SetExcelObjectByArray($originFilePath, $dataArray)
{
    $objReader = PHPExcel_IOFactory::createReaderForFile($originFilePath);
    $obj = $objReader->load($originFilePath);
    $obj->setActiveSheetIndex(0);
    $nodes = [];

    foreach ($dataArray as $value) {
        $node = new ExcelNode();
        $node->eng = $value["eng"];
        $node->num = $value["num"];
        $data = $value["data"];
        if(getType($data) == 'array'){
            $data = implode($data, ', ');
        }
        $node->data = $data;

        $nodes[] = $node;
    }
    $obj->setActiveSheetIndex(0);
    $sheet = $obj->getActiveSheet();

    foreach ($nodes as $node) {
        
        $sheet->setCellValueExplicit($node->eng . $node->num, $node->data . "", PHPExcel_Cell_DataType::TYPE_STRING);
    }
    return $obj;
}

// datarray
// eng, num
function GetArrayByExcelDataArray($filePath, &$dataArray)
{
    $obj = new PHPExcel();

    $objReader = PHPExcel_IOFactory::createReaderForFile($filePath);
    $obj = $objReader->load($filePath);
    $obj->setActiveSheetIndex(0);
    $nodes = [];

    $obj->setActiveSheetIndex(0);
    $sheet = $obj->getActiveSheet();

    foreach ($dataArray as $key => $value) {
        $data = $sheet->getCell($value["eng"] . $value["num"])->getFormattedValue();
        $dataArray[$key]["data"] = $data;
    }

    return $obj;
}

function GetExcelTopHighestColumn($sheet)
{

    $alphabet = "A";
    for ($i = 1; $i <= 18278; $i++) {
        $data = $sheet->getCell($alphabet . "1");

        $alphabet++;
        if ($data == "") {
            break;
        }
    }
    return $i - 1;
}

function GetExcelLeftHighestByFilePath($filePath, $col)
{

    $objReader = PHPExcel_IOFactory::createReaderForFile($filePath);
    $obj = $objReader->load($filePath);
    $obj->setActiveSheetIndex(0);
    $nodes = [];

    $obj->setActiveSheetIndex(0);
    $sheet = $obj->getActiveSheet();
    return $sheet->getHighestRow($col);

}

function GetExcelNodes($arr_top, $left_count, $read_data)
{
    // ADD_TOP
    $top_count = count($arr_top);
    $nodes = [];
    if ($left_count == 0) {
        $left_count = 1;
    }
    $virtual_arr_top = [];
    $arr_top_to_merge = [];

    foreach ($read_data as $read_key => $read_value) {
        $is_exist = false;

        foreach ($arr_top as $top_value) {
            if ($top_value == $read_key) {
                $is_exist = true;

                break;
            }
        }

        if ($is_exist == false) {
            $arr_top_to_merge[] = $read_key;

            $node = new ExcelNode();
            $node->data = $read_key;
            $node->eng = GetEEFromEI($top_count + 1);
            $node->num = '1';
            $nodes[] = $node;
            $top_count++;
        }
    }

    // MERGE
    $virtual_arr_top = array_merge($arr_top, $arr_top_to_merge);

    // ADD_ROW
    foreach ($read_data as $read_key => $read_data) {
        foreach ($virtual_arr_top as $virtual_key => $virtual_value) {
            // 가상의 폼네임이 읽은 폼네임과 같을 때
            if ($virtual_value == $read_key) {
                $node = new ExcelNode();
                $node->num = $left_count + 1;
                $node->data = $read_data;
                $node->eng = GetEEFromEI($virtual_key + 1);
                $nodes[] = $node;
                break;
            }
        }
    }

    return $nodes;
}

function GetExcelAbc($path)
{
    $corpLeftLen = GetExcelLeftHighestByFilePath($path, "A");
    $tempDataArray = [];

    for ($i = 2; $i < $corpLeftLen + 1; $i++) {
        $data["eng"] = "B";
        $data["num"] = $i;
        $tempDataArray[] = $data;
        $data["eng"] = "C";
        $data["num"] = $i;
        $tempDataArray[] = $data;
        $data["eng"] = "D";
        $data["num"] = $i;
        $tempDataArray[] = $data;
        $data["eng"] = "E";
        $data["num"] = $i;
        $tempDataArray[] = $data;
        $data["eng"] = "F";
        $data["num"] = $i;
        $tempDataArray[] = $data;
    }

    GetArrayByExcelDataArray($path, $tempDataArray);

    $r = [];
    foreach ($tempDataArray as $value) {
        $sendData = [];
        $sendData["eng"] = $value["eng"];
        $sendData["num"] = $value["num"];
        $sendData["data"] = $value["data"];
        $r[] = $sendData;
    }
    return $r;
}
