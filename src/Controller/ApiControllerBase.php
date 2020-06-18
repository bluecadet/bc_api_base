<?php

namespace Drupal\bc_api_base\Controller;

use Drupal\bc_api_base\Annotation\ApiBaseDoc;
use Drupal\bc_api_base\Annotation\ApiDoc;
use Drupal\bc_api_base\Annotation\ApiParam;
use Drupal\bc_api_base\ApiParameterValidation;
use Drupal\bc_api_base\AssetApiService;
use Drupal\bc_api_base\ValueTransformationService;
use Drupal\Component\Annotation\Doctrine\SimpleAnnotationReader;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * API Controller Base.
 *
 * This includes:
 * - platform flags
 * - Connection to Image service
 * That is all.
 *
 * @ApiBaseDoc(
 *   params = {
 *     @ApiParam(
 *       name = "platform",
 *       type = "string",
 *       description = "The platform flag.",
 *     ),
 *     @ApiParam(
 *       name = "page",
 *       type = "int",
 *       description = "Which page. Start at 0",
 *       default = "0",
 *       listOnly = TRUE,
 *     ),
 *     @ApiParam(
 *       name = "limit",
 *       type = "int",
 *       description = "How many records on a page.",
 *       default = "500",
 *       listOnly = TRUE,
 *     ),
 *     @ApiParam(
 *       name = "debug",
 *       type = "bool",
 *       description = "This is a description.",
 *       default = "FALSE",
 *     ),
 *   },
 * )
 */
class ApiControllerBase extends ControllerBase implements ApiControllerInterface {

  /**
   * The initial Request Object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request = 0;

  /**
   * Connection to the image service.
   *
   * @var \Drupal\bc_api_base\AssetApiService
   */
  protected $assetService;

  /**
   * Connection to the image service.
   *
   * @var \Drupal\bc_api_base\ValueTransformationService
   */
  protected $transformer;

  /**
   * Cache service to use to cache data related to this endpoint.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Requested or default platform.
   *
   * @var string
   */
  protected $platform = '';

  /**
   * Raw (Drupal) Data.
   *
   * @var array
   */
  protected $rawData = [];

  /**
   * Processed Data.
   *
   * @var array
   */
  protected $data = [];

  /**
   * Processed CacheTags.
   *
   * @var array
   */
  protected $cacheTags = [];

  /**
   * QueryParams.
   *
   * @var array
   */
  protected $params = [];

  /**
   * Params not exposed in the url.
   *
   * These are static for this endpoint or derived through code. Not alterable
   * through the url.
   *
   * @var array
   */
  protected $privateParams = [];

  /**
   * Page of results we are on.
   *
   * @var int
   */
  protected $page = 0;

  /**
   * Limit the number of results.
   *
   * We set this high because we don't normally page results.
   *
   * @var int
   */
  protected $limit = 500;

  /**
   * Total number of possible results.
   *
   * @var int
   */
  protected $resultTotal = 0;

  /**
   * Limit the number of results.
   *
   * @var int
   */
  protected $prev = "";

  /**
   * Limit the number of results.
   *
   * @var int
   */
  protected $next = "";

  /**
   * Specific resource request.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $resource = NULL;

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
   * Entity Type Manager.
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal State obj.
   *
   * @var Drupal\Core\State\State
   */
  private $drupalstate = [];

  /**
   * Drupal State obj.
   *
   * @var Drupal\bc_api_base\ApiParameterValidation
   */
  private $queryValidation;

