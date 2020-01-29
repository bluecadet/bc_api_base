<?php

namespace Drupal\bc_api_logger\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * API Controller Messager.
 *
 * @ApiDoc(
 *   params = {
 *     @ApiParam(
 *       name = "message",
 *       type = "string",
 *       description = "The message to log",
 *       required = TRUE,
 *     ),
 *     @ApiParam(
 *       name = "level",
 *       type = "bool",
 *       description = "Filter on on node status.",
 *       default = "7",
 *       required = TRUE,
 *       range = {0, 7},
 *     ),
 *     @ApiParam(
 *       name = "channel",
 *       type = "bool",
 *       description = "The channel to log this to, but should be set to anything bc_api_**",
 *       default = "bc_api_external",
 *     ),
 *   }
 * )
 */
class ApiLoggerMessage extends ControllerBase {

  /**
   * Current Route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRoute = NULL;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Class constructor.
   */
  public function __construct(CurrentRouteMatch $current_route, LoggerChannelFactoryInterface $factory) {
    $this->currentRoute = $current_route;
    $this->loggerFactory = $factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('current_route_match'),
      $container->get('logger.factory')
    );
  }

  /**
   * Undocumented function.
   */
  public function setMessage(Request $request) {

    // Check Log Level.
    $level = $request->request->get('level');
    if (is_null($level)) {
      $err_message = 'Level not set';
      throw new HttpException(400, $err_message);
    }
    if ($level < 0 || $level > 7) {
      $err_message = 'Level not set correctly. Must be 0-7.';
      throw new HttpException(400, $err_message);
    }

    // Check that we have a message.
    $log_message = $request->request->get('message');
    if (is_null($log_message)) {
      $err_message = 'Message not set';
      throw new HttpException(400, $err_message);
    }

    // Check Channel.
    $channel = $request->request->get('channel');
    if (is_null($channel)) {
      $channel = 'bc_api_external';
    }

    // Level: 0-7.
    $this->loggerFactory->get($channel)->log((int) $level, $log_message, ['request' => $request]);

    return new JsonResponse([
      'status' => 200,
      'msg' => 'Message logged',
    ]);
  }

}
