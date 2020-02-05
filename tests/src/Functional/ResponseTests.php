<?php

namespace Drupal\Tests\bc_api_base\Functional;

// phpcs:disable
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the api.
 *
 * @group bc_api
 * @group bc_api_base
 */
class ResponseTests extends BrowserTestBase {

  /**
   * The key auth service.
   *
   * @var \Drupal\key_auth\KeyAuthInterface
   */
  protected $keyAuth;

  protected $defaultTheme = 'classy';

  protected $dumpHeaders = TRUE;

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = [
    'node',
    'field',
    'text',
    'options',
    'menu_ui',
    'key_auth',
    'bc_api_base',
    'bc_api_example',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->keyAuth = $this->container->get('key_auth');
  }

  /**
   * Test that we are working.
   */
  public function testHttpResponses() {

    // Check that we get 404 w/json response.
    // Check that we get 403 w/json response.
    // Check that we get...

    $this->drupalGet('api');
    $this->assertResponse(404);

    $this->drupalGet('api/pirates');
    $this->assertResponse(403);

    // Create API user.
    $user2 = $this->drupalCreateUser(['use key authentication', 'use api']);

    // Log in.
    $this->drupalLogin($user2);

    // Set a key.
    $user2->set('api_key', $this->keyAuth->generateKey())->save();

    // Call with Query param.
    $this->drupalGet('api/pirates', [
      'query' => [
        'api-key' => $user2->api_key->value,
      ],
    ]);
    $this->assertResponse(200);

    // Call with Header param
    $this->drupalGet('api/pirates', [], ['api-key' => $user2->api_key->value]);
    $this->assertResponse(200);
  }

}
