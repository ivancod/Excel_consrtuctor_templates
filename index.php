<? if(!isset($MODUL)){ Header("Location:/"); die; }
###

if(isset($_POST['select_count'])){
	if(!defined("PAGE_MAX_ROWS"))define("PAGE_MAX_ROWS", $_POST['select_count']);

} else {
	if(!defined("PAGE_MAX_ROWS"))define("PAGE_MAX_ROWS", 10);
}

### filters

if($_GET['select_reviews_tags'] != '') {

	$array = $_GET['select_reviews_tags'];

	if(!in_array("0", $array)) {

		$tags_exist = 1;

		$tagsSql = 'AND `u`.`review_id` = `s`.`uniq_id` AND `u`.`teg_id` IN (';
		
		###
		
		$total = count($array);
		$counter = 0;
		foreach($array as $key => $value){
		  $counter++;
		  if($counter == $total){
		
			$tagsSql .= "".$value." ";
			
		  }
		  else{
			$tagsSql .= "".$value.", ";
		  }
		}

		###

		$tagsSql .= ')  ';
	} 
	else { $tagsSql = ""; $tags_exist = 0;}
	
}  else { $tags_exist = 0; }

### searchValue

if($_GET['searchValue'] !="") {
	$searchSql = "AND (`name` LIKE \"%{$_GET['searchValue']}%\" OR `mail` LIKE \"%{$_GET['searchValue']}%\" OR `phone` LIKE \"%{$_GET['searchValue']}%\" OR `uniq_id` LIKE \"%{$_GET['searchValue']}%\") ";
	$searchSql2 = "AND (`s`.`name` LIKE \"%{$_GET['searchValue']}%\" OR `s`.`mail` LIKE \"%{$_GET['searchValue']}%\" OR `s`.`phone` LIKE \"%{$_GET['searchValue']}%\" OR `s`.`uniq_id` LIKE \"%{$_GET['searchValue']}%\") ";
} else { $searchSql = ""; $searchSql2 = ""; }

### type

if($_GET['type'] != 0) {
	if($_GET['type'] == 1) { $typeSql = " AND `type` = '0'"; $typeSql2 = " AND `s`.`type` = '0'";}
	if($_GET['type'] == 2) { $typeSql = " AND `type` = '1'"; $typeSql2 = " AND `s`.`type` = '1'";}
} else { $typeSql = ""; $typeSql2 = "";}

###  select_reviews_rating

if($_GET['select_reviews_rating'] != 0) {
	if($_GET['select_reviews_rating'] == 1) { $ratingSql = " AND `rating` >= '3'"; $ratingSql2 = " AND `s`.`rating` >= '3'"; }
	if($_GET['select_reviews_rating'] == 2) { $ratingSql = " AND `rating` < '3'"; $ratingSql2 = " AND `s`.`rating` < '3'";}
} else { $ratingSql = ""; $ratingSql2 = "";}

### objects

if($_GET['objects']) {
	$objectSql = " AND `oid` = '{$_GET['objects']}'";
	$objectSql2 = " AND `s`.`oid` = '{$_GET['objects']}'";
} else { $objectSql = ""; $objectSql2 = "";}

### commentz

if($_GET['commentz']) {
	$commentsSql = " AND `rev_id` = '{$_GET['commentz']}'";
	$commentsSql2 = " AND `s`.`rev_id` = '{$_GET['commentz']}'";
} else { $commentsSql = ""; $commentsSql2 = "";}

### dates

if($_GET['date_from'] !="" AND $_GET['date_to']){
	$date_from = $_GET['date_from'];
	$date_from_elements  = explode("/",$date_from);
	$date_from_elements_res = mktime(0,0,0,$date_from_elements[1],$date_from_elements[0],$date_from_elements[2]);
	$date_to = $_GET['date_to'];
	$date_to_elements  = explode("/",$date_to);
	$date_to_elements_res = mktime(23,59,59,$date_to_elements[1],$date_to_elements[0],$date_to_elements[2]);
	$dateQuery = "AND `date` >= '{$date_from_elements_res}' AND `date` <= '{$date_to_elements_res}' ";
	$dateQuery2 = "AND `s`.`date` >= '{$date_from_elements_res}' AND `s`.`date` <= '{$date_to_elements_res}' ";
} else { $dateQuery = ""; $dateQuery2 = "";}

###

if($tags_exist == 1) {
	$all_reviews = db_array(mysql_query("SELECT s.*,s.id as sid, u.* FROM `".PREFIX."reviews` as s, `".PREFIX."teg_to_riview` as u WHERE `s`.`client_id` = '{$CLIENT_ID}' {$typeSql2} {$searchSql2} {$ratingSql2} {$objectSql2} {$dateQuery2} {$commentsSql2} {$tagsSql} AND `s`.`status` = '0'"));

	$all_reviews = count($all_reviews);

}  else {
	$all_reviews = CalcRecords("reviews"," `client_id` = '{$CLIENT_ID}' AND `status` = '0' {$typeSql} {$searchSql} {$ratingSql} {$objectSql} {$dateQuery} {$commentsSql}");
}

$rating = get_info($CLIENT_ID,"clients","rating","id");
$globaly_rating = $rating;

$MARK = explode(".", $globaly_rating);
$MARK_one = $MARK[0];
$MARK_two = $MARK[1];
$mark = substr($MARK_two, 0, 1);

if($mark !="") { $addpre = ",".$mark.""; } else { $addpre = ""; }
$ratingVal = $MARK_one."<span class='rating-val__small'>".$addpre."</span>";

$all_competitions = CalcRecords("competencies"," `client_id` = '{$CLIENT_ID}' AND `status` = '0'");
$all_tags = CalcRecords("tegs"," `client_id` = '{$CLIENT_ID}' AND `status` = '0'");

