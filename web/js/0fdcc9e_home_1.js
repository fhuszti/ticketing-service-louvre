$(function () {
  	$('.scrollspy-link').on('click', function(e) {
	    e.preventDefault();
	    
	    var hash = this.hash;
	    
	    $('html, body').animate({
	      		scrollTop: $(this.hash).offset().top - 120
	    	}, 700, function(){
	      		//replace the hash in the URL with the new one
	      		window.location.hash = hash;
	    	}
	    );
  	});
});