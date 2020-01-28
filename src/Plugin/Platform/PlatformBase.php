<?php

namespace Drupal\bc_api_base\Plugin\Platform;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\bc_api_base\Plugin\PlatformInterface;
use Drupal\bc_api_base\Plugin\PlatformTransformInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base Class for simple Platform PLugins.
 */
class PlatformBase extends PluginBase implements PlatformInterface, PlatformTransformInterface, ContainerFactoryPluginInterface {
  /**
   * Entity Manager.
   *
   * @var Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * Cache storage configs in case we need them again.
   *
   * @var array
   */
  protected $fieldStorageCache = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $entity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function textFieldVal($entity, string $field) {
    $vals = $entity->get($field)->getValue();
    $data = [];

    foreach ($vals as $val) {
      // Process Text.
      if (empty($val)) {
        $data[] = NULL;
      }
      $data[] = $this->applyPlatformTransformations($val['value']);
    }

    return $this->processResult($entity, $field, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function numFieldVal($entity, string $field) {
    $vals = $entity->get($field)->getValue();
    $data = [];

    foreach ($vals as $val) {
      $data[] = (float) $val['value'];
    }

    return $this->processResult($entity, $field, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function boolFieldVal($entity, string $field) {
    $vals = $entity->get($field)->getValue();
    $data = [];

    foreach ($vals as $val) {
      $data[] = (bool) $val['value'];
    }

    return $this->processResult($entity, $field, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function simpleEntityRefFieldVal($entity, string $field) {
    $vals = $entity->get($field)->referencedEntities();
    $data = [];

    foreach ($vals as $entity) {
      $data[] = (int) $entity->id();
    }

    return $this->processResult($entity, $field, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function taxFieldVal($entity, string $field) {
    $vals = $entity->get($field)->referencedEntities();
    $data = [];

    foreach ($vals as $tax) {
      $data[] = (int) $tax->id();
    }

    return $this->processResult($entity, $field, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function taxFieldSubfieldVal($entity, string $field, string $subField) {
    $vals = $entity->get($field)->referencedEntities();
    $data = [];

    foreach ($vals as $tax) {

      if ($subField == 'id' || $subField == 'tid') {
        $data[] = (int) $tax->id();
      }
      elseif ($subField == 'label') {
        $data[] = $tax->label();
      }
      elseif ($tax->hasField($subField)) {
        $tax_value = $tax->get($subField)->getValue();
        if (is_array($tax_value)) {
          foreach ($tax_value as $value) {
            if (array_key_exists('value', $value)) {
              $data[] = $this->applyPlatformTransformations($value['value']);
            }
            else {
              $data[] = $this->recursiveDataPlatformTransformation($tax_value);
            }
          }
        }
        else {
          $data[] = $this->applyPlatformTransformations($tax_value);
        }
      }
      else {
        $data[] = NULL;
      }
    }

    return $this->processResult($entity, $field, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function dateFieldVal($entity, string $field) {

    $date_field_val = $entity->get($field)->getValue();

    if (isset($date_field_val) && !empty($date_field_val)) {
      $date_field_obj = new DrupalDateTime(current($date_field_val)['value'], new \DateTimeZone('UTC'));

      // phpcs:disable
      // @TODO: Why did we get rid of this??
      // Grab timezone config.
      // $config = \Drupal::config('system.date');
      // $config_data_default_timezone = $config->get('timezone.default');

      // Set default Timezone.
      // $date_field_obj->setTimezone(new \DateTimeZone($config_data_default_timezone));
      // phpcs:enable

      $val = $date_field_obj->format('Y-m-d\TH:i:s');
    }
    else {
      $val = NULL;
    }

    return $val;
  }

  /**
   * {@inheritdoc}
   */
  public function createdChangedFieldVals($entity) {

    // Grab timezone config.
    $config = \Drupal::config('system.date');
    $config_data_default_timezone = $config->get('timezone.default');

    $created_val = NULL;
    if (method_exists($entity, "getCreatedTime")) {
      $created = DateTimePlus::createFromTimestamp($entity->getCreatedTime(), $config_data_default_timezone);
      $created->setTimezone(new \DateTimeZone('UTC'));
      $created_val = $created->format('Y-m-d\TH:i:s\Z');
    }

    $changed = DateTimePlus::createFromTimestamp($entity->getChangedTime(), $config_data_default_timezone);

    // Set Timezone to UTC.
    $changed->setTimezone(new \DateTimeZone('UTC'));

    return [$created_val, $changed->format('Y-m-d\TH:i:s\Z')];
  }

  /**
   * {@inheritdoc}
   */
  public function serializedTextFieldVal($entity, string $field) {
    $vals = $entity->get($field)->getValue();
    $data = [];

    foreach ($vals as $val) {
      // Process Text.
      $unserializedText = unserialize($val['value']);

      // Check if unserialized data is FALSE, which breaks mobile parsing.
      $data[] = !$unserializedText ? NULL : $unserializedText;
    }

    return $this->processResult($entity, $field, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function applyPlatformTransformations(string $text) {

    $new_text = $text;
    if (isset($this->pluginDefinition['striphtml']) && $this->pluginDefinition['striphtml'] === 'cinder') {
      $new_text = strip_tags($text, CINDER_ALLOWED_TAGS);
    }
    if (isset($this->pluginDefinition['urldecode']) && $this->pluginDefinition['urldecode']) {
      $new_text = urldecode($new_text);
    }
    if (isset($this->pluginDefinition['unescapechars']) && $this->pluginDefinition['unescapechars']) {
      $new_text = html_entity_decode($new_text);
      $new_text = preg_replace("/\r\n/", "\n", $new_text);
      $new_text = preg_replace("/\x{2028}/u", "\n", $new_text);
    }
    if (isset($this->pluginDefinition['striphtml']) && $this->pluginDefinition['striphtml'] === 'all') {
      $new_text = preg_replace("/<br>|<br\/>/", "\n", $new_text);
      $new_text = strip_tags($new_text);
    }

    $new_text = trim($new_text);
    return $new_text;
  }

  /**
   * {@inheritdoc}
   */
  public function recursiveDataPlatformTransformation($obj) {

    if (is_object($obj)) {
      $new_object = new \stdClass();

      foreach ($obj as $property => $value) {
        if (is_object($value) || is_array($value)) {
          $new_object->{$property} = $this->recursiveDataPlatformTransformation($value);
        }
        else {
          $new_object->{$property} = $this->applyPlatformTransformations($value);
        }
      }
    }
    elseif (is_array($obj)) {
      $new_object = [];

      foreach ($obj as $property => $value) {
        if (is_object($value) || is_array($value)) {
          $new_object[$property] = $this->recursiveDataPlatformTransformation($value);
        }
        else {
          $new_object[$property] = $this->applyPlatformTransformations($value);
        }
      }
    }

    return $new_object;
  }

  /**
   * Process the result for Single or multivalue fields.
   */
  private function processResult($entity, $field_name, $data) {

    $storage_config_id = $entity->getEntityTypeId() . '.' . $field_name;

    if (!isset($this->fieldStorageCache[$storage_config_id])) {
      $this->fieldStorageCache[$storage_config_id] = $this->entityManager->getStorage('field_storage_config')->load($storage_config_id);
    }

    $cardinality = $this->fieldStorageCache[$storage_config_id]->getCardinality();

    if ($cardinality == 1 && count($data) == 1) {
      return current($data);
    }
    elseif ($cardinality == 1 && count($data) === 0) {
      switch ($this->fieldStorageCache[$storage_config_id]->getType()) {
        case "list_integer":
        case "integer":
        case "entity_reference":
          return NULL;

        case "string":
        default:
          return "";
      }
    }

    return $data;
  }

}
