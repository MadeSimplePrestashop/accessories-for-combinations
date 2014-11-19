
$(document).ready(function() {
if (!!$.prototype.bxSlider)
		$('#bxslider').bxSlider({
			minSlides: 1,
			maxSlides: 6,
			slideWidth: 178,
			slideMargin: 20,
			pager: false,
			nextText: '',
			prevText: '',
			moveSlides:1,
			infiniteLoop:false,
			hideControlOnEnd: true
		});
})