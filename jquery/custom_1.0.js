$(function() {
	$("h2.pagetitle span").html(function(i, text) {
		return text.replace(/[a-zàâîïôèéêëèùûü]+/i, function(match) {
			return '<span class="firstword">' + match + '</span>';
		});
	});
	
	$('#da-slider').cslider({
		autoplay	: true,
		interval : 6000
	});
        
        //Define paths
        var basepath = window.location.protocol+'//'+window.location.hostname+'/';
        
        $('.itemslistaction').click(function(){
            var id = this.id;

            if ($('#itemlist-'+id).css('display') == 'none') {
                $('#image-itemlist-'+id).attr('src', basepath+'/theme/image.php?theme=utold&image=switch_minus&component=itemslist');
                $('#itemlist-'+id).css('display', 'block');
            } else {
                $('#image-itemlist-'+id).attr('src', basepath+'/theme/image.php?theme=utold&image=switch_plus&component=itemslist');
                $('#itemlist-'+id).css('display', 'none');
            }

        });
});