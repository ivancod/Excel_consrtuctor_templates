<?php

  function CreateTMP($tmp_id, $CLIENT_ID, $tmp_type, $tmp_title, $fields){
    mysql_query("INSERT INTO `".PREFIX."temp_excel` (`client_id`, `type`, `title`) VALUES (".$CLIENT_ID.",".$tmp_type.",'".$tmp_title."')");
  
    echo "INSERT INTO `".PREFIX."temp_excel` (`client_id`, `type`, `title`) VALUES (".$CLIENT_ID.",".$tmp_type.",'".$tmp_title."')";
    echo $fields;
  
    $new_tmp_id = mysql_insert_id();
    foreach (explode(",", $fields) as $tpm_to) {
      mysql_query("INSERT INTO `".PREFIX."excel_fields` (`temp_id`, `field_id`) VALUES (".$new_tmp_id.",".$tpm_to.")");
    }
  }
  function DeleteTMP($tmp_id){
    mysql_query("DELETE FROM `".PREFIX."temp_excel` WHERE `id` = ".$tmp_id);
    mysql_query("DELETE FROM `".PREFIX."excel_fields` WHERE `temp_id` = ".$tmp_id);
  }

  ### GET FROM AJAX

  $client_ID  = $_GET['CLIENT_ID'] ? $_GET['CLIENT_ID'] : $CLIENT_ID;
  $tmp_title  = $_GET['tmp_title'];
  $fields     = $_GET['field'];
  $tmp_type;
  if($_GET['tmp_type'] != '') $tmp_type = $_GET['tmp_type'];

  ### Event Listener

  if(isset($_GET['create_tmp_excel'])) 
  { 
    CreateTMP(0, $client_ID, $_GET['tmp_type'], $tmp_title, $fields);
  }
  if(isset($_GET['update_tmp_excel']))
  {
    DeleteTMP($_GET['tmp_id']);
    CreateTMP($_GET['tmp_id'], $client_ID, $_GET['tmp_type'], $tmp_title, $fields);
  }
  if(isset($_GET['delete_tmp_excel']))
  {
    foreach (explode(",", $_GET['tmp_id']) as $temp_excel) {
      DeleteTMP($temp_excel);
    }
  }


  ### UPLOAD AJAX

  $argWidthString = $argWidth ? implode(",", $argWidth) : $_GET['argWidth'];
  $argTitleString = $argTitle ? implode(",", $argTitle) : $_GET['argTitle'];
  $argTitleMass = explode(",", $argTitleString);

  $hasTMP       = db_array(mysql_query("SELECT * FROM `".PREFIX."temp_excel` WHERE `client_id` = ".$client_ID));
  if($_GET['upTMP'] == 'undefined') $_GET['upTMP'] = $hasTMP[0]['id']; 
  $fieldsUpTMP;
  $upTitle;
  if($hasTMP){
    $fieldsUpTMP  = db_array(mysql_query("SELECT `field_id` FROM `".PREFIX."excel_fields` WHERE `temp_id` = ".$_GET['upTMP']));
    $upTitle      = db_array(mysql_query("SELECT `title`    FROM `".PREFIX."temp_excel`   WHERE `id` = ".$_GET['upTMP']));
    foreach ($fieldsUpTMP as $UpValue) unset($argTitleMass[$UpValue[0]]);
  }
?>

