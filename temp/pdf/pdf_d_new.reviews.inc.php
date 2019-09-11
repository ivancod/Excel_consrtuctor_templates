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
  <table>
    <tr style="border: none">
      <td  width="50%" class="logo_client"><img src="../thumbs/'.$clientLogo.'" width="100px"/></td>
      <td width="50%" class="logo_rvz"><img src="https://revizion.ua/mails/logo_mail.png" width="150px"/></td>
    </tr>
  </table>
  <br>

  </div>
';
$response = '';
foreach($sql as $ps) 
{
  $text = $ps['text'];
  $text =str_replace(chr(13),' ',$text);
  $text = str_replace(chr(10),' ',$text);

  $rating = str_replace('.',',',$ps['rating']);

  //$objDName = get_info($ps['r_id'],"distrib_objects","title","ids");
  $objectID = get_info($ps['oid'],"cobjects","d_trackid","id");

  $objDName = get_info($ps['oid'],"cobjects","d_name","id");

  if($ps['add_info']  == 'undefined') $ps['add_info'] = '';
  if($ps['add_info2'] == 'undefined') $ps['add_info2'] = '';
  if($ps['phone'] == 'undefined' OR $ps['phone'] == '') $ps['phone'] = '---';

  $response .= ' 
  <table>
    <tr>
      <th width="10%">Ім`я</th>
      <th width="18%">Дата</th>
      <th width="10%">Місто</th>
      <th width="10%">Рейтинг</th>
      <th width="6%">NPS</th>
      <th width="19%">Телефон</th>
      <th width="17%">Об`єкт</th>
      <th width="5%">ТА</th>
      <th width="5%">ТТ</th>
    </tr>
    <tr>
      <td class="response_name">'.$ps['name'].'</td>
      <td class="response_date">'.date("d.m.Y  H:i", $ps['date']).'</td>
      <td class="response_date">'.$ps['city'].'</td>
      <td class="response_rating">'.$rating.'</td>
      <td class="response_nps">'.$ps['nps'].'</td>
      <td class="response_tel">'.$ps['phone'].'</td>
      <td class="response_tel">'.$objDName.'</td>
      <td class="response_object">'.$pobjectID.'</td>
      <td class="response_object">'.$ps['r_id'].'</td>
    </tr>     
  ';
  if($text) $response .= '<tr><td  class="response_text" colspan="6">'.$text.'</td></tr>';
  if($ps['comment']) $response .= '<tr><td  class="response_text" colspan="6">'.$ps['comment'].'</td></tr>';
  $response .= '</table>';
}
$content = '
<div class="content">
  <div class="response flex">
   '.$response.'
  </div>
</div>';

$footer = '<div class="footer"></div></div>';

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
