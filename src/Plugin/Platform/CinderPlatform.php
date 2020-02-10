<?php

namespace Drupal\bc_api_base\Plugin\Platform;

/**
 * Class CinderPlatform.
 *
 * @Platform(
 *    id = "cinder",
 *    label = "Cinder",
 *    striphtml = "cinder",
 *    urldecode = "FALSE",
 *    unescapechars = "FALSE",
 *    imageurl = "FALSE",
 * )
 */
class CinderPlatform extends PlatformBase {

  /**
   * {@inheritdoc}
   */
  public function applyPlatformTransformations(string $text) {

    $new_text = $text;
    $new_text = strip_tags($text, CINDER_ALLOWED_TAGS);
    $new_text = preg_replace("/\r?\n|\r/", "", $new_text);

    $new_text = trim($new_text);

    return $new_text;
  }

}
