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
    $new_text = preg_replace('#<p(.*?)>(.*?)</p>#is', '$2<br/>', $new_text);
    $new_text = preg_replace("/\r?\n|\r/", "", $new_text);
    $new_text = strip_tags($new_text, CINDER_ALLOWED_TAGS);

    $new_text = trim($new_text);
    $new_text = rtrim($new_text, "<br/>");

    return $new_text;
  }

}
