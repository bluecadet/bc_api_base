<?php

namespace Drupal\bc_api_logger\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Configure logging settings for this site.
 *
 * @internal
 */
class ApiLoggerSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bc_api_logger_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['bc_api_logger.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('bc_api_logger.settings');

    $row_limits = [5, 100, 1000, 10000, 100000, 1000000];
    $form['row_limit'] = [
      '#type' => 'select',
      '#title' => $this->t('Database log messages to keep'),
      '#default_value' => $config->get('row_limit'),
      '#options' => [0 => $this->t('All')] + array_combine($row_limits, $row_limits),
      '#description' => $this->t('The maximum number of messages to keep in the database log. Requires a <a href=":cron">cron maintenance task</a>.', [':cron' => Url::fromRoute('system.status')->toString()]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('bc_api_logger.settings')
      ->set('row_limit', $form_state->getValue('row_limit'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
