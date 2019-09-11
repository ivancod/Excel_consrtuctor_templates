<?php
  require_once 'doc/classes/PHPExcel.php';
  require_once 'doc/classes/PHPExcel/Writer/Excel2007.php';

  $str = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI'];

  $field_id = db_array(mysql_query("SELECT * FROM `".PREFIX."excel_fields` WHERE `temp_id` = ".$tmp_id." ORDER BY `field_id`"));
  $tmpArgTitle = [];
  $tmpArgWidth = [];
  foreach ($field_id as $field) {
    array_push($tmpArgTitle, $argTitle[$field['field_id']]);
    array_push($tmpArgWidth, $argWidth[$field['field_id']]);
  }
  $objPHPExcel = new PHPExcel(); 
  $objPHPExcel->setActiveSheetIndex(0); 
  $bgColor = 'C1A191';
  $key = excelTitle($tmpArgTitle, $bgColor, $str, $objPHPExcel);

  excelWidth($tmpArgWidth, $str, $objPHPExcel);

  $styleArray = array(
      'font'  => array(
          'size'  => 14,
      ),
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('argb' => '333'),
          ),
      ),

  );
  $objPHPExcel->getActiveSheet()->getStyle('A1:'.$str[$key].'1')->getFont()->setBold('true');
  $objPHPExcel->getActiveSheet()->getStyle('A1:'.$str[$key].$sqlLength)->applyFromArray($styleArray);
  $objPHPExcel->getActiveSheet()->getStyle('H1:'.$str[$key].$sqlLength)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
  $objPHPExcel->getActiveSheet()->getStyle('B1:B'.$sqlLength)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
  $objPHPExcel->getActiveSheet()->getStyle('D1:D'.$sqlLength)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

  ###
  
  $e = 2;
  foreach($sql as $ps) 
  {
    $person = '';
    if($ps['person'] != 0){
      $sql = db_array(mysql_query("SELECT `name`,`surname` FROM `".PREFIX."personal` WHERE `id` = '{$ps['person']}'"));
      $person = $sql[0]['name'].' '.$sql[0]['surname'];
    }
    $text = $ps['text'];
    $text =str_replace(chr(13),' ',$text);
    $text = str_replace(chr(10),' ',$text);

    $rating = str_replace('.',',',$ps['rating']);

    if($ps['oid'] == 0) { 
      $OBJECT_ECHO = '---';
      } else {
      $OBJECT_ECHO = get_info($ps['oid'],"cobjects","title","id");
    }
    if($ps['add_info']  == 'undefined') $ps['add_info'] = '';
    if($ps['add_info2'] == 'undefined') $ps['add_info2'] = '';

  	if($ps['com_exist'] == 0) { $comExist =  'ні';} else  { $comExist =  'так';} 

    $argData = array( $ps['name'], $ps['phone'], $ps['nps'], date("d.m.Y  H:i", $ps['date']), $text, $comExist, $OBJECT_ECHO, $ps['wishes'], $ps['star1'], $ps['star2'], $ps['star3'], $ps['star4'], $ps['star5'], $ps['star6'], $person ,$ps['dignity'], $ps['limitations'], $ps['mail'], $rating, $ps['city'], $ps['born_date'], $ps['add_info'], $ps['add_info2']);
    $tmpArgData = [];
    foreach ($field_id as $field) array_push($tmpArgData, $argData[$field['field_id']]);
    excelData($tmpArgData, $e, $str, $objPHPExcel);
    $e++;
  }
$filename = $file."_".date("Y-m-d_H-i-s",time());
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
$objWriter->save('export/'.$filename.'.xlsx');