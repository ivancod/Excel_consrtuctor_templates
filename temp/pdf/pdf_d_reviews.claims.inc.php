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
  $text = $ps['txt'];
  $text =strip_tags($text);
  $ps['reply'] =strip_tags($ps['reply']);
  $text = str_replace("\n", ' ', $text);
  $reply = str_replace("\n", ' ', $ps['reply']);

  $text = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $text);
  $reply = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $reply);

  $CITY = get_info($ps['ta_id'],"cobjects","d_city","d_trackid");
  $response .= ' 
  <table>
    <tr>
      <th width="10%">TA</th>
      <th width="18%">TT</th>
      <th width="15%">Місто</th>
      <th width="20%">Дата</th>
      <th width="27%">Файл</th>
    </tr>
    <tr>
      <td class="response_name">'.$ps['ta_id'].'</td>
      <td class="response_date">'.$ps['d_id'].'</td>
      <td class="response_date">'.$CITY.'</td>
      <td class="response_rating">'.date("d.m.Y  H:i", $ps['claim_date']).'</td>
      <td class="response_tel">'.$ps['file'].'</td>
    </tr> 
  ';
  if($text)  $response .= '<tr><td  class="response_text" colspan="6">'.$text.'</td></tr>';
  if($reply) $response .= '<tr><td  class="response_text" colspan="6">'.$reply.'</td></tr>';
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
