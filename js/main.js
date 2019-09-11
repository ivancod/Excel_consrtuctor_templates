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