###

 if($hide_rating != 1){
	$rateStr = $l['lc_199'].': <span class="reliability__mark-count">'.$ratingVal.'</span>';	
 } else { 

	$all_reviews_global = CalcRecords("reviews"," `client_id` = '{$CLIENT_ID}' AND `status` = '0' ");

	$happy = CalcRecords("reviews"," `client_id` = '{$CLIENT_ID}' AND `status` = '0' AND `nps` > '8'");
	$angry = CalcRecords("reviews"," `client_id` = '{$CLIENT_ID}' AND `status` = '0' AND `nps` < '7' ");
	$totalUsers = $all_reviews_global; 
	$GLOBAL_NPS = (($happy - $angry) / $totalUsers) * 100;
	$npsVal = round($GLOBAL_NPS,1);

	$rateStr = 'NPS: <span class="reliability__mark-count">'.$npsVal.'%</span>';
}

###

$clientTitle = get_info($CLIENT_ID,"clients","title","id");
$clientTitleEn = get_info($CLIENT_ID,"clients","title_en","id");
$clientLogo = get_info($CLIENT_ID,"clients","logo","id");

$sms_reply = get_info($CLIENT_ID,"client_params","sms_reply","client_id");
$send_reply_tg = get_info($CLIENT_ID,"client_params","send_reply_tg","client_id");

$getFirstClientReview = get_info_limits($CLIENT_ID,"reviews","date","client_id","AND `status` = '0' ORDER BY `id` ASC LIMIT 1");
$firstReview = date("d/m/Y", $getFirstClientReview);
$datefrom = !empty($_GET['date_from']) ? $_GET['date_from'] : $firstReview;

$clientLogos = '<a href="https://revizion.ua/" target="_blank"><img border="0" src="https://revizion.ua/thumbs/'.$clientLogo.'" style="max-width: 190px; max-height: 60px;"></a>';

# короче тут компетенции начинаються
$manId = $_SESSION['main_id'];
$type_answer = 1;
include 'params/vs_functions.php';
include 'inc.comp.php';
	
# короче тут компетенции заканчиваються

### Constructor_EXCEL start

$tmp_type = '0';
// порядок в масивах тайтлах дожен совпадать с порядком в массиве данных
$argWidth = [	30,	30,	10,	25,	25,	25,	30, 25, 25,	25,	25,	25,	25,	25,	35, 20, 20, 45, 15, 30, 30, 30, 30];
$argTitle = ['Ім`я', 'Телефон', 'Оцінка NPS', 'Дата  відгуку', 'Текст', 'Коментар', ' Об`єкт', 'Побажання', 'Параметр оцінки 1', 'Параметр оцінки 2', 'Параметр оцінки 3', 'Параметр оцінки 4', 'Параметр оцінки 5', 'Параметр оцінки 6', 'Персонал', 'Переваги', 'Недолiки', 'E-mail', 'Рейтинг', 'Місто', 'Дата народження', 'Дод. поле 1', 'Дод. поле 2']; 

echo '<div id="const_wrap" style="display: none; width: 100%; max-width: 700px">';
	include 'blocks/excel_constructor.php';
echo '</div>';
echo '<div id="excel_wrap" style="display: none; width: 100%; max-width: 700px"></div>';

