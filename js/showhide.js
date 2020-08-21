(function ($) {
  'use strict';

  Drupal.behaviors.toggle = {
  attach: function (context, settings) {
    $( "#show-example" ).click(function () {
      // Do it afterwards as the operation is async
      $("#example").slideToggle("slow");
  })
}};

}(jQuery));
