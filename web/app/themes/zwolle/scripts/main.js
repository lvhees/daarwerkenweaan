jQuery(function($){

	window.p = console ? function(){
		console.log(arguments.length > 1 ? arguments : arguments[0]);
	} : function(){};

	// Menu icon
	$('.menu_icon').on('click', function(e){
		(e).preventDefault();

		$('body').toggleClass('show_menu');
	});

	var caroussel = $('.caroussel');
	if(caroussel.length > 0){
		(function(){
			var caroussel_speed = 6000;
			caroussel.slick({
				'autoplay': false,
				'fade': true,
				'speed': 1000,
				'dots': true,
				'arrows': false,
				'swipeToSlide': true,
				'pauseOnHover': false,
				'pauseOnDotsHover': false,
				'autoplaySpeed': caroussel_speed
			});

			var progress = 0,
				progress_bar = $('.bar', '.progress'),
				progress_pause = $('.pause', '.progress'),
				last_update = new Date().getTime(),
				paused = false,
				tempPaused = false;
			var resetProgress = function(){
				progress = 0;
			};

			var updateProgress = function(){
				if(tempPaused || paused) return;

				var now = new Date().getTime();
				progress += (now - last_update);
				progress_bar.css({
					'transform': 'scaleX(' + (progress / caroussel_speed) + ')'
				});
				last_update = now;

				requestAnimFrame(updateProgress);
			};

			var pauseSlider = function(){
				tempPaused = true;
				caroussel.slick('slickPause');
			};

			var playSlider = function(){
				tempPaused = false;
				if(!paused)
					caroussel.slick('slickPlay');

				progress = 0;
				last_update = new Date().getTime();
				requestAnimFrame(updateProgress);
			};

			var togglePause = function(){
				//p(caroussel.slick());
				progress_pause.toggleClass('paused');
				caroussel.slick('slickPlay');
				paused = !paused;
				progress = 0;
			};

			progress_pause.on('click', togglePause);

			caroussel.on('beforeChange', resetProgress);
			caroussel.on('mouseenter', pauseSlider);
			caroussel.on('mouseleave', playSlider);

			requestAnimFrame(updateProgress);
			caroussel.slick('slickPlay');

			$('body').addClass('has-caroussel');

		})();
	}

	if($('.home').length > 0){

		var ticker = $('.ticker');
		if(ticker.length > 0){
			(function() {
				var inner = $('.inner', ticker),
					inner_width = inner.width(),
					cloned = false,
					paused = false,
					pos = 0,
					speed = 2;

				var update_ticker = function () {
					if (paused) return;

					inner.css({'transform': 'rotateZ(360deg) translate(' + (pos -= speed) + 'px, -50%)'});

					if (!cloned) {
						inner.html(inner.html() + inner.html());
						cloned = true;
					}
					else if (inner_width + pos <= 0) {
						pos = -speed;
					}

					requestAnimFrame(update_ticker);
				};

				requestAnimFrame(update_ticker);

				ticker.on('mouseenter', '.inner', function () {
					paused = true;
				});

				ticker.on('mouseleave', '.inner', function () {
					paused = false;
					requestAnimFrame(update_ticker);
				});
			})();
		}

	}

});

function base64_decode(data) {

	var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
		ac = 0,
		tmp_arr = [],
		dec;

	if (!data) {
		return data;
	}

	data += '';

	do {
		// unpack four hexets into three octets using index points in b64
		h1 = b64.indexOf(data.charAt(i++));
		h2 = b64.indexOf(data.charAt(i++));
		h3 = b64.indexOf(data.charAt(i++));
		h4 = b64.indexOf(data.charAt(i++));

		bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

		o1 = bits >> 16 & 0xff;
		o2 = bits >> 8 & 0xff;
		o3 = bits & 0xff;

		if (h3 == 64) {
			tmp_arr[ac++] = String.fromCharCode(o1);
		} else if (h4 == 64) {
			tmp_arr[ac++] = String.fromCharCode(o1, o2);
		} else {
			tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
		}
	} while (i < data.length);

	dec = tmp_arr.join('');

	return decodeURIComponent(escape(dec.replace(/\0+$/, '')));
}
