$(document).ready(function() {

  // WIDGET HOVER EFFECT - ON
  $('body').on('mouseenter', '.blg-card-link', function(e){
    $(this).parent().find('.blg-img-div > div').stop(false, false).animate({height: "120%",width:"120%",left:"-10%",top:"-10%"});
  });


  // WIDGET HOVER EFFECT - OFF
  $('body').on('mouseleave', '.blg-card-link', function(e){
    $(this).parent().find('.blg-img-div > div').stop(false, false).animate({height: "100%",width:"100%",left:"0",top:"0"});
  });


  // LIGHT GALLERY
  if(typeof $.fn.lightGallery !== 'undefined') {
    $('.blg-primary-img').each(function() {
      var $gallery = $(this);

      if($gallery.find('a').length === 0) {
        var imgSrc = $gallery.find('img').attr('src');

        if(imgSrc) {
          $gallery.wrapInner('<a href="' + imgSrc + '"></a>');
        }
      }

      $gallery.lightGallery({
        mode: 'lg-fade',
        thumbnail: true,
        cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)',
        download: false,
        share: false,
        selector: 'a'
      });
    });
  }


  // CHANGE FILE INPUT NAME
  $('input[name="image"]').change(function() {
    if( $(this)[0].files[0]['name'] != '' ) {
      $('.blg-att .wrap > span').text( $(this)[0].files[0]['name'] );
    }
  });


  // REMOVE IS_BLANK CLASS
  $('body').on('click', 'input#s_slug', function(e){
    $(this).removeClass('is_blank');
  });


  // BLOG SLUG
  $('body').on('keyup keypress keydown', '.blg-publish input#s_slug', function(e){
    var slug = this.value.replace(/\s/g, '-').toLowerCase();

    if(slug.match(/[^a-zA-Z0-9_-]/g)) {
      this.value = slug.replace(/[^a-zA-Z0-9_-]/g, '');
    } else {
      this.value = slug;
    }    
  });

  $('body').on('keyup keypress keydown', '.blg-publish input#s_title', function(e){
    if($('.blg-publish input#s_slug').val() == '') {
      $('.blg-publish input#s_slug').addClass('is_blank');
    }

    if($('.blg-publish input#s_slug').hasClass('is_blank')) {
      var slug = this.value.replace(/\s/g, '-').toLowerCase();
      
      if(slug.match(/[^a-zA-Z0-9_-]/g)) {
        $('.blg-publish input#s_slug').val(slug.replace(/[^a-zA-Z0-9_-]/g, ''));
      } else {
        $('.blg-publish input#s_slug').val(slug);
      }
    }
  });
});