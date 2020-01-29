
- logs to our own DB table.
- can force keep certain records.
- Settings to remove old records.

POST to api/message

{
  'message' => '',
  'level' => 1,
  'channel' => 'bc_api_external', // Default: 'bc_api_external', but should be set to anything 'bc_api_**'.
}

Levels:
use Drupal\Core\Logger\RfcLogLevel;
RfcLogLevel::EMERGENCY  = 0;
RfcLogLevel::ALERT      = 1;
RfcLogLevel::CRITICAL   = 2;
RfcLogLevel::ERROR      = 3;
RfcLogLevel::WARNING    = 4;
RfcLogLevel::NOTICE     = 5;
RfcLogLevel::INFO       = 6;
RfcLogLevel::DEBUG      = 7;
