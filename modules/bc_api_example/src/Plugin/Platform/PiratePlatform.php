<?php

namespace Drupal\bc_api_example\Plugin\Platform;

use Drupal\bc_api_base\Plugin\Platform\PlatformBase;

/**
 * Class DefaultPlatform.
 *
 * @Platform(
 *    id = "pirate",
 *    label = "Pirate Platform",
 *    striphtml = "none",
 *    urldecode = "FALSE",
 *    unescapechars = "FALSE",
 *    imageurl = "FALSE",
 * )
 */
class PiratePlatform extends PlatformBase {

}
