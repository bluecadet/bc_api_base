<?php

namespace Drupal\bc_api_base;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait CacheableJsonResponseTrait.
 *
 * This trait provides a way to create cacheable JSON responses. It allows for
 * the addition of cacheable metadata and dependencies to the response, ensuring
 * that the response can be cached appropriately.
 */
trait CacheableJsonResponseTrait {

  /**
   * Cacheable metadata for the response.
   *
   * @var \Drupal\Core\Cache\CacheableMetadata
   */
  protected $cacheMetadata = NULL;

  /**
   * Returns the cacheable metadata for the response.
   *
   * When building any data, cacheable metadata should be set from there. And it
   * will be applied when the request is altered.
   *
   * @return \Drupal\Core\Cache\CacheableMetadata
   */
  protected function getCacheableMetadata(): CacheableMetadata {
    if (!isset($this->cacheMetadata)) {
      $this->cacheMetadata = new CacheableMetadata();
    }
    return $this->cacheMetadata;
  }

  /**
   * Adds a dependency on an object: merges its cacheability metadata.
   *
   * For instance, when a response depends on some configuration, an entity, or
   * an access result, we must make sure their cacheability metadata is present
   * on the response. This method makes doing that simple.
   *
   * @param \Drupal\Core\Cache\CacheableDependencyInterface|mixed $dependency
   *   The dependency. If the object implements CacheableDependencyInterface,
   *   then its cacheability metadata will be used. Otherwise, the passed in
   *   object must be assumed to be uncacheable, so max-age 0 is set.
   *
   * @return $this
   */
  public function addCacheableDependency($dependency) {

    $this->cacheMetadata = $this->getCacheableMetadata()->merge(CacheableMetadata::createFromObject($dependency));

    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * Override the base classes response, to create a cacheable response.
   */
  public function createResponse(): Response {
    $response = new CacheableJsonResponse($this->return_data);

    // Attach cache metadata if available.
    if ($cache_metadata = $this->getCacheableMetadata()) {
      $response->addCacheableDependency($cache_metadata);
    }

    // Alter it.
    $this->responseAlter($response);

    return $response;
  }

}
