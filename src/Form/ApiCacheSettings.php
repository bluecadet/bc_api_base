<?php

namespace Drupal\bc_api_base\Form;

use Drupal\bc_aicc\ImportBatch;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteProvider;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * API Cache length settings Form.
 */
class ApiCacheSettings extends FormBase {

  /**
   * Database Connection.
   *
   * @var Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * Drupal State obj.
   *
   * @var Drupal\Core\State\State
   */
  private $drupalState = [];

  /**
   * Entity Query.
   *
   * @var Drupal\Core\Routing\RouteProvider
   */
  protected $routeProvider;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Class constructor.
   */
  public function __construct(Connection $database, $drupal_state, RouteProvider $route_provider, MessengerInterface $messenger) {
    $this->database = $database;
    $this->drupalState = $drupal_state;
    $this->routeProvider = $route_provider;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('database'),
      $container->get('state'),
      $container->get('router.route_provider'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bc_api_cache_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->drupalState->get('bc_api_base.cache.settings', []);

    $form['#tree'] = TRUE;

    $endpoints = $this->getEndpointClasses();

    $form['message'] = [
      '#markup' => 'Common times in seconds:',
      'list' => [
        '#theme' => 'item_list',
        '#items' => [
          '30 mins: 1800',
          '1 hour: 3600',
          '6 hours: 21600',
          '1 day: 86400',
          '3 days: 259200',
        ],
      ],
    ];

    $form['classes'] = [];

    foreach ($endpoints as $class => $class_data) {
      $form['classes'][$class]['routes'] = [
        '#type' => 'fieldset',
        '#title' => $class,
      ];

      foreach ($class_data as $i => $route) {
        $form['classes'][$class]['routes'][$i] = [
          '#type' => 'number',
          '#title' => $route . ' Cache Time',
          '#default_value' => isset($settings['classes'][$class]['routes'][$i]) ? $settings['classes'][$class]['routes'][$i] : 0,
          '#description' => $this->t('In number of seconds'),
          '#min' => 0,
        ];
      }
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $to_save = [];
    $to_save['classes'] = $values['classes'];

    $this->drupalState->set('bc_api_base.cache.settings', $to_save);

    // drupal_set_message($this->t('You have saved your settings.'));
    $this->messenger()->addMessage($this->t('You have saved your settings.'));
  }

  /**
   * Get class and endpoint data.
   */
  protected function getEndpointClasses() {
    $classes = [];

    $query = $this->database->select('router', 'r');
    $query->fields('r');
    $query->condition("r.path", "/api%%", "LIKE");
    $results = $query->execute()->fetchAll();

    foreach ($results as $result) {
      // Do not use the message route.
      if ($result->name != "bc_api_logger.message") {
        $route = $this->routeProvider->getRouteByName($result->name);
        $deaults = $route->getDefaults();
        $controller_class_ex = explode(":", $deaults['_controller']);
        $class_name = $controller_class_ex[0];

        if (!isset($classes[$class_name])) {
          $classes[$class_name] = [];
        }

        if ($controller_class_ex[1] == "getResourceList") {
          $classes[$class_name]['list'] = $result->name;
        }
        else {
          $classes[$class_name]['detail'] = $result->name;
        }
      }
    }

    return $classes;
  }

}
