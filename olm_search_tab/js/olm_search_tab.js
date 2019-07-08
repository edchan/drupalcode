(function ($) {
  "use strict";

  Drupal.behaviors.olm_search_tab = {
    attach: function (context) {

      var default_city_name = Drupal.settings.olm.default_city_name;

      $(document).ready(function(){
        var id = $('#block-olm-search-tab-olm-search-tab-block .tab-pane:first').attr('id');
        var data_all_types = $('#'+id).attr('data-all-types');
        $('.map-button').attr("href",  'q?city=' + default_city_name + '&mode=map-view&type=' + data_all_types );
      });

      $('#block-olm-search-tab-olm-search-tab-block .nav-tabs a').click(function() {
         var id = $(this).attr('href');
         var data_all_types = $(id).attr('data-all-types');
         $('.map-button').attr("href",  'q?city=' + default_city_name + '&mode=map-view&type=' + data_all_types );

         var submit_url = $('.Search__Button--submit').attr('href');
         submit_url = submit_url.replace(/(&type=.*(?=&))|(&type=.*)/i, '&type=' + data_all_types);
         $('.Search__Button--submit').attr('href', submit_url);
      });
    }
  };

})(jQuery);
