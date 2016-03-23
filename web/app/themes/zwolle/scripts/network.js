var Network = Base.extend({

	map: null,
	map_clusterer: null,
	markers: null,
	fields: null,

	views: null,
	users: null,
	active_item: null,

	active_view: 'map',
	options: {},

	constructor: function(container, options){
		var self = this;

		self.options = jQuery.extend({}, self.options, options || {});
		self.container = jQuery(container);
		self.items = [];
		self.item_data = JSON.parse(base64_decode(self.options.items));

		// Add filter view events
		if(self.options.fields_dropdown){
			self.fields_dropdown = jQuery(self.options.fields_dropdown);
			self.fields_dropdown.on('change', jQuery.proxy(self.filter, self));
			self.fillFields();
		}

		// Views
		self.view_container = jQuery(self.options.view_container);
		if(self.options.view_toggle){
			self.views = {
				'list': self.options.list_view ? jQuery(self.options.list_view) : null,
				'map': self.options.map_view ? jQuery(self.options.map_view) : null
			};

			self.view_toggle = jQuery(self.options.view_toggle);
			self.view_toggle.on('click', 'a', jQuery.proxy(self.changeView, self));

			if(self.views.list){
				self.views.list.on('click', '.item', function(e){
					self.selectItem(jQuery(e.currentTarget).data('data'));
				});
			}
		}

		self.view_container.on('click', '.close-overlay', function(e){
			(e).preventDefault();
			self.clearSelection();
		});

		// Create the map
		self.createMap();

		// Add markers
		jQuery.each(self.item_data || [], function(nr, item){
			self.createItem(item);
		});

		self.map_clusterer = new MarkerClusterer(self.map, [],{
			styles: [{
				url: self.options.marker_icon,
				height: 51,
				width: 27,
				textColor: '#ffffff',
				textSize: 16,
				iconAnchor: [-13, 51]
			}]
		});

		if(self.options.fields_dropdown) {
			self.filter();
		}

	},

	createMap: function() {
		var self = this;

		self.map = new google.maps.Map(self.container.get(0), {
			zoom: self.options.zoom,
			center: self.options.latlng,
			disableDefaultUI: true,
			styles: [
				{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}
			]
		});

		google.maps.event.addListener(self.map, 'click', function(){
			self.clearSelection();
		});

	},

	createItem: function(data){
		var self = this;

		// Map Marker
		if(data.location){
			data.location.lat = data.location.lat + (0.0005 - Math.random()/1000);
			data.location.lng = data.location.lng + (0.0005 - Math.random()/1000);

			var marker = new google.maps.Marker({
				position: data.location,
				map: self.map,
				customInfo: data || {},
				icon: {
					url: self.options.marker_icon,
					size: new google.maps.Size(27, 51),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(13, 51)
				}
			});

			google.maps.event.addListener(marker, 'click', function(){
				self.selectItem(marker);
			});
			google.maps.event.addListener(marker, 'mouseover', function() {
				marker.setIcon(self.options.marker_icon_hover);
			});
			google.maps.event.addListener(marker, 'mouseout', function() {
				marker.setIcon(self.options.marker_icon);
			});
		}

		// List item
		var list_item;
		if(self.views && self.views.list){
			list_item = jQuery('<div class="item" />')
				.data('data', data)
				.append(
					jQuery('<div class="avatar" />').css({'background-image': 'url(' + data.avatar + ')'}),
					jQuery('<div class="name" />').html(data.name)
				)
				.appendTo(self.views.list);
		}

		// Save for later
		self.items.push({
			'data': data,
			'marker': marker,
			'list_item': list_item
		});

	},

	selectItem: function(marker){
		var self = this,
			data = marker.customInfo ? marker.customInfo : marker,
			already_active = self.active_item && self.active_item.marker == marker;

		self.clearSelection();
		if(already_active) return; // Only close active item

		var item_popup;
		if(data.type == 'loket'){
			item_popup = jQuery('<div class="item-overlay hide" />').append(
				jQuery('<div class="name" />').html(data.name),
				jQuery('<div class="company" />').html(data.address),
				jQuery('<div class="contact" />').append(
					jQuery('<div class="phone" />')
						.attr('href', 'tel:' + data.phone)
						.html(data.phone),
					jQuery('<div class="email" />')
						.attr('href', 'mailto:' + data.email)
						.html(data.email)
				),
				jQuery('<div class="content" />').html(data.content)
			);
		}
		else {
			var fields = [];
			jQuery.each(data.fields, function(nr, field){
				fields.push(jQuery('<div class="field" />').html(field));
			});

			item_popup = jQuery('<div class="item-overlay hide" />').append(
				jQuery('<a href="#" class="close-overlay">Sluiten</a>'),
				jQuery('<div class="user-wrapper" />').append(
					jQuery('<div class="avatar" />').css({'background-image': 'url(' + data.avatar + ')'}),
					jQuery('<div class="user" />').append(
						jQuery('<div class="name" />').html(data.name),
						jQuery('<div class="function" />').html(data.function)
					)
				),
				self.isLoggedIn() ? [
					fields && fields.length > 0 ? jQuery('<div class="fields" />').append(
						'<h3>Specialismen</h3>',
						fields
					) : null,
					jQuery('<div class="company" />').append(
						'<h3>Werkzaam bij</h3>',
						data.company,
						data.company_url ? ('<a href="' + data.company_url  + '">' + data.company_url  + '</a>') : null
					),
					data.phone || data.email ? jQuery('<div class="contact" />').append(
						'<h3>Contact informatie</h3>',
						data.phone ? jQuery('<a class="phone" />')
							.attr('href', 'tel:' + data.phone)
							.html(data.phone) : null,
						data.email ? jQuery('<a class="email" />')
							.attr('href', 'mailto:' + data.email)
							.html(data.email) : null
					) : null,
					data.motto ? jQuery('<div class="motto" />').append(
						'<h3>Motto</h3>',
						data.motto
					) : null
				] : [
					jQuery('<div class="login" />').append(
						'Om alle gegevens te zien, dient u zich <a href="/registreren">aan te melden</a>'
					)
				]
			);
		}


		self.view_container.append(item_popup);
		requestTimeout(function(){
			item_popup.removeClass('hide');
		}, 50);

		self.active_item = {
			'marker': marker,
			'popup': item_popup
		};

	},

	isLoggedIn: function(){
		return this.options.user;
	},

	clearSelection: function(){
		var self = this;

		if(self.active_item){
			var old_user = self.active_item;
			old_user.popup.addClass('hide');
			requestTimeout(function(){
				old_user.popup.remove();
			}, 1000);

			self.active_item = null;
		}

	},

	fillFields: function(){
		var self = this;

		self.fields = [];

		jQuery.each(self.item_data, function(nr, user){
			jQuery.each(user.fields || [], function(i, field) {
				if (jQuery.inArray(field, self.fields) == -1 && field){
					self.fields.push(field);
				}
			});
		});

		self.fields.sort(function(a, b){
			a = a.toLowerCase();
			b = b.toLowerCase();
			return ((a < b) ? -1 : ((a > b) ? 1 : 0));
		});

		jQuery.each(self.fields, function(nr, field){
			self.fields_dropdown.append('<option>'+field+'</option>');
		});
	},

	filter: function(e){
		var self = this,
			value = self.fields_dropdown.val();

		var markers = [];
		jQuery.each(self.items, function(nr, user){

			if(!value || user.data.fields.indexOf(value) > -1){
				if(user.marker){
					user.marker.setVisible(true);
					markers.push(user.marker);
				}
				user.list_item.addClass('active');
			}
			else {
				if(user.marker) user.marker.setVisible(false);
				user.list_item.removeClass('active');
			}

		});

		self.map_clusterer.clearMarkers();
		self.map_clusterer.addMarkers(markers);

	},

	changeView: function(e){
		var self = this;
		(e).preventDefault();

		var a = jQuery(e.currentTarget),
			to = a.data('view');

		if(to != self.active_view){
			jQuery('.active', self.view_toggle).removeClass('active');
			a.addClass('active');

			self.views[self.active_view].removeClass('active');
			self.views[to].addClass('active');

			self.active_view = a.data('view');
		}

	}

});
