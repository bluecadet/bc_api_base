<?php

// phpcs:disable
namespace Drupal\bc_api_base\Annotation;

/**
 * Class ApiDoc.
 *
 * @package Drupal\bc_api_base\Annotation
 *
 * @Annotation
 */
class ApiDoc {

  /**
   * General Description.
   *
   * @var string
   */
  public $description = "";

  /**
   * List Description.
   *
   * @var string
   */
  public $list_description = "";

  /**
   * Individual Resource Description.
   *
   * @var string
   */
  public $resource_description = "";

  /**
   * Params.
   *
   * @var array<\Drupal\bc_api_base\Annotation\ApiParam>
   */
  public $params;

}
