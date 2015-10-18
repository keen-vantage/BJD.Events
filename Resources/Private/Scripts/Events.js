define(
	[
		'Library/jquery',
		'Nieuwenhuizen.BuJitsuDo/Scripts/Application',
		'Nieuwenhuizen.BuJitsuDo/Scripts/Service/HttpClient'
	],
	function($, Application, Http) {
		Application.on('ready', function () {
			$('[data-action="add-to-event"]').on('click', function(event) {
				event.preventDefault();

				var $that = $(this),
					url = $that.attr('href'),
					parent = $that.parent(),
					button = $that.find('button'),
					html = button.html(),
					removeButton = $('[data-action="remove-from-event"]', parent);

				button.attr('disabled', true);
				button.html('<i class="fa fa-spinner fa-pulse"></i>');
				Http.updateResource(url)
					.always(function() {
						button.removeAttr('disabled');
						button.html(html);
					})
					.done(function() {
						$that.addClass('hide');
						removeButton.removeClass('hide');
					});
			});
			$('[data-action="remove-from-event"]').on('click', function(event) {
				event.preventDefault();

				var $that = $(this),
					parent = $that.parent(),
					url = $that.attr('href'),
					button = $that.find('button'),
					html = button.html(),
					addButton = $('[data-action="add-to-event"]', parent);

				button.attr('disabled', true);
				button.html('<i class="fa fa-spinner fa-pulse"></i>');
				Http.updateResource(url)
					.always(function() {
						button.removeAttr('disabled');
						button.html(html);
					})
					.done(function() {
						$that.addClass('hide');
						addButton.removeClass('hide');
					});
			});

			$('[data-role="google-map"]').each(function (index, Element) {

				geocoder = new google.maps.Geocoder();
				var map = new google.maps.Map(Element, {
					zoom: 15,
					center: {lat: -34.397, lng: 150.644}
				});

				var $element = $(Element),
					address = $element.data('google-map-address'),
					location = $element.data('google-map-location'),
					locationDescription = $element.data('google-map-locationDescription'),
					street = $element.data('google-map-street'),
					city = $element.data('google-map-city'),
					zipCode = $element.data('google-map-zipcode');

				var contentString = '<h2>' + location + '</h2>' +
					'<p>' + (street ? street : '') + '<br />' + (zipCode ? zipCode: '') + '<br />' + (city ? city :'') + '</p>' +
					'<p>' + (locationDescription ? locationDescription : '') + '</p>';

				var infowindow = new google.maps.InfoWindow({
					content: contentString
				});

				geocoder.geocode( { 'address': address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						map.setCenter(results[0].geometry.location);
						var image = '/_Resources/Static/Packages/Nieuwenhuizen.BuJitsuDo/Images/logo-marker.png';
						var marker = new google.maps.Marker({
							map: map,
							position: results[0].geometry.location,
							icon: image
						});
						google.maps.event.addListener(marker, 'click', function() {
							infowindow.open(map,marker);
						});
					} else {
						console.log("Geocode was not successful for the following reason: " + status);
					}
				});
			});
		});
	}
);