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
<table>
    <tr>
      <th width="32%">Клиент</th>
      <th width="20%">Телефон</th>
      <th width="10%">Всього</th>
      <th width="18%">Позитивних</th>
      <th width="18%">Негативних</th>
    </tr>
';
foreach($sql as $ps) 
{
  $reviews_pos = $ps['reviews_pos'];
  $reviews_neg = $ps['reviews_neg'];
  $all_reviews = $reviews_pos + $reviews_neg;  
  if($ps['phone'] == 'undefined' OR $ps['phone'] == '') $ps['phone'] = '---';

  $response .= '
  
    <tr>
      <td class="response_name">'.$ps['name'].'</td>
      <td style="text-align: center" class="response_date">'.$ps['phone'].'</td>
      <td style="text-align: center" class="response_rating">'.$all_reviews.'</td>
      <td style="text-align: center" class="response_object">'.$reviews_pos.'</td>
      <td style="text-align: center" class="response_object">'.$reviews_neg.'</td>
    </tr>
  ';

}
$response .= '</table>';
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
