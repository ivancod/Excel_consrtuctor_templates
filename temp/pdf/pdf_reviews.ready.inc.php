<?php

$header = '
<style>
  .h1 {font-size: 30px;border: none;}
  .logo {max-height: 50px;background: #c1a191;}
  table {margin-top: 20px;width: 100%;border-collapse: collapse;}
  th, td {border: 1px solid grey; padding: 2.5px 0;}
  .logo_client {border: none;text-align: left;}
  .logo_rvz {border: none;text-align: right;}
  .review__title {width: 100%;text-align: justify-content;}
  .response_date, .response_rating, .response_tel, .response_object, .response_nps, .h1 {text-align: center}
  th {background: #c1a191;}
  .response_name {padding-left: 5px;}
  .review {background: #f4e7e2;}
  .review__text {padding: 10px 20px;}
  .review__name {padding: 10px 20px;}
  .review__date {text-align: center;}
  .response_text {padding: 10px 20px;}
</style>

<div class="wrapper flex">
  <div class="header flex">
  <table >
    <tr heigth="50px" style="border: none">
      <td  width="50%" class="logo_client"><img src="../thumbs/'.$clientLogo.'"/ width="100px"></td>
      <td width="50%" class="logo_rvz"><img src="https://revizion.ua/mails/logo_mail.png" width="150px"/></td>
    </tr>

  </table>
  <br>

  </div>
';
$response = '
';
foreach($sql as $ps) 
{
  $text = $ps['text'];
  $text =str_replace(chr(13),' ',$text);
  $text = str_replace(chr(10),' ',$text);
  $cmnt_poster = '';
  if($ps['cmnt_poster'] == '' OR $ps['cmnt_poster'] == '0') { $cmnt_poster = '---';} else { $cmnt_poster = $ps['cmnt_poster']; }
  $rating = str_replace('.',',',$ps['rating']);
  if($ps['oid'] == 0) { 
    $OBJECT_ECHO = '---';
    } else {
    $OBJECT_ECHO = get_info($ps['oid'],"cobjects","title","id");
  }
  $person = '';
  if($ps['person'] != 0){
    $sql = db_array(mysql_query("SELECT `name`,`surname` FROM `".PREFIX."personal` WHERE `id` = '{$ps['person']}'"));
    $person = $sql[0]['name'].' '.$sql[0]['surname'];
  }
  $response .= '
  <table>
  <tr>
    <th width="20%">Ім`я</th>
    <th width="18%">Дата</th>
    <th width="10%">Оцiнка</th>
    <th width="6%">NPS</th>
    <th width="19%">Телефон</th>
    <th width="27%">Об`єкт</th>
  </tr>
  <tr>
    <td class="response_name">'.$ps['name'].'</td>
    <td style="text-align: center" class="response_date">'.date("d.m.Y  H:i", $ps['date']).'</td>
    <td style="text-align: center" class="response_rating">'.$rating.'</td>
    <td style="text-align: center" class="response_nps">'.$ps['nps'].'</td>
    <td style="text-align: center" class="response_tel">'.$ps['phone'].'</td>
    <td style="text-align: center" class="response_object">'.$OBJECT_ECHO.'</td>
  </tr>
  ';
  if($person != '') $response .= '<tr><td  class="response_text" colspan="6">Персонал: '.$person.'</td></tr>';
  if($text) $response .= '<tr><td colspan="6" class="response_text">'.$text.'</td></tr>';
  
  if($ps['reply']){
    $response .= '
    <tr class="review">
      <td class="review__name" colspan="5">'.$cmnt_poster.'</td>
      <td class="review__date" colspan="1" >'.date("d.m.Y  H:i", $ps['reply_date']).'</td>
    </tr>
    <tr >
      <td  class="review review__text" colspan="6">'.$ps['reply'].'</td>
    </tr>';
  }
$response .= '</table>';

}
$content = '
<div class="content">
  <div class="response flex">
   '.$response.'
  </div>
</div>';

$footer = '<div class="footer"></div>
</div>';
$html  = $header; 
$html .= $content; 
$html .= $footer; 

//==============================================================

require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new mPDF();

ob_clean();
ob_flush();
$mpdf->WriteHTML($html);
$substr = "&quot;";

$position = strrpos($clientTitle, $substr);
$title;

if($position === false) { $title = $clientTitle;}
else { $title = str_replace($substr, '', $clientTitle);}

$mpdf->Output($file.$title."_".date("Y-m-d_H-i",time()).'.pdf', 'D');
$mpdf->Output('export/'.$file.$title."_".date("Y-m-d_H-i",time()).'.pdf', 'F'); 

//==============================================================
