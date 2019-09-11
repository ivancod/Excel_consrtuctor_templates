<?php
  include "../params/connect.php";
  include "../params/function.php";

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

<script defer>
$(document).ready(function() {
// picker start

  let arrow = `<svg  xmlns="http://www.w3.org/2000/svg" class="arrow-svg"  viewBox="0 0 357 357" width="12" width="12">
                  <g id="play-arrow" style="transform: rotate(90deg); transform-origin: center;" fill="#000" fill-opacity=".87">
                    <polygon points="38.25,0 38.25,357 318.75,178.5"/>
                  </g>
              </svg>`;
  $('.names-TMP').picker({
    containerClass: 'vs_select_wrap tegs_wrap',
    search : true,
    texts: {
        trigger : "Oберiть",
        noResult : "Немає результатiв",
        search : "Пошук"
    }
  });
  let selected = $('.names-TMP').next('.picker').find('.pc-element.pc-trigger');
  selected.html(selected.html()+arrow);
  $('.names-TMP').next('.picker').find('g[fill="#000"]').attr('fill', '#ddbcac');
  if($('.names-TMP').find('option').length == $('.names-TMP').find('option[selected]').length) $('.names-TMP').next('.picker').find('.pc-element.pc-trigger').hide().removeClass('pc-element-active');
  $('.names-TMP').next('.picker').find('.pc-element.pc-trigger');
  $('.pc-element.pc-trigger').on('click', function(){
      if(!$(this).hasClass('pc-element-active')) {
        $then = $($(this).next('.pc-list')[0]); 
        $then.attr('style', '');
        $(this).addClass('pc-element-active');
      } else {
        $(this).removeClass('pc-element-active');
      }
  });
  $('.arrow-svg').on('click', function(){
    $(this).parent().addClass('pc-element-active');
    $then = $($(this).parent().next('.pc-list'));
    if($(this).parent().hasClass('pc-element-active')) {
      $then.attr('style', '');
      $(this).parent().addClass('pc-element-active');
    }
  });
  $(document).click(function(e){
    let node = $('.pc-trigger, .arrow-svg, .pc-list input');
    if(!node.is(e.target)) {
      node.removeClass('pc-element-active');
    }
  });

// picker end

// tabs start

  $('.tabs__list').each(function(i) {
    var storage = localStorage.getItem('tab' + i);
    if (storage) {
      $(this).find('li').removeClass('active-tab').eq(storage).addClass('active-tab')
        .closest('div.tabs').find('div.tabs__content').removeClass('active-tab').eq(storage).addClass('active-tab');
    }
  });

  $('.tabs__list').on('click', 'li:not(.active-tab)', function() {
    $(this)
      .addClass('active-tab').siblings().removeClass('active-tab')
      .closest('div.tabs').find('div.tabs__content').removeClass('active-tab').eq($(this).index()).addClass('active-tab');
    var ulIndex = $('.tabs__list').index($(this).parents('.tabs__list'));
    localStorage.removeItem('tab' + ulIndex);
    localStorage.setItem('tab' + ulIndex, $(this).index());
  });

// tabs end

// multiselect start

  $('#undo_redo').multiselect({keepRenderingSort: true});
  $('#up_redo').multiselect({keepRenderingSort: true});

// multiselect end

// AJAX start

  let container = $('#const_wrap');
  let tmp_type  = 'tmp_type='+$('#constructor_excel').attr('data-type');
  let CLIENT_ID = 'CLIENT_ID='+$('#constructor_excel').attr('data-client');
  let argTitle  = 'argTitle='+$('#constructor_excel').attr('data-argTitle');
  let argWidth  = 'argWidth='+$('#constructor_excel').attr('data-argWidth');

  function Refresh(){
    let upTMP     = 'upTMP='+$('.sel_up_tmp option[selected]').val();
    console.log(`../blocks/excel_constructor.php?&${CLIENT_ID}&${argTitle}&${argWidth}&${upTMP}&${tmp_type}`);
    $.ajax({ url: `../blocks/excel_constructor.php?&${CLIENT_ID}&${argTitle}&${argWidth}&${upTMP}&${tmp_type}`,
      success: function(data) { 
        container.html(data);   
      } 
    });
  }
  function ajaxAfter(){
    Refresh();
  }

  $('#create_tmp_excel').on('click', function(){
    let sel   = $('#undo_redo_to option');
    let title = 'tmp_title='+$('.tmpCr_title input').val();
    let field = 'field=';
    for(let i = 0; i < sel.length; i++) {
      if(i != 0) field += ','; 
      field += $(sel[i]).val();
    }
    console.log(`../blocks/excel_constructor.php?create_tmp_excel=1&${CLIENT_ID}&${tmp_type}&${title}&${field}`);
    $.ajax({ url: `../blocks/excel_constructor.php?create_tmp_excel=1&${CLIENT_ID}&${tmp_type}&${title}&${field}`, 
      success: function(data) { 
        ajaxAfter();
      }
    });  
  });


  $('#update_tmp_excel').on('click', function(){
    let sel   = $('#up_redo_to option');
    let tmp   = 'tmp_id='+$('.sel_up_tmp option[selected]').val();
    let title = 'tmp_title='+$('.tmpUp_title input').val();
    let field = 'field=';
    for(let i = 0; i < sel.length; i++) {
      if(i != 0) field += ','; 
      field += $(sel[i]).val();
    }
    console.log(`../blocks/excel_constructor.php?update_tmp_excel=1&${tmp}&${CLIENT_ID}&${tmp_type}&${title}&${field}`);
    $.ajax({ url: `../blocks/excel_constructor.php?update_tmp_excel=1&${tmp}&${CLIENT_ID}&${tmp_type}&${title}&${field}`, 
      success: function(data) { 
        ajaxAfter(); 
      } 
    });
  });


  $('#delete_tmp_excel').on('click', function(){
    let sel = $('.active-tab').find('option[selected]');
    let tmp_id = 'tmp_id=';
    for(let i = 0; i < sel.length; i++) {
      if(i != 0) tmp_id += ','; 
      tmp_id += $(sel[i]).val();
    }
    console.log(`../blocks/excel_constructor.php?delete_tmp_excel=1&${tmp_id}`);
    $.ajax({ url: `../blocks/excel_constructor.php?delete_tmp_excel=1&${tmp_id}`,
       success: function(data) { 
        ajaxAfter(); 
      } 
     });
  });

  $('.sel_up_tmp').on('sp-change', ajaxAfter);

// AJAX end

// refresh list TMP start

  $('#excel_wrap').html('');
  $('#excel_wrap').append($('#const_wrap .export_excel'));
  $('#excel_wrap .look-more__btn').on('click', function(){
    console.log(this);
    $('.letter-holder').addClass('letter-holder-active');
    $('#excel_wrap .look-more__btn').attr('disabled', 'true');
    let tmp = $(this).attr('date-TMP');
    let title = '<span class="text_temp" style="text-align:center; width:100%"> ('+$(this).text()+') </span>';
    $.post("#", { exp_all: "1", temp_excel: tmp },
      function(data) {
        $('#excel_wrap .text_temp, #excel_wrap .excel_link').remove();
        let link = $(data).find('.excel_link');
        link.children('p').append(title);
        $('#excel_wrap').append(link);
        $('#excel_wrap .look-more__btn').removeAttr('disabled');
        $('.letter-holder').removeClass('letter-holder-active');
        console.log(link);
      }
    );
  });

// refresh list TMP end

// check on required start

  function validName(field, btn){
    if($(field).val() < 1) $(btn).hide();
    $(field).on('keyup',function(){
      let val = $(this).val();
      if(val.length >= 1){
        $(btn).show(300);
      } else {
        $(btn).hide(300);
      }
    });
  }
  validName('.tmpCr_title input', '#create_tmp_excel');
  validName('.tmpUp_title input', '#update_tmp_excel');

// check on required TMP end

});

</script>