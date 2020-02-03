<?php

namespace Drupal\bc_api_base\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * API Controller Interface.
 */
interface ApiControllerInterface {

  /**
   * Initialize Cachetags.
   */
  public function initCacheTags();

  /**
   * Set Platform based on request.
   *
   * @return string
   *   The cache string id.
   */
  public function setPlatform();

  /**
   * Set query params based on request.
   *
   * @return string
   *   The cache string id.
   */
  public function setParams();

  /**
   * Get cache time, in seconds.
   *
   * @param string|null $id
   *   Name of specific cache time variable.
   *
   * @return int
   *   Time in seconds for cache time.
   */
  public function getApiCacheTime($id);

  /**
   * Get cache id.
   *
   * @return string
   *   The cache string id.
   */
  public function getCacheId();

  /**
   * Get Api Resource.
   *
   * @param Symfony\Component\HttpFoundation\Request $request
   *   Actual request.
   *
   * @return Symfony\Component\HttpFoundation\HttpResponse
   *   An HTTP response.
   */
  public function getResource(Request $request);

  /**
   * Get Api Resource List.
   *
   * @param Symfony\Component\HttpFoundation\Request $request
   *   Actual request.
   *
   * @return Symfony\Component\HttpFoundation\HttpResponse
   *   An HTTP response.
   */
  public function getResourceList(Request $request);

  /**
   * Get Api Resource Query Data.
   */
  public function getResourceQueryResult();

  /**
   * Get Api Resource Query Data.
   */
  public function getResourceListQueryResult();

  /**
   * From an array of raw (Drupal) data, build out our endpoint data.
   */
  public function buildAllResourceData();

  /**
   * Build Links associated with this endpoint.
   */
  public function buildLinks();

}
