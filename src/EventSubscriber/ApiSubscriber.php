<?php

namespace Drupal\bc_api_base\EventSubscriber;

use Drupal\Core\EventSubscriber\HttpExceptionSubscriberBase;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\ParamConverter\ParamNotConvertedException;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\State\State;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Redirect 403 to User Login event subscriber.
 */
class ApiSubscriber extends HttpExceptionSubscriberBase {

  /**
   * Url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Drupal State obj.
   *
   * @var Drupal\Core\State\State
   */
  private $drupalState = [];

  /**
   * Drupal Logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs a new R4032LoginSubscriber.
   */
  public function __construct(UrlGeneratorInterface $url_generator, State $drupal_state, LoggerChannelFactoryInterface $logger) {
    $this->urlGenerator = $url_generator;
    $this->drupalState = $drupal_state;
    $this->loggerFactory = $logger;
  }

  /**
   * {@inheritdoc}
   */
  protected static function getPriority() {
    return 250;
  }

  /**
   * {@inheritdoc}
   */
  protected function getHandledFormats() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function onException(ExceptionEvent $event) {
    // Grab the exception.
    $exception = $event->getException();

    // Make the exception available for example when rendering a block.
    $request = $event->getRequest();
    $request->attributes->set('exception', $exception);

    $handled_formats = $this->getHandledFormats();

    $format = $request->query->get(MainContentViewSubscriber::WRAPPER_FORMAT, $request->getRequestFormat());

    if ($exception instanceof HttpExceptionInterface && empty($handled_formats) || in_array($format, $handled_formats)) {
      $method = 'on' . $exception->getStatusCode();
      // Keep just the leading number of the status code to produce either a
      // on400 or a 500 method callback.
      $method_fallback = 'on' . substr($exception->getStatusCode(), 0, 1) . 'xx';
      // We want to allow the method to be called and still not set a response
      // if it has additional filtering logic to determine when it will apply.
      // It is therefore the method's responsibility to set the response on the
      // event if appropriate.
      if (method_exists($this, $method)) {
        $this->$method($event);
      }
      elseif (method_exists($this, $method_fallback)) {
        $this->$method_fallback($event);
      }
    }

    if ($exception instanceof ParamNotConvertedException) {
      $request = $event->getRequest();

      if (strpos($request->getRequestUri(), "/api/") === 0 || $request->getRequestUri() == "/api") {
        $data = [
          'status' => 404,
          'error_msg' => 'Not Found',
          'full_msg' => $exception->getMessage(),
        ];
        $response = new JsonResponse($data);
        $event->setResponse($response);

        // Log this call.
        $this->loggerFactory->get('bc_api')->error("Bad Api call. ", ["request" => $request, "exception" => $exception]);
      }
    }

    if ($exception instanceof MethodNotAllowedHttpException) {
      $request = $event->getRequest();

      if (strpos($request->getRequestUri(), "/api/") === 0 || $request->getRequestUri() == "/api") {
        $data = [
          'status' => 404,
          'error_msg' => 'Not Found',
          'full_msg' => $exception->getMessage(),
        ];
        $response = new JsonResponse($data);
        $event->setResponse($response);

        // Log this call.
        $this->loggerFactory->get('bc_api')->error("Bad Api call. ", ["request" => $request, "exception" => $exception]);
      }
    }
  }

  /**
   * Redirects on 400 Bad Request kernel exceptions.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The Event to process.
   */
  public function on400(GetResponseEvent $event) {

    $request = $event->getRequest();
    $exception = $event->getException();

    if (strpos($request->getRequestUri(), "/api/") === 0 || $request->getRequestUri() == "/api") {
      $data = [
        'status' => (int) $exception->getStatusCode(),
        'error_msg' => 'Bad Request',
        'full_msg' => $exception->getMessage(),
      ];
      $response = new JsonResponse($data);
      $event->setResponse($response);

      // Log this call.
      $this->loggerFactory->get('bc_api')->error("400: Bad Api call. " . $exception->getMessage(), ["request" => $request, "exception" => $exception]);
    }
  }

  /**
   * Redirects on 403 Access Denied kernel exceptions.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The Event to process.
   */
  public function on403(GetResponseEvent $event) {

    $request = $event->getRequest();
    $exception = $event->getException();

    if (strpos($request->getRequestUri(), "/api/") === 0 || $request->getRequestUri() == "/api") {

      $data = [
        'status' => (int) $exception->getStatusCode(),
        'error_msg' => 'Access Denied',
        // 'full_msg' => $exception->getMessage(), DEBUG ONLY
      ];
      $response = new JsonResponse($data);
      $event->setResponse($response);

      // Log this call.
      $this->loggerFactory->get('bc_api')->error("403: Bad Api call. " . $exception->getMessage(), ["request" => $request, "exception" => $exception]);
    }
  }

  /**
   * Redirects on 404 Not Found kernel exceptions.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The Event to process.
   */
  public function on404(GetResponseEvent $event) {

    $request = $event->getRequest();
    $exception = $event->getException();

    if (strpos($request->getRequestUri(), "/api/") === 0 || $request->getRequestUri() == "/api") {
      $data = [
        'status' => (int) $exception->getStatusCode(),
        'error_msg' => 'Not Found',
        'full_msg' => $exception->getMessage(),
      ];
      $response = new JsonResponse($data);
      $event->setResponse($response);

      // Log this call.
      $this->loggerFactory->get('bc_api')->error("403: Bad Api call. " . $exception->getMessage(), ["request" => $request, "exception" => $exception]);
    }

  }

}
