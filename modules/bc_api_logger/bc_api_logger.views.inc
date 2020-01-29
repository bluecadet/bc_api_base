<?php

/**
 * @file
 * Provide views data for bc_api_logger.module.
 */

/**
 * Implements hook_views_data().
 */
function bc_api_logger_views_data() {
  $data = [];

  $data['api_log']['table']['group'] = t('API Log');
  $data['api_log']['table']['provider'] = 'bc_api_logger';

  $data['api_log']['table']['base'] = [
    'field' => 'id',
    'title' => t('API Log entries'),
    'help' => t('Contains a list of api log entries.'),
  ];

  $data['api_log']['id'] = [
    'title' => t('ID'),
    'help' => t('Unique api_log event ID.'),
    'field' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['uid'] = [
    'title' => t('UID'),
    'help' => t('The user ID of the user on which the log entry was written.'),
    'field' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'title' => t('User'),
      'help' => t('The user on which the log entry as written.'),
      'base' => 'users_field_data',
      'base field' => 'uid',
      'id' => 'standard',
    ],
  ];

  $data['api_log']['keep'] = [
    'title' => t('Keep'),
    'help' => t('Whether or not to always keep this record.'),
    'field' => [
      'id' => 'boolean',
    ],
    'filter' => [
      'id' => 'boolean',
    ],
    'argument' => [
      'id' => 'boolean',
    ],
  ];

  $data['api_log']['type'] = [
    'title' => t('Type'),
    'help' => t('The type of the log entry, for example "user" or "page not found".'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'dblog_types',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['message'] = [
    'title' => t('Message'),
    'help' => t('The actual message of the log entry.'),
    'field' => [
      'id' => 'dblog_message',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['variables'] = [
    'title' => t('Variables'),
    'help' => t('The variables of the log entry in a serialized format.'),
    'field' => [
      'id' => 'serialized',
      'click sortable' => FALSE,
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['severity'] = [
    'title' => t('Severity level'),
    'help' => t('The severity level of the event; ranges from 0 (Emergency) to 7 (Debug).'),
    'field' => [
      'id' => 'machine_name',
      'options callback' => 'Drupal\dblog\Controller\DbLogController::getLogLevelClassMap',
    ],
    'filter' => [
      'id' => 'in_operator',
      'options callback' => 'Drupal\Core\Logger\RfcLogLevel::getLevels',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['path'] = [
    'title' => t('Path'),
    'help' => t('API path'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['method'] = [
    'title' => t('Method'),
    'help' => t('Method used for the call.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['query_string'] = [
    'title' => t('Query String'),
    'help' => t('The called query string.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['request_params'] = [
    'title' => t('Request Params'),
    'help' => t('The called request params.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['link'] = [
    'title' => t('Operations'),
    'help' => t('Operation links for the event.'),
    'field' => [
      'id' => 'dblog_operations',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['location'] = [
    'title' => t('Location'),
    'help' => t('URL of the origin of the event.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['referer'] = [
    'title' => t('Referer'),
    'help' => t('URL of the previous page.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['hostname'] = [
    'title' => t('Hostname'),
    'help' => t('Hostname of the user who triggered the event.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['api_log']['timestamp'] = [
    'title' => t('Timestamp'),
    'help' => t('Date when the event occurred.'),
    'field' => [
      'id' => 'date',
    ],
    'argument' => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
    'sort' => [
      'id' => 'date',
    ],
  ];

  return $data;
}
