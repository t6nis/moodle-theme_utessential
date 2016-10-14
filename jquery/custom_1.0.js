$(function() {
    $("h2.pagetitle span").html(function(i, text) {
        return text.replace(/[a-zàâîïôèéêëèùûü]+/i, function(match) {
            return '<span class="firstword">' + match + '</span>';
        });
    });

    $('#da-slider').cslider({
        autoplay : true,
        interval : 6000
    });

    //Define paths
    var basepath = window.location.protocol+'//'+window.location.hostname+'/';

    $('.itemslistaction').click(function(){
        var id = this.id;
        if ($('#itemlist-'+id).css('display') == 'none') {
            $('#image-itemlist-'+id).attr('src', basepath+'/theme/image.php?theme=utessential&image=switch_minus&component=itemslist');
            $('#itemlist-'+id).css('display', 'block');
        } else {
            $('#image-itemlist-'+id).attr('src', basepath+'/theme/image.php?theme=utessential&image=switch_plus&component=itemslist');
            $('#itemlist-'+id).css('display', 'none');
        }
    });

    // Floating quiz_navblock.
    if ($('#page-mod-quiz-attempt').length > 0 && $(window).width() > 780) {        
        var top = $('#mod_quiz_navblock').offset().top - parseFloat($('#mod_quiz_navblock').css('margin-top').replace(/auto/, 0));
        var quizblockheight = $('#mod_quiz_navblock').height();    
        var quizblockwidth = $('#mod_quiz_navblock').width();
        window.setTimeout(showNotice, 3000);        
        $(window).scroll(function (event) {            
          var y = $(this).scrollTop();
          if (y >= top) {
            $('#page-content #block-region-side-pre').css('overflow', 'visible');
            $('#mod_quiz_navblock').addClass('fixed').css({'position':'absolute', 'top':y+10, 'z-index':'100', 'width' : quizblockwidth});
          } else {
            if ($('#mod_quiz_navblock').hasClass('fixed')) {
                $('.block_settings').css('margin-top', (quizblockheight+(top/2)+10)); 
            }  
            $('#mod_quiz_navblock').removeClass('fixed').css({'top': top, 'width' : quizblockwidth});                      
          } 
        });
    }
    // Floating quiz notice
    function showNotice() {       
        if ($('#mod_quiz_navblock .othernav #quiz-timer #quiz-time-left').html().length > 0) {
            var lang_string = 'NB! Esita enne aja lõppemist!';
            if ($('html').attr('lang') !== 'et') {
                lang_string = 'NB! Submit before the time goes out!';
            }
            $('#mod_quiz_navblock .othernav').append("<span style=\"font-weight:bold; color:#CC0033;\">"+lang_string+"</span>");
        }        
    }
    
    // Nasty hack for group command change icon on click.
    $('a.menu-action[href*="groupmode"]').click(function(){
        var imgsrc = $(this).find('img').attr('src');
        if (imgsrc.search('groupn') != -1) {
            $(this).removeClass('editing_groupsnone');
            $(this).addClass('editing_groupsseparate');
        } else if (imgsrc.search('groups') != -1) {
            $(this).removeClass('editing_groupsseparate');
            $(this).addClass('editing_groupsvisible');
        } else if (imgsrc.search('groupv') != -1) {
            $(this).removeClass('editing_groupsvisible');
            $(this).addClass('editing_groupsnone');
        }
    });
    
    // -- 14.10.2016 Call out selectmenu() on Multianswer question 
    $('#page-question-preview.course-4293 .multianswer select, #page-mod-quiz-attempt.course-4293 .multianswer select, #page-mod-quiz-review.course-4293 .multianswer select').selectmenu({});
});