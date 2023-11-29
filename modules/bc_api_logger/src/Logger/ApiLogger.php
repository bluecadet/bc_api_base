<?php

namespace Drupal\bc_api_logger\Logger;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\DatabaseException;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\Logger\RfcLoggerTrait;
use Psr\Log\LoggerInterface;

/**
 * Logger.
 */
class ApiLogger implements LoggerInterface {
  use RfcLoggerTrait;
  use DependencySerializationTrait;

  /**
   * The dedicated database connection target to use for log entries.
   */
  const DEDICATED_DBLOG_CONNECTION_TARGET = 'dedicated_api_log';

  /**
   * The database connection object.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The message's placeholders parser.
   *
   * @var \Drupal\Core\Logger\LogMessageParserInterface
   */
  protected $parser;

  /**
   * List of channels to log.
   *
   * @var array
   */
  protected $capturedChannels = [
    'bc_api',
    'bc_api_external',
  ];

  /**
   * Constructs a DbLog object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection object.
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   */
  public function __construct(Connection $connection, LogMessageParserInterface $parser) {
    $this->connection = $connection;
    $this->parser = $parser;
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, string|\Stringable $message, array $context = []): void {

    if (in_array($context['channel'], $this->capturedChannels) || preg_match('/^bc_api[_a-z0-9]*/', $context['channel']) != 0) {
      // Remove any backtraces since they may contain an unserializable vars.
      unset($context['backtrace']);

      // Add more API specific context.
      // Path.
      $context['path'] = isset($context['request']) ? $context['request']->getPathInfo() : "";

      // Method.
      $context['method'] = isset($context['request']) ? $context['request']->getMethod() : "";

      // Query params.
      $context['query_string'] = isset($context['request']) ? $context['request']->getQueryString() : "";

      // Post params.
      $context['request_params'] = isset($context['request']) ? $context['request']->request->all() : "";

      // Convert PSR3-style messages to
      // \Drupal\Component\Render\FormattableMarkup style, so they can be
      // translated too in runtime.
      $message_placeholders = $this->parser->parseMessagePlaceholders($message, $context);

      try {
        $this->connection
          ->insert('api_log')
          ->fields([
            'uid' => $context['uid'],
            'keep' => 0,
            'type' => mb_substr($context['channel'], 0, 64),
            'message' => $message,
            'variables' => serialize($message_placeholders),
            'severity' => $level,
            'path' => $context['path'],
            'method' => $context['method'],
            'query_string' => $context['query_string'],
            'request_params' => serialize($context['request_params']),
            'link' => $context['link'],
            'location' => $context['request_uri'],
            'referer' => $context['referer'],
            'hostname' => mb_substr($context['ip'], 0, 128),
            'timestamp' => $context['timestamp'],
          ])
          ->execute();
      }
      catch (\Exception $e) {
        // When running Drupal on MySQL or MariaDB you can run into several
        // errors
        // that corrupt the database connection. Some examples for these kind of
        // errors on the database layer are "1100 - Table 'xyz' was not locked
        // with LOCK TABLES" and "1153 - Got a packet bigger than
        // 'max_allowed_packet' bytes". If such an error happens, the MySQL
        // server
        // invalidates the connection and answers all further requests in this
        // connection with "2006 - MySQL server had gone away". In that case the
        // insert statement above results in a database exception. To ensure
        // that
        // the causal error is written to the log we try once to open a
        // dedicated
        // connection and write again.
        if (
          // Only handle database related exceptions.
          ($e instanceof DatabaseException || $e instanceof \PDOException) &&
          // Avoid an endless loop of re-write attempts.
          $this->connection->getTarget() != self::DEDICATED_DBLOG_CONNECTION_TARGET
        ) {
          // Open a dedicated connection for logging.
          $key = $this->connection->getKey();
          $info = Database::getConnectionInfo($key);
          Database::addConnectionInfo($key, self::DEDICATED_DBLOG_CONNECTION_TARGET, $info['default']);
          $this->connection = Database::getConnection(self::DEDICATED_DBLOG_CONNECTION_TARGET, $key);
          // Now try once to log the error again.
          $this->log($level, $message, $context);
        }
        else {
          throw $e;
        }
      }
    }
  }

}