### Constructor_EXCEL end
?>

 
			<main class="col-xl-20 col-xxl-17"> 

				<div class="reliability row align-items-end">
					<div class="col-10 col-md-6 col-xl-5 col-xxl-4">
						<p class="reliability__total"><?=$l['lc_198']?>: <span class="reliability__total-count"><?=$all_reviews?></span></p>
					</div>
					<div class="col-10 col-md-5 col-xl-4 col-xxl-3 reliability__mark-wrap">
						<p class="reliability__mark"><?=$rateStr?></p>
					</div>
					<div class="col-sm-20 col-md-9 col-xl-8 col-xxl-6 reliability__search">
						<form class="reviews-search" method="get" action="<?=getUrl()?>">
							<input type="hidden" name="opinions">
							<input type="hidden" name="list">
							<input type="hidden" name="l">
							<input type="hidden" name="select_reviews_comments" value="0">
							<input type="text" placeholder="<?=$l['lc_226']?>" name="searchValue" value="<?=$_GET['searchValue']?>">
							<button class="reviews-search__btn" name="search_post"><i class="icon icon-search"></i></button>
						</form>
					</div>
				</div>
				<form action="<?=getUrl()?>" method="get">
				<input type="hidden" name="opinions">
				<input type="hidden" name="list">
				<input type="hidden" name="l">

					<button class="filter-block-show"><?=$l['lc_200']?> <i class="icon-triangle icon-triangle_padding"></i></button>
					<div class="filter-block-hide">
						<div class="reviews-filter">

							<div class="reviews-filter__date">
								<p class="reviews-filter__date-name"><?=$l['lc_201']?>:</p>
								<div class="reviews-filter__wrap datepicker-group">
									<div class="datepicker-wrap">
										<span class="datepicker-wrap__title"><?=$l['lc_202']?></span>
										<input type="text" class="filter-datepicker filter-datepicker_from" name="date_from" value="<?=$datefrom?>" style="position: relative; z-index: 9;">
									</div>
									<div class="datepicker-wrap">
										<span class="datepicker-wrap__title"><?=$l['lc_203']?></span>
										<input type="text" name="date_to" class="filter-datepicker filter-datepicker_to" value="<?=$_GET['date_to']?>" style="position: relative; z-index: 9;">
									</div>
									<button class="reviews-filter__btn">ok</button>
								</div>
							</div>

							<div class="reviews-filter__select-wrap reviews-filter__select-wrap_rating" style="margin-right: 10px;">
								<p class="reviews-filter__date-name" style="text-transform: lowercase;"><?=$l['opinions']?>:</p>
								<div class="filter-select">
									<select class="filter-select__item" name="type" onchange="this.form.submit()">
										<option value="0" <?php echo glob_select($_GET['type'], 0);?>><?=$l['l_437']?></option>
										<option value="1" <?php echo glob_select($_GET['type'], 1);?>><?=$l['lc_120']?></option>
										<option value="2" <?php echo glob_select($_GET['type'], 2);?>><?=$l['lc_121']?></option>
									</select>
								</div>
							</div>
							
							<div class="reviews-filter__select-wrap reviews-filter__select-wrap_padding">
								<p class="reviews-filter__date-name"><?=$l['lc_227']?>:</p>
								<div class="filter-select">
									<select class="filter-select__item" name="objects" onchange="this.form.submit()">
										<option value="0"><?=$l['lc_205']?></option>
										<?php
										$Objects = db_array(mysql_query("SELECT id,title,d_name FROM `".PREFIX."cobjects` WHERE `client_id` = '{$CLIENT_ID}' ORDER BY `id` DESC"));
										foreach($Objects as $so) 
										{
											if($so['d_name'] !='') { $DNAME = $so['d_name'];} else { $DNAME = $so['title'];}
											echo "<option value='{$so['id']}' ".glob_select($_GET['objects'], $so['id']).">{$DNAME}</option>\n";
										}
										?>
									</select>
								</div>
							</div>

							<div class="reviews-filter__select-wrap reviews-filter__select-wrap_rating">
								<p class="reviews-filter__date-name">рейтинг:</p>
								<div class="filter-select">
									<select class="filter-select__item" name="select_reviews_rating" onchange="this.form.submit()">
										<option value="0" <?php echo glob_select($_GET['select_reviews_rating'], 0);?>><?=$l['lc_205']?></option>
										<option value="1" <?php echo glob_select($_GET['select_reviews_rating'], 1);?>><?=$l['lc_206']?></option>
										<option value="2" <?php echo glob_select($_GET['select_reviews_rating'], 2);?>><?=$l['lc_207']?></option>
									</select>
								</div>
							</div>

						</div>

						<?php
						if($all_tags > 0) {
						?>
						<div class="reviews-filter vs_reviews-filter">
							<div class="reviews-filter__date reviews-filter__select-wrap_padding" style="max-width: 100% !important; width: auto;">
								<p class="reviews-filter__date-name ">вибір тегів:</p>
								<div class="filter-select">
									<select class="names-tegs" name="select_reviews_tags[]" multiple="multiple">

										<?php
										$tegs = db_array(mysql_query("SELECT id,title FROM `".PREFIX."tegs` WHERE `client_id` = '{$CLIENT_ID}' AND `status` = '0' ORDER BY `id` DESC"));
										foreach($tegs as $tt) 
										{
											echo "<option value='{$tt['id']}'"; 
												if(in_array($tt['id'], $array)) echo 'selected';
											echo ">{$tt['title']}</option>\n";
										}
										?>
									</select>
								</div>
							</div>
						</div>
						<?
						}
						?>
					</div>
					
					</form>
					
					<form action="<?=getUrl()?>" method="post">
					<div class="row justify-content-between align-items-center reviews-action-wrap">
						<div class="reviews-action col-xl-17 col-xxl-12">
							<div class="check-all-wrap d-none d-sm-block">
								<div class="check-all">
									<input type="checkbox" id="check-all" class="check-hide check-all-reviews">
									<label for="check-all" class="check-all__custom check-all__custom_relative"><i class="icon icon icon-check-brown"></i></label>
								</div>
							</div>
							<button class="reviews-action__btn" name="publish_all"><?=$l['lc_228']?></button>
							<!--<button class="reviews-action__btn" name="answer_all"><?=$l['lc_219']?></button>-->
							
							<button class="reviews-action__btn" data-fancybox="g_constructor_excel" data-src="#const_wrap">КОНСТРУКТОР</button>
							<button class="reviews-action__btn" data-fancybox="g_excel_wrap" data-src="#excel_wrap">EXCEL</button>
							<button class="reviews-action__btn" name="export_pdf">PDF</button>
						</div>
						<div class="col-xl-3 pagination-wrap pagination-wrap_bottom">
							<div class="check-all d-flex d-sm-none">
								<input type="checkbox" id="check-all1" class="check-hide check-all-reviews">
								<label for="check-all1" class="check-all__custom check-all__custom_relative"><i class="icon icon icon-check-brown"></i></label>
								<div class="filter-select-check">
									<select class="filter-select__check" name="select-reviews-rating">
										<option value=""><?=$l['lc_205']?></option>
									</select>
								</div>
							</div>
							<div class="pagination reviews-action-wrap__pagination  reviews-action-wrap__pagination_mobile"></div>
							<div class="filter-select select-count">
								<select class="filter-select__item filter-select__item_uppercase" name="select_count" onchange="this.form.submit()">
									<option value="10" <?php echo glob_select($_POST['select_count'], 10);?>>10</option>
									<option value="20" <?php echo glob_select($_POST['select_count'], 20);?>>20</option>
									<option value="30" <?php echo glob_select($_POST['select_count'], 30);?>>30</option>
								</select>
							</div>
						</div>
					</div>
					<?php
					### export_pdf

					if(isset($_POST['export_pdf'])) 
					{

						$sql = db_array(mysql_query("SELECT * FROM `".PREFIX."reviews` WHERE `client_id` = '{$CLIENT_ID}' AND `status` = '0' {$typeSql} {$searchSql} {$ratingSql} {$dateQuery} {$objectSql}"));
						$sqlLength = count($sql) + 1;
						$file = 'export_reviews_';
				
						include 'doc/pdf/pdf_new.reviews.inc.php';
					}



					### export_all

					if(isset($_POST['exp_all'])) 
					{
						$tmp_id = $_POST['temp_excel']; 
	
						$sql = db_array(mysql_query("SELECT * FROM `".PREFIX."reviews` WHERE `client_id` = '{$CLIENT_ID}' AND `status` = '0' {$typeSql} {$searchSql} {$ratingSql} {$dateQuery} {$objectSql}  LIMIT 5000"));
						$sqlLength = count($sql) + 1;
						$file = 'export_reviews_';

						include 'doc/new.reviews.inc.php';
						
						echo "<p>&nbsp;</p><div class='msg success excel_link'>{$l['lc_209']}<p><a  href='export/".$filename.".xlsx' target='_blank' style='color: #ff0000;'>{$l['lc_148']}</a></p></div>";
					}

					### publish_all

					if(isset($_POST['publish_all'])) 
					{
						if(isset($_POST['selectReview']))
						{
							foreach($_POST['selectReview'] as $key=>$value)
							db_update(PREFIX . "reviews", array("type" => 1, "status" => 0,"manager" => $_SESSION['client_id']), "id = '{$value}'");
						}
						echo "<p>&nbsp;</p><div class='msg success'>{$l['lc_230']}</div>";
					}

					### publish review

					if(isset($_POST['publish'])) 
					{
						foreach($_POST['publish'] as $id => $review_id) 
						{
							db_update(PREFIX . "reviews", array("type" => 1, "status" => 0,"manager" => $_SESSION['client_id']), "id = '{$id}'");
							echo "<p>&nbsp;</p><div class='msg success'>{$l['lc_231']}</div>";
						}
					}

					### claim

					if(isset($_POST['claim'])) 
					{
						foreach($_POST['claim'] as $ids => $review_id) 
						{
							$claimTxt = strip_tags($_POST['claim_txt'][$ids]);
							$claim_type = $_POST['contest'][$ids];

							db_update(PREFIX . "reviews", array("type" => 1, "claim_txt" => $claimTxt, "claim_date" => time(), "claim_type" => $claim_type, "status" => 1), "id = '{$ids}'");
							echo "<p>&nbsp;</p><div class='msg success'>{$l['lc_212']}</div>";
						}
					}
					?>

					<div class="reviews-list">
						<?php

							if($tags_exist == 1) {
								$post_s = db_array(mysql_query("SELECT s.*,s.id as sid, u.* FROM `".PREFIX."reviews` as s, `".PREFIX."teg_to_riview` as u WHERE `s`.`client_id` = '{$CLIENT_ID}' {$typeSql2} {$searchSql2} {$ratingSql2} {$objectSql2} {$dateQuery2} {$commentsSql2} {$tagsSql} AND `s`.`status` = '0'"));

								$post_s = count($post_s);

								$pc = new PAGEING;
								$pc->init($post_s);

								###

								$posts = db_array(mysql_query("SELECT s.*,s.id as sid, u.* FROM `".PREFIX."reviews` as s, `".PREFIX."teg_to_riview` as u WHERE `s`.`client_id` = '{$CLIENT_ID}' {$typeSql2} {$searchSql2} {$ratingSql2} {$objectSql2} {$dateQuery2} {$commentsSql2} {$tagsSql} AND `s`.`status` = '0' GROUP BY `s`.`id` ORDER BY `s`.`date` DESC LIMIT {$pc->rstart}, {$pc->ronpage}"));

							} else {
								$post_s = mysql_result(mysql_query("SELECT count(*) FROM `".PREFIX."reviews` WHERE `client_id` = '{$CLIENT_ID}' AND `status` = '0' {$typeSql} {$searchSql} {$ratingSql} {$objectSql} {$dateQuery} {$commentsSql}"), 0);

								$pc = new PAGEING;
								$pc->init($post_s);

								$posts = db_array(mysql_query("SELECT * FROM `".PREFIX."reviews` WHERE `client_id` = '{$CLIENT_ID}' AND `status` = '0' {$typeSql} {$searchSql} {$ratingSql} {$objectSql} {$dateQuery} {$commentsSql} ORDER BY `date` DESC, `id` ASC LIMIT {$pc->rstart}, {$pc->ronpage}"));
							}
							
							###

							$rat_1 = get_info($CLIENT_ID,"client_params","rat_1","client_id");
							$rat_2 = get_info($CLIENT_ID,"client_params","rat_2","client_id");
							$rat_3 = get_info($CLIENT_ID,"client_params","rat_3","client_id");
							$rat_4 = get_info($CLIENT_ID,"client_params","rat_4","client_id");
							$addField = get_info($CLIENT_ID,"client_params","add_field1_title","client_id");
							$add_info2_title = get_info($CLIENT_ID,"client_params","add_field2_title","client_id");

							foreach($posts as $lr) 
							{
								$_GET['post'] = $lr['uniq_id'];
								  $status   = db_array(mysql_query("SELECT * FROM `".PREFIX."review_status` WHERE `review_id` = ".$lr['uniq_id']." ORDER BY `id` DESC"));
								  $status_1 = db_array(mysql_query("SELECT * FROM `".PREFIX."review_status` WHERE `review_id` = ".$lr['uniq_id']." AND `status` = 1 ORDER BY `date` DESC"));
								  $status_2 = db_array(mysql_query("SELECT * FROM `".PREFIX."review_status` WHERE `review_id` = ".$lr['uniq_id']." AND `status` = 2 ORDER BY `date` DESC"));
								  $status_3 = db_array(mysql_query("SELECT * FROM `".PREFIX."review_status` WHERE `review_id` = ".$lr['uniq_id']." AND `status` = 3 ORDER BY `date` DESC"));


								if(mysql_num_rows(mysql_query("SELECT * FROM `".PREFIX."user_mails` WHERE `mail` = '{$lr['mail']}' AND `status` = '0'"))) 
								{
									$userMailStatus = " color: #ff0000;";
									$userMailStatusIco = ' <img src="img/ico-notok.svg" width="12px" height="12px" style="float: left; margin-right: 5x;">';
								}

								if(mysql_num_rows(mysql_query("SELECT * FROM `".PREFIX."user_mails` WHERE `mail` = '{$lr['mail']}' AND `status` = '1'"))) 
								{
									$userMailStatus = " color: green;";
									$userMailStatusIco = ' <img src="img/ico-ok.svg" width="12px" height="12px" style="float: left; margin-right: 5px;">';
								}

								if(!mysql_num_rows(mysql_query("SELECT * FROM `".PREFIX."user_mails` WHERE `mail` = '{$lr['mail']}'"))) 
								{
									$userMailStatus = "";
									$userMailStatusIco = '';
								}

								### $userPhoneStatus

								if(mysql_num_rows(mysql_query("SELECT * FROM `".PREFIX."user_phones` WHERE `phone` = '{$lr['phone']}' AND `status` = '0'"))) 
								{
									$userPhoneStatus = " style='color: #ff0000;'";
									$userPhoneStatusIco = ' <img src="img/ico-notok.svg" width="12px" height="12px" style="float: left; margin-right: 5px; margin-top: 3px;">';
								}

								if(mysql_num_rows(mysql_query("SELECT * FROM `".PREFIX."user_phones` WHERE `phone` = '{$lr['phone']}' AND `status` = '1'"))) 
								{
									$userPhoneStatus = " style='color: green;'";
									$userPhoneStatusIco = ' <img src="img/ico-ok.svg" width="12px" height="12px" style="float: left; margin-right: 5px; margin-top: 3px;">';
								}

								if(!mysql_num_rows(mysql_query("SELECT * FROM `".PREFIX."user_phones` WHERE `phone` = '{$lr['phone']}'"))) 
								{
									$userPhoneStatus = "";
									$userPhoneStatusIco = '';
								}

								###

								$OBJECT_URL = $lr['rev_id'];
								$OBJECT_ID = $lr['oid'];

								###

								if($OBJECT_ID != 0) {
									$objDName = get_info($OBJECT_ID,"cobjects","d_name","id");

									if($objDName == "") { 
										$OBJECT = get_info($lr['rev_id'],"rev_offline","comment","url");
										$objName = get_info($OBJECT_ID,"cobjects","title","id"); 

										$comment_to_qr = get_info($lr['rev_id'],"rev_offline","comment","url");
										if($comment_to_qr !="") { $cmnt_qr = ' ('.$comment_to_qr.')'; } else { $cmnt_qr = '';}

										$OBJECT_ECHO = "{$l['lc_246']}: <a href='profile.html?opinions&params&offline' title='".$OBJECT."'>".$objName."{$cmnt_qr}</a>";
									} else {
										$objDPhone = get_info($OBJECT_ID,"cobjects","d_phone","id");
										$objDMail = get_info($OBJECT_ID,"cobjects","d_mail","id");
										$objName = $l['lc_246'].': '.$objDName."\n".$objDPhone." ".$objDMail."\n";
										$OBJECT_ECHO = $objName;
									}
								}

								###

								if($lr['person'] != 0) {
									$personaName1 = get_info($lr['person'],"personal","name","id");
									$personaName2 = get_info($lr['person'],"personal","surname","id");
									$PERSONAL_ECHO = "{$l['lc_234']}: ".$personaName1." ".$personaName2;
								} else { $PERSONAL_ECHO = '';}

								if($lr['nps'] > 8) { $ireccomend = '<span class="sign-recommend comment-item__sign">'.$l['lc_247'].'</span>'; }  else { $ireccomend = '';}

								$dignity = empty($lr['dignity']) ? "" : '<div class="product-prop__item">
											<div class="product-prop__title">
												<i class="icon icon-plus"></i>
												<span>'.$l['lc_213'].':</span>
											</div>
											<div class="product-prop__desc">
												<span>'.$lr['dignity'].'</span>
											</div>
										</div>'; 

								$limitations = empty($lr['limitations']) ? "" : '<div class="product-prop__item">
											<div class="product-prop__title">
												<i class="icon icon-minus"></i>
												<span>'.$l['lc_214'].':</span>
											</div>
											<div class="product-prop__desc">
												<span>'.$lr['limitations'].'</span>
											</div>
										</div>'; 

								$rrating = round($lr['rating'],1);

								if($rrating >= 3) {
									$iconSmile = 'icon-smile-good';
									$iconIco = '';
									$bg_style = '';

								} else {
									$iconSmile = 'icon icon-smile-bad';
									$bg_style = ' reviews-item__bg_checked-default';
									$iconIco = 'icon-check-none';
								}

								if($lr['text'] != "") { $iconCmnt = 'icon-message-on';} else {
									$iconCmnt = 'icon icon-message-off';
								}

								if($lr['claim_type'] != 0) { $iconIco = 'icon-check-none'; }

								$lr['phone'] = str_replace("-","",$lr['phone']);

								$order_id = empty($lr['order_id']) ? "" : "{$l['lc_215']}: ".$lr['order_id'];
								$product_id = empty($lr['product_id']) ? "" : "{$l['lc_235']}: ".$lr['product_id'];
								$userPhone = empty($lr['phone']) ? "" : "{$l['lc_216']}.: ".$lr['phone'];
								$userBD = empty($lr['born_date']) ? "" : "{$l['lc_217']}: ".$lr['born_date'];
								$userCity = empty($lr['city']) ? "" : "{$l['lc_236']} : ".$lr['city'];
								$add_info2 = empty($lr['add_info2']) ? "" : "<br />{$add_info2_title}: ".$lr['add_info2'];

								###

								$reply_date = empty($lr['reply_date']) ? "---" : date("d.m.Y H:i", $lr['reply_date']);
								$answer = empty($lr['reply']) ? "" : '<p style="margin-top: 10px;"><b>'.$l['lc_245'].': '.$lr['cmnt_poster'].' ('.$reply_date.'):</b> '.$lr['reply'].'</p>';
								$r_l_status = db_array(mysql_query("SELECT * FROM `".PREFIX."review_status` WHERE `review_id` = ".$lr['uniq_id']." AND `status` > 2 ORDER BY `id` DESC"));
								$r_lstatus1 = $status ? 1 : 0;
								if($r_l_status[0]['status'] == 3) $r_lstatus1 = 3;
								if($r_l_status[0]['status'] == 4) $r_lstatus1 = 4;
								###
								if($r_lstatus1) {
									switch ($r_lstatus1) {
								    case 1:
							        $typeBadge = "<span class='newBadge' style='border-color: #dd5b04; color: #dd5b04;'>в роботi</span>";
							        break;
								    case 3:
							        $typeBadge = "<span class='newBadge' style='border-color: #057c45; color: #057c45;'>закритий</span>";
							        break;
								    case 4:
							        $typeBadge = "<span class='newBadge' style='border-color: #dd5b04; color: #dd5b04;'>в роботi</span>";
							        break;
									}
								} else {
									if($lr['type'] == 0) { $typeBadge = "<span class='newBadge'>новий</span>"; } else { $typeBadge = "";}
								}

								### 

								$r_id = $lr['r_id'];

								if($r_id != '' AND $CLIENT_ID == '280') {
									$tadres = ' ('.get_info($r_id,"distrib_objects","adres","ids").')';
									$tt = $l['lc_237'].': '.get_info($r_id,"distrib_objects","title","ids").$tadres;
								} else { $tt = ''; }

								###

								if($addField) {
									$useraddField = empty($lr['add_info']) ? "" : $addField.": ".$lr['add_info'];
								}

								$audioFile = empty($lr['audio']) ? "" : "<audio controls><source src='audio/{$lr['audio']}' type='audio/mpeg'></audio>";

								###
								
								$file = empty($lr['file']) ? "" : "<a data-fancybox=\"gallery\" href='thumbs/{$lr['file']}'><div class='oThumb' style='background: url(thumbs/{$lr['file']}); background-size: cover; background-position: center center;'></div></a>";

								### nps show
									
								if($lr['nps'] > 8 ) { $nps_bg = ' style="background: url(img/happy.svg) 6px 10px no-repeat; background-size: 18px;"';}
								if($lr['nps'] > 6 AND $lr['nps'] < 9) { $nps_bg = ' style="background: url(img/neitral.svg) 6px 10px no-repeat; background-size: 18px; float: right; top: 0;"';}
								if($lr['nps'] < 7 ) { $nps_bg = ' style="background: url(img/angry.svg) 6px 10px no-repeat; background-size: 18px; float: right; top: 0;"';}

								$npsShow = '<span class="nps" '.$nps_bg.'>'.$lr['nps'].'</span>';
								?>
								<article class="reviews-item">
							<div class="reviews-item__bg <?=$bg_style?>">
								<div class="reviews-item__status">
									<div class="reviews-item__icon">
										<div class="reviews-check element-margin-mobile">
											<input type="checkbox" id="check-reviews<?=$lr['id']?>" name="selectReview[]" value="<?=$lr['id']?>" class="check-hide reviews-check__single">
											<label for="check-reviews<?=$lr['id']?>" class="check-all__custom check-all__custom_border">
												<i class="icon icon icon-check-brown"></i>
											</label>
										</div>
										<i class="icon <?=$iconSmile?> element-margin-mobile d-block d-lg-none"></i>
										<i class="icon <?=$iconCmnt?> element-margin-mobile"></i>
										<i class="icon <?=$iconIco?>"></i>
										<div class="reviews-item__rating ratting-show ratting-mobile d-block d-lg-none">
											<?php if($hide_rating != 1) { ?>
											<div class="reviews-item__star">
												<span class="star-count"><?=round($lr['rating'],1)?></span>
												<div class="star-list">
													<?=generateStars($lr['rating'])?>
												</div>
												<div class="rating-hidden">
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_1?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star1']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star1'])?>
															</div>
														</div>
													</div>
													<?if($lr['star2'] != 0) { ?> 
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_2?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star2']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star2'])?>
															</div>
														</div>
													</div>
													<?php } ?>

													<?if($lr['star3'] != 0) { ?> 
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_3?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star3']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star3'])?>
															</div>
														</div>
													</div>
													<?php } ?>

													<?if($lr['star4'] != 0) { ?> 
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_4?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star4']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star4'])?>
															</div>
														</div>
													</div>
													<?php } ?>
												</div>
											</div>
											<?php } ?>

											<?=$npsShow?><?=$ireccomend?>
										</div>
									</div>
									<div class="reviews-item__smile d-none d-lg-flex">
										<i class="icon <?=$iconSmile?>"></i>
									</div>
								</div>
								<div class="reviews-item__main">
									<div class="reviews-item__top">
										<div class="reviews-info">
											<table class="reviews-info__table">
												<tr>
													<td class="reviews-info__personal">
														<h6 class="reviews-info__name"><?=$typeBadge?> <?=$lr['name']?></h6>
													</td>
													<td class="reviews-info__city-wrap">
														<span class="reviews-info__city">
														<?=$PERSONAL_ECHO?><?=$OBJECT_ECHO?>
														</span>
														<?php if($status){
															$date = $status[0]['24h_completed'] ? '24 години' : date('d.m.Y', $status[0]['date_completed']);
															echo '<br><span class="reviews-info__city">Прогноз вирiшення: '.$date.'</span>';
														}	?>
													</td>
													<td class="reviews-info__number-wrap">
														<span class="reviews-info__number" <?=$userPhoneStatus?>><?=$userPhone?> <?=$userPhoneStatusIco?></span>
														<?php if($status_3){
															echo '<br><span class="reviews-info__city">Закриття: '.date('d.m.Y H:i', $status_3[0]['date']).'</span>';
														}	?>
													</td>
													<td class="reviews-info__id-wrap">
														<span class="reviews-info__id"><?=$l['lc_218']?>: <?=$lr['uniq_id']?></span>
													</td>
												</tr>
												<tr>
													<td class="reviews-info__personal">
														<div class="reviews-info__mail-wrap">
															<span class="reviews-info__date"><?=date("d.m.Y  H:i", $lr['date'])?></span>
															<div style='clear: left;width: 100%; height: 5px;'></div>
															<span class="reviews-info__mail" style="margin-left: 0;<?=$userMailStatus?>"><?=$lr['mail']?> <?=$userMailStatusIco?></span>
														</div>
													</td>
													<td class="reviews-info__city-wrap">
														<span class="reviews-info__number"><?=$tt?> <?=$useraddField?> <?=$add_info2?></span>
													</td>
													<td class="reviews-info__number-wrap">
														<span class="reviews-info__number"><?=$product_id?></span>
													</td>
													<td class="reviews-info__id-wrap">
														<span class="reviews-info__id"><?=$userBD?> <?=$userCity?></span>
													</td>
												</tr>
											</table>
										</div>
										<div class="reviews-item__rating ratting-show d-none d-lg-block">
											<?php if($hide_rating != 1) { ?>
											<div class="reviews-item__star">
												<span class="star-count"><?=round($lr['rating'],1)?></span>
												<div class="star-list">
													<?=generateStars($lr['rating'])?>
												</div>
												<div class="rating-hidden">
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_1?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star1']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star1'])?>
															</div>
														</div>
													</div>
													<?if($lr['star2'] != 0) { ?> 
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_2?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star2']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star2'])?>
															</div>
														</div>
													</div>
													<?php } ?>

													<?if($lr['star3'] != 0) { ?> 
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_3?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star3']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star3'])?>
															</div>
														</div>
													</div>
													<?php } ?>

													<?if($lr['star4'] != 0) { ?> 
													<div class="rating-hidden__row">
														<p class="rating-hidden__title"><?=$rat_4?>:</p>
														<div class="rating-hidden__wrap">
															<span class="rating-hidden__count"><?=$lr['star4']?></span>
															<div class="rating-hidden__star">
																<?=generateStars($lr['star4'])?>
															</div>
														</div>
													</div>
													<?php } ?>
												</div>
											</div>
											<?php } ?>
											<?=$npsShow?><?=$ireccomend?>
										</div>
									</div>
									<?php if($d_status == 1) { ?>
									<div class="product-prop">
										<div class="reviews-info__id"><?=$lr['user_info']?></div>
									</div>
									<?php } ?>
									<div class="product-prop">
										<?=$dignity?>
										<?=$limitations?>
									</div>
									<?=$file?>
									<div class="reviews-wrap">
										<div class="reviews-wrap__text">
											<span class="reviews-wrap__text_show"><? vs_link_in_audio($lr['text']); ?></span>
										</div>
									</div>
									<div class="reviews-wrap">
										<div class="reviews-wrap__text">
											<!--<span class="reviews-wrap__text_show"><?=$answer?></span>-->
										</div>
									</div>

							<?=$audioFile?>

							<?php

					      $competenties = GetComp('compent_to_riview', 'competencies', 'compent_id', 'title', $lr['uniq_id']);
					      $tegs 				= GetComp('teg_to_riview', 'tegs', 'teg_id', 'title', $lr['uniq_id']);

					      $last_sms 			= db_array(mysql_query("SELECT * FROM `".PREFIX."review_sms` WHERE `review_id` = ".$lr['uniq_id']." AND `type` = 0 ORDER BY `date` DESC"));
					      $last_reply 		= db_array(mysql_query("SELECT * FROM `".PREFIX."review_reply` WHERE `review_id` = ".$lr['uniq_id']." ORDER BY `date` DESC"));
					      // $last_sms_close = db_array(mysql_query("SELECT * FROM `".PREFIX."review_sms` WHERE `review_id` = ".$lr['uniq_id']." AND `type` = 1 ORDER BY `date` DESC"));

					      ###
							?>	
							<div class="vs_comp_wrap">	
						  <table>
				  		  <?php
							  if($competenties AND $vs_HasCompetenties){
							  ?>
							  <tr>
							  	<td class="vs_td_title_comp" ><span class="vs_td_title_comp_text">Компетенцiї</span></td>
							  	<td class="vs_td_comp"><?php foreach($competenties as $val) echo '<span class="vs_comp" style="color: #c10831;">'.$val."</span> ";?></td>
							  </tr>
							  <?php 
								}
                $tegss  = db_array(mysql_query("SELECT * FROM `".PREFIX."tegs` WHERE `client_id` = ".$CLIENT_ID));
                $sqlLength = count($tegss) + 1;
                $comp = db_array(mysql_query("SELECT `teg_id` FROM `".PREFIX."teg_to_riview` WHERE  `review_id` = ".$lr['uniq_id']));
                $com  = [];
                if(($tegss AND !$vs_HasCompetenties) OR $tegs) {
							  ?>
							  <tr>
							  	<td class="vs_td_title_comp vs_td_title_tegs"><span class="vs_td_title_comp_text">Теги</span></td>       
							  	<td class="vs_td_comp"><?php foreach($tegs as $val) echo '<span class="vs_comp">#'.$val."</span> ";?></td>
							  </tr>
              <? } ?>
						  </table>

               <?php 
  
                foreach($comp as $val) array_push($com, $val['teg_id']); 
                if($tegss AND !$vs_HasCompetenties){ ?>
                    <div class="names-tegs_wrap" style="display: none;">
                      <select class="names-tegs" name="tegs['<?=$lr['uniq_id']?>'][]" multiple="multiple">
                      <?php foreach($tegss as $s) { ?>
                        <option value="<?=$s['id']?>" <?php if(in_array($s['id'], $com)) echo 'selected'; ?>>#<?=$s['title']?></option> 
                      <? } ?>
                      </select>
                      <button class="answer-button__btn answer-button__submit" name="reply[<?=$lr['id']?>]"><?=$l['lc_16']?></button>
                      <input type="hidden" name="not_competenties" value="no">
                    </div>
              <? } ?>
              <br>
              <?php if($status_1){ 
						  	$s_1;
						  	if(!$last_reply[0]['text']){
						  		$s_1 = 'змiна статусу';
						  	} else {
						  		$s_1 = '';
						  	}
						  	?>
						  	<div class="vs_nowrap">
									<span class='vs_info_title' style='color:#a92543'>Коментарiй  вiд <?=GetNameStatus($status_1[0])?> (<?=date('d.m.Y H:i', $status_1[0]['date'])?>): </span>
									<span class="vs_nowrap"><?=$s_1?> <?=$last_reply[0]['text']?></span></span><br>
						  	</div>
						<? } ?>
						  <?php if($status_2){ ?>
				  	  	<div class="vs_nowrap">
									<span class='vs_info_title' style='color:#dd5b04'>Iнформування вiд <?=GetNameStatus($status_2[0])?> (<?=date('d.m.Y H:i', $status_2[0]['date'])?>):</span> 
									<span class="vs_nowrap"><?=$last_sms[0]['text']?></span></span><br>
						  	</div>
							<? } ?>
						  <?php if($status_3){ ?>
				  	  	<div class="vs_nowrap">
									<span class='vs_info_title' style='color:#057c45'>Закриття вiд <?=GetNameStatus($status_3[0])?> (<?=date('d.m.Y H:i', $status_3[0]['date'])?>): </span>
									<span class="vs_nowrap"><?=getWayClosed($status_3[0])?></span></span><br>
						  	</div>
							<? } ?>
							</div>
						
							</div>
							</div>
								

							<div class="reviews-item__main-wrap1">
								<div class="reviews-item__main more-toggle">
									<div class="look-more">
										<div class="look-more__prop">
											<?php if($status_1 OR !$vs_HasCompetenties){ 
												if(!$vs_HasCompetenties) echo '<input type="hidden" name="not_competenties" value="no">';?>
												<button class="look-more__btn look-more__btn_triangle look-more__answer"><?=$l['lc_219']?> <i class="icon-triangle icon-triangle_padding"></i></button>
											<? } 
											if(!$vs_HasCompetenties AND $lr['type'] == 0) echo '<button class="look-more__btn" name="publish['.$lr['id'].']">'.$l['lc_228'].'</button>';
											?>
											<?php if($vs_HasCompetenties){ ?>
											<button class="look-more__btn vs_btn_competenties" data-fancybox="g_<?=$lr['uniq_id']?>" data-src="#qreply_<?=$lr['uniq_id']?>" data-client="<?=$CLIENT_ID?>" type="button" name="publish[<?=$lr['id']?>]"><?=$l['lc_228']?></button>
											<?php
											echo '<div  id="qreply_'.$lr['uniq_id'].'" class="qreply_wrap" style="display:none;">';
												// include 'blocks/qreply.php';
											echo '</div>';
											} ?>

											<button class="look-more__btn look-more__btn_triangle look-more__contest answer-button__btn answer-button__cancel vs_btn_claim"><?=$l['lc_240']?></button>
										</div>
										<div class="contest-toggle contest_hide">
											<div class="contest">
												<div class="radio-button radio-button__reviews">
													<input name="contest[<?=$lr['id']?>]" value="1" type="radio" id="radio<?=$lr['id']?>" class="radio-button__hide" <?php echo glob_checked($lr['claim_type'], 1);?>>
													<label for="radio<?=$lr['id']?>" class="radio-button__custom"></label>
													<label for="radio<?=$lr['id']?>" class="radio-button__title"><?=$l['lc_242']?></label>
												</div>
												<div class="radio-button radio-button__reviews">
													<input name="contest[<?=$lr['id']?>]" value="2" type="radio" id="radiok<?=$lr['id']?>" class="radio-button__hide" <?php echo glob_checked($lr['claim_type'], 2);?>>
													<label for="radiok<?=$lr['id']?>" class="radio-button__custom"></label>
													<label for="radiok<?=$lr['id']?>" class="radio-button__title"><?=$l['lc_243']?></label>
												</div>
												<div class="radio-button radio-button__reviews">
													<input name="contest[<?=$lr['id']?>]" value="3" type="radio" id="radioz<?=$lr['id']?>" class="radio-button__hide" <?php echo glob_checked($lr['claim_type'], 3);?>>
													<label for="radioz<?=$lr['id']?>" class="radio-button__custom"></label>
													<label for="radioz<?=$lr['id']?>" class="radio-button__title"><?=$l['lc_244']?></label>
												</div>
											</div>

											<textarea class="look-more__textarea" name="claim_txt[<?=$lr['id']?>]"><?=$lr['claim_txt']?></textarea>

											<div class="more-answer-hide">
												<div class="more-answer">
													<div class="answer-button">
														<button class="answer-button__btn answer-button__submit" name="claim[<?=$lr['id']?>]"><?=$l['lc_16']?></button>
														<button type="reset" class="answer-button__btn answer-button__cancel"><?=$l['lc_17']?></button>
													</div>
												</div>
											</div>

										</div>
										
										<div id="answerArea">
												<textarea class=" look-more__textarea" name="replyTxt[<?=$lr['id']?>]"></textarea>
												<input type="hidden" name="userName[<?=$lr['id']?>]" value="<?=$lr['name']?>">
												<input type="hidden" name="userMail[<?=$lr['id']?>]" value="<?=$lr['mail']?>">
												<input type="hidden" name="userPhone[<?=$lr['id']?>]" value="<?=$lr['phone']?>">
												
												<div class="more-answer-hide">
													<div class="more-answer">
														<div class="more-answer__toggle">
															<div class="more-answer__wrap"></div>
														</div>
                    
														<div class="answer-button">
															<button class="answer-button__btn answer-button__submit" name="reply[<?=$lr['id']?>]"><?=$l['lc_16']?></button>
															<button type="reset" class="answer-button__btn answer-button__cancel"><?=$l['lc_17']?></button>
														</div>
													</div>
												</div>
											</div>
											<?php if(!$vs_HasCompetenties){

											    # History reply
										      	if($lr['reply'] != ''){ ?>
										      	  <div>
														    <div class="vs_history vs_history_sms wrap_review vs_history_close" >
														        <p><small style="color:#5d4a49"><?=$reply_date?> вiд <?=$lr['cmnt_poster']?></small></p>
														        <p class="rating-soc__subtitle" style="font-size:1.6rem"><?=$lr['reply']?></p>
														        <div class="accordion_title">Бiльше</div>
														    </div>
														  </div>
										      <?php	}
										      $review_reply    = db_array(mysql_query("SELECT * FROM `".PREFIX."review_reply`  WHERE `review_id` = ".$lr['uniq_id']));
										      if($review_reply) {
										      	$hideAll = $lr['reply'] ? 1 : 0;
										        getTempSMS($review_reply, 2, 'Дивитись ще коментарi', $hideAll);
										      }
											} ?>
									</div>

								</div>
							</div>
						</article>
								<?php
							}
							?>	
			
					</div>

				</form>
				<div class="reviews-next">
					<div class="pagination reviews-next__pagination reviews-next__pagination_hide"></div>
					<div class="pagination reviews-next__pagination">
					<?php
					$pc->PrintList();
					?>
					</div>
				</div>
			</main>