<?php

// phpcs:disable
namespace Drupal\bc_api_base\Annotation;

/**
 * Class ApiParam.
 *
 * @package Drupal\bc_api_base\Annotation
 *
 * @Annotation
 */
class ApiParam {

  /**
   * The name.
   *
   * @Required
   * @var string
   */
  public $name;

  /**
   * Type.
   *
   * @Required
   * @var string
   */
  public $type;

  /**
   * Description.
   *
   * @var string
   */
  public $description;

  /**
   * Default.
   *
   * @var string
   */
  public $default;

  /**
   * Required.
   *
   * @var bool
   */
  public $required;

  /**
   * Range.
   *
   * @var array<int>
   */
  public $range;

}
