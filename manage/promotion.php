<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("Asia/Ho_Chi_Minh");
require(dirname(__FILE__) . '/lib/PHPExcel.php');

if ($_REQUEST['act'] == 'setup')
{
    admin_priv('setup_promotion');

    $smarty->assign('ur_here', $_LANG['setup_promotion']);
    $smarty->display('promotion.htm');

}elseif ($_REQUEST['act'] == 'run') {
    admin_priv('setup_promotion');
     //upload csv
    if (isset($_POST['submit'])) {

        if(isset($_FILES['file2'])){
            if ($_FILES['file2']['error'] > 0)
            {
                 sys_msg('File bị lổi', 1, array(), false);

            }else{

                $array = excelToArray($_FILES['file2']['tmp_name'], $header=true);
                 //$objPHPExcel = new PHPExcel();
                 foreach ($array as $v) {
                    if($v['promotion'] == '' || $v['promotion'] == 0){
                        $is_km = $startday = $endday = $v['promotion'] = 0;
                    }else{
                        $is_km = 1;
                        $startday  = strtotime(PHPExcel_Style_NumberFormat::toFormattedString($v['startday'],'YYYY-MM-DD' ));
                        $endday  = strtotime(PHPExcel_Style_NumberFormat::toFormattedString($v['endday'],'YYYY-MM-DD' ));
                    }

                    $gift =  !empty($v['gift']) ? $v['gift'] : '';
                    $timegift = !empty($v['giftday']) ? $v['giftday'] : 0;


                    $sql = "UPDATE " . $ecs->table('goods') . " SET " .
                        "is_promote = '$is_km', " .
                        "promote_price = '$v[promotion]', " .
                        "goods_gift = '$gift', " .
                        "timegift = '$timegift', " .
                        "promote_start_date = '$startday', " .
                        "promote_end_date = '$endday' ".
                        "WHERE goods_sn = '$v[code]' AND is_on_sale = 1 AND is_delete = 0 LIMIT 1";
                    $db->query($sql);
                }
            }
        }


        clear_cache_files(); //clear old cache
    }


   $link[] = array('href' => 'promotion.php?act=setup', 'text' => $_LANG['setup_promotion']);
   sys_msg($_LANG['update_success'], 0, $link);
}

function readCSV($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

/*
|--------------------------------------------------------------------------
| Excel To Array
|--------------------------------------------------------------------------
| Helper function to convert excel sheet to key value array
| Input: path to excel file, set wether excel first row are headers
| Dependencies: PHPExcel.php include needed
*/
function excelToArray($filePath, $header=true){
        //Create excel reader after determining the file type
        $inputFileName = $filePath;
        /**  Identify the type of $inputFileName  **/
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        /**  Create a new Reader of the type that has been identified  **/
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        /** Set read type to read cell data onl **/
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);
        //Get worksheet and built array with first row as header
        $objWorksheet = $objPHPExcel->getActiveSheet();
        //excel with first row header, use header as key
        if($header){
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
            $headingsArray = $headingsArray[1];
            $r = -1;
            $namedDataArray = array();
            for ($row = 2; $row <= $highestRow; ++$row) {
                $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
                if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                    ++$r;
                    foreach($headingsArray as $columnKey => $columnHeading) {
                        $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                    }
                }
            }
        }
        else{
            //excel sheet with no header
            $namedDataArray = $objWorksheet->toArray(null,true,true,true);
        }
        return $namedDataArray;
}


 ?>