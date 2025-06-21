<?php

namespace Drupal\bc_api_base\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
   * Will validate and set query parameters based on annotations.
   */
  public function autoParams();

  /**
   * Set query params based on request.
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
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Actual request.
   *
   * @return \Symfony\Component\HttpFoundation\HttpResponse
   *   An HTTP response.
   */
  public function getResource(Request $request);

  /**
   * Get Api Resource List.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Actual request.
   *
   * @return \Symfony\Component\HttpFoundation\HttpResponse
   *   An HTTP response.
   */
  public function getResourceList(Request $request);

  /**
   * Create the response object.
   *
   * We allow subclasses to override this method if they need to create
   * different types of responses, however, they can also alter the response
   * later, if they only need minor changes.
   *
   * @return Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function createResponse(): Response;

  /**
   * Alter the response object before executing.
   *
   * @param \Symfony\Component\HttpFoundation\Response $response
   *   The response object to alter.
   */
  public function responseAlter(Response $response);

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
