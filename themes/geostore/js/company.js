(function ($, Drupal,drupalSettings) {
  Drupal.behaviors.myBehavior = {
    attach: function (context, settings) {
        // The location of Uluru
        var uluru = {lat: drupalSettings.latitud, lng: drupalSettings.longitud};
        // The map, centered at Uluru
        var map = new google.maps.Map(
            document.getElementById('map'), 
            {
              mapTypeControl: true,
              zoom: 17, 
              center: uluru,
              mapTypeControlOptions: {
                mapTypeIds: []
              }
            }
          );
        // The marker, positioned at Uluru
        var marker = new google.maps.Marker({position: uluru, map: map});
    }
  };
})(jQuery, Drupal, drupalSettings);