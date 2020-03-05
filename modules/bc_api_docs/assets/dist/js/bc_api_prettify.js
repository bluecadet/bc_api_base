// eslint-disable-next-line no-unused-vars
(($, Drupal, drupalSettings) => {

  Drupal.behaviors.bc_api_prettify = {
    attach: function (context, settings) {
      PR.prettyPrint();
    }
  }

  // eslint-disable-next-line no-undef
})(jQuery, Drupal, drupalSettings);
