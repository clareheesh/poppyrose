<?php
/** Template Name: Map */

get_header(); ?>

<?php if (have_posts()): ?>

	<?php while (have_posts()) : the_post(); ?>

		<div class="container">
			<div class="col-xs-12 entry-content">
				<h1><?php the_title(); ?></h1>

				<div>
					<?php the_content(); ?>
				</div>
			</div>
		</div>

		<div id="map" style="width:100%; height:500px;"></div>

	<?php endwhile; ?>
<?php endif; ?>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8VtA1T2GXX-BXv-JKUT1c1dykk3yF3v8&callback=initMap"
	        async defer></script>
	<script>
		var marker;
		function initMap() {
			var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 13,
				center: {lat: -27.469392, lng: 153.068928},
				scrollwheel: false,
				styles: [{
					"elementType": "geometry",
					"stylers": [
						{"saturation": -17},
						{"lightness": 13},
						{"gamma": 0.78},
						{"weight": 0.4},
						{"visibility": "simplified"}
					]
				}]
			});

			marker = new google.maps.Marker({
				map: map,
				draggable: true,
				animation: google.maps.Animation.DROP,
				position: {lat: -27.4647266, lng: 153.0802232},
				title: 'Poppy Rose'
			});

			<?php if(get_field('map_info_content')) : ?>
				var contentString = '<div id="content">'+
				'<div id="siteNotice">'+
				'</div>'+
				<?= json_encode(get_field('map_info_content')); ?> +
				'</div>'+
				'</div>';

				var infowindow = new google.maps.InfoWindow({
					content: contentString
				});

				marker.addListener('click', function() {
					infowindow.open(map, marker);
				});
			<?php endif; ?>
		}
	</script>

<?php
get_footer(); ?>