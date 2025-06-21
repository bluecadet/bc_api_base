<?php

namespace Drupal\Tests\bc_api_base\Unit;

use Drupal\bc_api_base\CacheableJsonResponseTrait;
use Drupal\bc_api_base\Controller\ApiControllerBase;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unit Tests for CacheableJsonResponseTrait.
 *
 * @coversDefaultClass \Drupal\bc_api_base\CacheableJsonResponseTrait
 * @group bc_api_base
 */
class CacheableJsonResponseTraitTest extends UnitTestCase {

  /**
   * Provides a test double for the trait.
   */
  protected function getTestController(array $return_data = ['foo' => 'bar']) {
    return new class($return_data) extends ApiControllerBase {
      use CacheableJsonResponseTrait;

      public function __construct($return_data) {
        $this->return_data = $return_data;
      }

      public function setCacheTags(array $tags) {
        $this->getCacheableMetadata()->setCacheTags($tags);
      }

      /**
       * Public wrapper for testing the protected method.
       */
      public function getCacheableMetadataPublic() {
        return $this->getCacheableMetadata();
      }

      /**
       * Public wrapper for the protected cacheableJsonResponse method.
       */
      public function cacheableJsonResponsePublic(Response $response) {
        return $this->cacheableJsonResponse($response);
      }

    };
  }

  /**
   * Tests that getCacheableMetadata returns an instance of CacheableMetadata.
   */
  public function testGetCacheableMetadataReturnsObject() {
    $controller = $this->getTestController();
    $metadata = $controller->getCacheableMetadataPublic();
    $this->assertInstanceOf(CacheableMetadata::class, $metadata);
  }

  /**
   * Tests that addCacheableDependency merges cacheable metadata.
   */
  public function testSetAndGetCacheTags() {
    $controller = $this->getTestController();
    $tags = ['foo:1', 'bar:2'];
    $controller->setCacheTags($tags);
    $metadata = $controller->getCacheableMetadataPublic();
    $this->assertEquals($tags, $metadata->getCacheTags());
  }

  /**
   * Tests that cacheableJsonResponse returns a CacheableJsonResponse.
   */
  public function testCreateResponseReturnsCacheableJsonResponse() {
    $controller = $this->getTestController(['baz' => 'qux']);
    $controller->setCacheTags(['baz:1']);
    $response = $controller->createResponse();
    $this->assertInstanceOf(CacheableJsonResponse::class, $response);
    $this->assertEquals(json_encode(['baz' => 'qux']), $response->getContent());
    $this->assertEquals(200, $response->getStatusCode());
    // Check cache tags are present.
    $metadata = $response->getCacheableMetadata();
    $this->assertContains('baz:1', $metadata->getCacheTags());
  }

}