<div id="constructor_excel"  data-client="<?=$client_ID?>"  data-type="<?=$tmp_type?>" data-argWidth="<?=$argWidthString?>" data-argTitle="<?=$argTitleString?>">

  <form action=""></form>
  <div class="row"><span class="con_excel_title">Конструктор EXCEL шаблонiв</span></div>
  <div class="tabs">
    <ul class="tabs__list">
      <li class="tabs__list-item active-tab"> Додати</li>
      <?php if($hasTMP){?>
      <li class="tabs__list-item"> Редагувати</li>
      <li class="tabs__list-item"> Видалити</li>
      <? } ?>
    </ul>
    <div class="tabs__content active-tab">
      <div class="container">
        <form action="#g_constructor_excel" method="post" >
          <div class="row">
            <div class="col">
              <label class="tmp_title tmpCr_title">Назва нового шаблону<br>
                <input type="text" name="tmp_title" required>
              </label>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-7">
                <h4>Всi поля</h4>
                <select name="tmp_excel_from[]" id="undo_redo" class="form-control" size="13" multiple="multiple" >
                  <?php foreach (explode(",", $argTitleString) as $key => $value) echo "<option value='".$key."'>".$value."</option>"; ?>
                </select>
            </div>
            
            <div class="col-6 const_buttons">
              <button type="button" id="undo_redo_undo" class="look-more__btn look-more__btn_triangle">Крок назад</button>
              <button type="button" id="undo_redo_rightAll" class="look-more__btn look-more__btn_triangle">>></button>
              <button type="button" id="undo_redo_leftAll" class="look-more__btn look-more__btn_triangle"><<</i></button>
              <button type="button" id="undo_redo_redo" class="look-more__btn look-more__btn_triangle">Крок в перед</button>
              <button type="button" id="create_tmp_excel" class="reviews-action__btn" name="create_tmp_excel">Створити шаблон</button>
            </div>
            
            <div class="col-7">
              <h4>Обранi поля</h4>
              <select name="tpm_excel_to[]" id="undo_redo_to" class="form-control" size="13" multiple="multiple" required></select>
            </div>
          </div>
        </form>
      </div>  
    </div>
    <?php if($hasTMP){?>
    <div class="tabs__content">
      <div class="container">
        <form action="#g_constructor_excel" method="post" >
          <div class="row">
            <div class="col">
              <div class="select_tmp_excel select_Up_wrap">
                <p class="reviews-filter__date-name">Обрати шаблон</p>
                <?php
                if($hasTMP){ ?>
                  <select class="names-TMP sel_up_tmp" name="temp_excel" required>
                  <?php foreach($hasTMP as $k => $t) {
                    $sel;
                    if($_GET['upTMP'] == $t['id']) $sel =  ' selected ';
                    echo "<option value='".$t['id']."' ".$sel.">".$t['title']."</option>"; 
                  } ?>
                  </select>
                <? } ?>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <label class="tmp_title tmpUp_title">Нова назва шаблону<br>
                <input type="text" name="tmp_title" value="<?=$upTitle[0]['title']?>" required  >
              </label>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-7">
                <h4>Всi поля</h4>
                <select name="tmp_excel_from[]" id="up_redo" class="form-control" size="13" multiple="multiple" >
                  <?php foreach ($argTitleMass as $key => $value) echo "<option value='".$key."'>".$value."</option>"; ?>
                </select>
            </div>
            
            <div class="col-6 const_buttons">
              <button type="button" id="up_redo_undo" class="look-more__btn look-more__btn_triangle">Крок назад</button>
              <button type="button" id="up_redo_rightAll" class="look-more__btn look-more__btn_triangle">>></button>
              <button type="button" id="up_redo_leftAll" class="look-more__btn look-more__btn_triangle"><<</i></button>
              <button type="button" id="up_redo_redo" class="look-more__btn look-more__btn_triangle">Крок в перед</button>
              <button type="button" id="update_tmp_excel" class="reviews-action__btn" name="update_tmp_excel">Оновити шаблон</button>
            </div>
            
            <div class="col-7">
              <h4>Обранi поля</h4>
              <select name="tpm_excel_to[]" id="up_redo_to" class="form-control" size="13" multiple="multiple" required>
              <?php foreach ($fieldsUpTMP as $fValue) echo "<option value='".$fValue['field_id']."'>".explode(",", $argTitleString)[$fValue['field_id']]."</option>"; ?>
              </select>
            </div>
          </div>
        </form>
      </div>  
    </div>
    <div class="tabs__content">
      <div class="container">
        <form action="#g_constructor_excel" method="post" >
          <div class="row">
            <div class="col"> 
              <div class="select_tmp_excel">
                <p class="reviews-filter__date-name">Обрати шаблон</p>
                <?php if($hasTMP){ ?>
                  <select class="names-TMP" name="temp_excel[]" required multiple="multiple">
                  <?php foreach($hasTMP as $k => $t) echo "<option value=".$t['id'].">".$t['title']."</option>"; ?>
                  </select> 
                <? } ?>
                <button type="button" id="delete_tmp_excel" class="reviews-action__btn" name="delete_tmp_excel">Видалити</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <? } ?>

  </div>
</div>

<!-- модалка для выбора шаблона екстпорта  -->

<div class="export_excel">
  <span class="con_excel_title">Оберiть шаблон</span>

  <div class="listTMP">
  <?php 
    if($hasTMP){ 
      foreach($hasTMP as $k => $t) echo '<button type="button" date-TMP="'.$t['id'].'" class="look-more__btn look-more__btn_triangle listTMP-item">'.$t['title'].'</button>'; 
    } else {
      echo '<p class="reviews-filter__date-name">Шаблонiв не знайдено! Використайте контруктор для створення нового шаблону.</p>';
    }
  ?>
  </div>
  <div class="letter-holder">
    <div class="l-1 letter">.</div>
    <div class="l-2 letter">.</div>
    <div class="l-3 letter">.</div>
    <div class="l-4 letter">.</div>
    <div class="l-5 letter">.</div>
    <div class="l-6 letter">.</div>
    <div class="l-7 letter">.</div>
  </div>
</div>

