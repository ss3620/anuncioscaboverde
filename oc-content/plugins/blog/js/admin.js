$(document).ready(function(){


  // ON BLOG POST/STATUS SELECT IN COMMENTS RELOAD PAGE
  $('body').on('change', 'select#blogId', function(e){
    window.location.replace($(this).attr('rel') + "&blogId=" + $(this).val());
  });

  $('body').on('change', 'select#enabled', function(e){
    window.location.replace($(this).attr('rel') + "&enabled=" + $(this).val());
  });


  // REMOVE IS_BLANK CLASS
  $('body').on('click', 'input#s_slug', function(e){
    $(this).removeClass('is_blank');
  });


  // BLOG SLUG
  $('body').on('keyup keypress keydown', 'input#s_slug', function(e){
    var slug = this.value.replace(/\s/g, '-').toLowerCase();

    if(slug.match(/[^a-zA-Z0-9_-]/g)) {
      this.value = slug.replace(/[^a-zA-Z0-9_-]/g, '');
    } else {
      this.value = slug;
    }    
  });

  $('body').on('keyup keypress keydown', 'input#s_title', function(e){
    if($('input#s_slug').val() == '') {
      $('input#s_slug').addClass('is_blank');
    }

    if($('input#s_slug').hasClass('is_blank')) {
      var slug = this.value.replace(/\s/g, '-').toLowerCase();
      
      if(slug.match(/[^a-zA-Z0-9_-]/g)) {
        $('input#s_slug').val(slug.replace(/[^a-zA-Z0-9_-]/g, ''));
      } else {
        $('input#s_slug').val(slug);
      }
    }
  });




  // USER LOOKUP
  var name = $('.mb-user-edit input[name="s_os_name"], .mb-comment-edit input[name="s_os_name"]');

  if(name.length) {
    name.prop('autocomplete', 'off');
    
    name.autocomplete({
      source: user_lookup_base,
      minLength: 0,
      select: function (event, ui) {
        if (ui.item.id == '') {
          return false;
        } else {
          $.getJSON(
            user_lookup_url + ui.item.id,
            {'s_username': name.val()},
            function(data){
              if(data.user.id != 0) {
                $('#s_os_name').val(data.user.name);
                $('#s_os_email').val(data.user.email);
              } else {
                blg_message(user_lookup_error, 'error');
              }
            }
          );
        }

        $('#fk_i_user_id, #fk_i_os_user_id').val(ui.item.id);
      },
      search: function () {
        $('#fk_i_user_id, #fk_i_os_user_id').val('');
      }
    });

    $('.ui-autocomplete').css('zIndex', 10000);
  }
 

  // HIDE MESSAGE
   $('body').on('click', '.mb-message-js', function(e){
     e.preventDefault();
     $('.mb-message-js > div').fadeOut(300, function() {
       $('.mb-message-js > div').remove();
     });
  });
   

  // CHANGE FILE INPUT NAME
  $('input[name="image"]').change(function() {
    if( $(this)[0].files[0]['name'] != '' ) {
      $('.mb-file .wrap > span').text( $(this)[0].files[0]['name'] );
    }
  });


  // CATEGORY MULTI SELECT
  $('body').on('change', '.mb-row-select-multiple select', function(e){
    $(this).closest('.mb-row-select-multiple').find('input[type="hidden"]').val($(this).val());
  });


  // ON LOCALE CHANGE RELOAD PAGE
  $('body').on('change', 'select.mb-select-locale', function(e){
    window.location.replace($(this).attr('rel') + "&blgLocale=" + $(this).val());
  });


  // HELP TOPICS
  $('#mb-help > .mb-inside > .mb-row.mb-help > div').each(function(){
    var cl = $(this).attr('class');
    $('label.' + cl + ' span').addClass('mb-has-tooltip').prop('title', $(this).text());
  });

  $('.mb-row label').click(function() {
    var cl = $(this).attr('class');
    
    if($('#mb-help > .mb-inside > .mb-row.mb-help > div.' + cl).length) {
      var pos = $('#mb-help > .mb-inside > .mb-row.mb-help > div.' + cl).offset().top - $('.navbar').outerHeight() - 12;
      $('html, body').animate({
        scrollTop: pos
      }, 1400, function(){
        $('#mb-help > .mb-inside > .mb-row.mb-help > div.' + cl).addClass('mb-help-highlight');
      });

      return false;
    }
  });


  // ON-CLICK ANY ELEMENT REMOVE HIGHLIGHT
  $('body, body *').click(function(){
    $('.mb-help-highlight').removeClass('mb-help-highlight');
  });


  // GENERATE TOOLTIPS
  Tipped.create('.mb-has-tooltip', { maxWidth: 200, radius: false, behavior: 'hide' });
  Tipped.create('.mb-has-tooltip-user', { maxWidth: 350, radius: false, size: 'medium', behavior: 'hide' });


  // CHECKBOX & RADIO SWITCH
  $.fn.bootstrapSwitch.defaults.size = 'small';
  $.fn.bootstrapSwitch.defaults.labelWidth = '0px';
  $.fn.bootstrapSwitch.defaults.handleWidth = '50px';

  $(".element-slide").bootstrapSwitch();



  // MARK ALL
  $('input.mb_mark_all').click(function(){
    if ($(this).is(':checked')) {
      $('input[name^="' + $(this).val() + '"]').prop( "checked", true );
    } else {
      $('input[name^="' + $(this).val() + '"]').prop( "checked", false );
    }
  });

});


var timeoutHandle;

function blg_message($html, $type = '') {
  window.clearTimeout(timeoutHandle);

  $('.mb-message-js').fadeOut(0);
  $('.mb-message-js').attr('class', '').addClass('mb-message-js').addClass($type);
  $('.mb-message-js').fadeIn(200).html('<div>' + $html + '</div>');

  var timeoutHandle = setTimeout(function(){
    $('.mb-message-js > div').fadeOut(300, function() {
      $('.mb-message-js > div').remove();
    });
  }, 5000);
}