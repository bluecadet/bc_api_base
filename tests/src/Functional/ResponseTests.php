<?php

namespace Drupal\Tests\bc_api_base\Functional;

// phpcs:disable
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use Drupal\key_auth\KeyAuth;

/**
 * Test the api.
 *
 * @group bc_api
 * @group bc_api_base
 * @group bc_api_base:functional
 */
class ResponseTests extends BrowserTestBase {

  /**
   * The key auth service.
   *
   * @var \Drupal\key_auth\KeyAuthInterface
   */
  protected $keyAuth;

  /**
   * The module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $keyAuthConfig;

  protected $defaultTheme = 'classy';

  protected $dumpHeaders = TRUE;

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = [
    'node',
    'taxonomy',
    'user',
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
    $this->keyAuthConfig = $this->config('key_auth.settings');
  }

  /**
   * Test that we are working.
   */
  public function testHttpResponses() {

    $this->keyAuthConfig->set('auto_generate_keys', TRUE);
    $this->keyAuthConfig->set('detection_methods', [
      KeyAuth::DETECTION_METHOD_HEADER,
      KeyAuth::DETECTION_METHOD_QUERY,
    ])->save();
    $this->keyAuthConfig->save();

    // Check that we get 404 w/json response.
    // Check that we get 403 w/json response.
    // Check that we get...

    $this->drupalGet('api');
    $this->assertResponse(404);

    $this->drupalGet('api/pirates');
    $this->assertResponse(403);

    // Create API user.
    $user = $this->drupalCreateUser(['use api', 'use key authentication']);

    // Log in.
    $this->drupalLogin($user);

    // Call with Query param.
    $this->drupalGet('api/pirates', [
      'query' => [
        'api-key' => $user->api_key->value,
      ],
    ]);
    $this->assertResponse(200);

    // Call with Header param
    $this->drupalGet('api/pirates', [], ['api-key' => $user->api_key->value]);
    $this->assertResponse(200);
  }

  /**
   * Test that we are working.
   */
  public function testResponseData() {

    // Create API user.
    $user = $this->drupalCreateUser(['use key authentication', 'use api']);

    // Log in.
    $this->drupalLogin($user);

    // Call with Query param.
    $data = $this->drupalGet('api/pirates', [
      'query' => [
        'api-key' => $user->api_key->value,
      ],
    ]);
    $this->assertResponse(200);

    $data = json_decode($data);

    // Call with Header param
    $this->drupalGet('api/pirates', [], ['api-key' => $user->api_key->value]);
    $this->assertResponse(200);

    module_load_include('inc', 'bc_api_example', 'bc_api_example.data');

    // Check we have a proper result count.
    $pirates = bc_api_example_get_pirates_data();
    $this->assertEqual($data->resultTotal, count($pirates), "Check count", "Pirates");

    // Check that we have cms_title key in response data.
    $this->assertTrue(isset($data->data[0]->cms_title), "Check cms_title", "Pirates");

  }

}