  /**
   * Class constructor.
   */
  public function __construct(
    AssetApiService $assetService,
    ValueTransformationService $transformer,
    ApiParameterValidation $query_validation,
    CacheBackendInterface $cache,
    CurrentRouteMatch $current_route,
    LoggerChannelFactoryInterface $factory,
    EntityTypeManagerInterface $entityTypeManager,
    $drupal_state) {

    $this->assetService = $assetService;
    $this->initCacheTags();
    $this->transformer = $transformer;
    $this->queryValidation = $query_validation;
    $this->cache = $cache;
    $this->currentRoute = $current_route;
    $this->loggerFactory = $factory;
    $this->entityTypeManager = $entityTypeManager;
    $this->drupalState = $drupal_state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('bc_api_base.asset'),
      $container->get('bc_api_base.valueTransformer'),
      $container->get('bc_api_base.param_validation'),
      $container->get('cache.bc_api_base'),
      $container->get('current_route_match'),
      $container->get('logger.factory'),
      $container->get('entity_type.manager'),
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function initCacheTags() {}

  /**
   * {@inheritdoc}
   */
  public function getDefaultPlatform() {
    return "default";
  }

  /**
   * {@inheritdoc}
   */
  public function setPlatform() {
    // Check if there's a platform parameter.
    $platform = $this->request->query->get('platform');
    if ($platform == NULL) {
      $platform = $this->getDefaultPlatform();
    }

    $this->platform = $platform;
    $this->transformer->setPlatform($platform);
  }

  /**
   * {@inheritdoc}
   */
  public function setParams() {

    // Set values if we are asking for a specific resource.
    $parameters = $this->currentRoute->getParameters()->all();

    if (isset($parameters['nid'])) {
      $this->privateParams['nid'] = $parameters['nid']->id();
      $this->resource = $parameters['nid'];
    }
    elseif (isset($parameters['taxonomy_term'])) {
      $this->privateParams['tid'] = $parameters['taxonomy_term']->id();
      $this->resource = $parameters['taxonomy_term'];
    }

    // Add in debugging.
    $this->privateParams['debug'] = filter_var($this->request->get('debug'), FILTER_VALIDATE_BOOLEAN);
  }

  /**
   * {@inheritdoc}
   */
  public function autoParams() {

    // @TODO: Why aren't these autoloaded???
    new ApiDoc([]);
    new ApiBaseDoc([]);
    new ApiParam([]);

    $reader = new SimpleAnnotationReader();
    $reader->addNamespace('Drupal\bc_api_base\Annotation');

    // Called class params.
    $class = get_class($this);
    $reflectionClass = new \ReflectionClass($class);
    $annotations = $reader->getClassAnnotations($reflectionClass);

    list($params, $errors) = $this->queryValidation->validateQueryParams($annotations, $this->request->query);
    $this->params = array_merge($this->params, $params);

    if (!empty($errors)) {

      $response_msg = array_reduce($errors, function ($msg, $errors) {
        if (!empty($msg)) {
          $msg .= " ";
        }
        $msg .= $errors['error_msg'];
        return $msg;
      });

      $bad_response = new BadRequestHttpException($response_msg);

      throw $bad_response;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheId() {
    $cid = get_class($this);

    if (!empty($this->params)) {
      foreach ($this->params as $key => $param) {
        $cid .= ":" . $key . "=" . $param;
      }
    }

    $cid .= ":page-" . $this->page;
    $cid .= ":limit-" . $this->limit;

    if (!empty($this->privateParams)) {
      foreach ($this->privateParams as $key => $param) {
        $cid .= ":" . $key . "=" . $param;
      }
    }

    return $cid;
  }

  /**
   * {@inheritdoc}
   */
  public function getApiCacheTime($id = 'list') {
    $settings = $this->drupalState->get('bc_api_base.cache.settings', []);

    if (isset($settings['classes']["\\" . get_class($this)]['routes'][$id])) {
      return ($settings['classes']["\\" . get_class($this)]['routes'][$id]);
    }
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  final public function getResource(Request $request) {
    $this->request = $request;

    // Check if there's a platform parameter.
    $this->setPlatform($request);

    $this->autoParams();
    $this->setParams();

    $cid = $this->getCacheId();

    $cache_time = $this->getApiCacheTime('detail');

    $this->return_data = [
      'status' => 200,
      'data' => [],
      'resultTotal' => 0,
    ];

    // Log this call.
    $this->loggerFactory->get('bc_api')->info("Endpoint Called", ["request" => $request]);

    if ($cache = $this->cache->get($cid)) {
      $this->return_data = $cache->data;
      $this->return_data['cacheHit'] = TRUE;
    }
    else {

      $this->getResourceQueryResult();
      $this->buildAllResourceData();
      $this->buildLinks();

      $this->return_data['data'] = $this->data;
      $this->return_data['resultTotal'] = $this->resultTotal;

      // Alter data some more.
      $this->responseDataAlter();

      $this->cache->set($cid, $this->return_data, (time() + $cache_time), $this->cacheTags);

      $this->return_data['cacheHit'] = FALSE;
    }

    if ($this->privateParams['debug'] && function_exists("ksm")) {
      ksm($this->return_data);
      return [];
    }

    $response = new JsonResponse($this->return_data);
    // Alter it.
    $this->responseAlter($response);

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  final public function getResourceList(Request $request) {
    $this->request = $request;

    // Set Page.
    $this->page = ($request->query->get('page')) ? $request->query->get('page') : $this->page;

    // Set Limit.
    $this->limit = ($request->query->get('limit')) ? $request->query->get('limit') : $this->limit;

    // Check if there's a platform parameter.
    $this->setPlatform($request);

    $this->autoParams();
    $this->setParams();

    $cid = $this->getCacheId();

    $cache_time = $this->getApiCacheTime('list');

    $this->return_data = [
      'status' => 200,
      'data' => [],
      'resultTotal' => 0,
      'pageCount' => 0,
      'prev' => '',
      'next' => '',
    ];

    // Log this call.
    $this->loggerFactory->get('bc_api')->info("Endpoint Called", ["request" => $request]);

    if ($cache = $this->cache->get($cid)) {
      $this->return_data = $cache->data;
      $this->return_data['cacheHit'] = TRUE;
    }
    else {

      $this->getResourceListQueryResult();
      $this->buildAllResourceData();
      $this->buildLinks();

      $this->return_data['data'] = $this->data;
      $this->return_data['resultTotal'] = $this->resultTotal;
      $this->return_data['pageCount'] = $this->page;
      $this->return_data['prev'] = $this->prev;
      $this->return_data['next'] = $this->next;

      // Alter data some more.
      $this->responseDataAlter();

      $this->cache->set($cid, $this->return_data, (time() + $cache_time), $this->cacheTags);
      $this->return_data['cacheHit'] = FALSE;
    }

    if ($this->privateParams['debug'] && function_exists("ksm")) {
      ksm($this->return_data);
      return [];
    }

    $response = new JsonResponse($this->return_data);
    // Alter it.
    $this->responseAlter($response);

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceQueryResult() {
    $this->rawData = [];
    $this->resultTotal = 0;

    if (isset($this->resource) && !empty($this->resource)) {
      $this->rawData = [$this->resource];
      $this->resultTotal = 1;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceListQueryResult() {
    $query = $this->entityTypeManager->getStorage('node')->getQuery();

    $count_query = clone $query;

    $query->range(($this->page * $this->limit), $this->limit);
    $entity_ids = $query->execute();

    // Must set total result count so we can properly page.
    $this->resultTotal = (int) $count_query->count()->execute();

    // Process Items.
    $node_storage = $this->entityTypeManager->getStorage('node');
    $this->rawData = $node_storage->loadMultiple($entity_ids);
  }

  /**
   * {@inheritdoc}
   */
  public function buildAllResourceData() {
    foreach ($this->rawData as $node) {
      $this->data[] = $node->toArray();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildLinks() {
    $this->prev = "";
    $this->next = "";
  }

  /**
   * {@inheritdoc}
   */
  public function responseDataAlter() {}

  /**
   * {@inheritdoc}
   */
  public function responseAlter(Response $response) {}

}
