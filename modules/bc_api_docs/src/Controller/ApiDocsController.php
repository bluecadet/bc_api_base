<?php

namespace Drupal\bc_api_docs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\bc_api_base\Annotation\ApiBaseDoc;
use Drupal\bc_api_base\Annotation\ApiDoc;
use Drupal\bc_api_base\Annotation\ApiParam;
use Drupal\Component\Annotation\Doctrine\SimpleAnnotationReader;
use Drupal\Core\Routing\RouteProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to build out API documentation.
 */
class ApiDocsController extends ControllerBase {

  /**
   * Entity Query.
   *
   * @var Drupal\Core\Routing\RouteProvider
   */
  protected $routeProvider;

  /**
   * {@inheritdoc}
   */
  public function __construct(RouteProvider $route_provider) {
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('router.route_provider')
    );
  }

  /**
   * Grab Routes and build page.
   */
  public function build(Request $request) {

    // @TODO: Why aren't these autoloaded???
    new ApiDoc([]);
    new ApiBaseDoc([]);
    new ApiParam([]);

    $query = db_select('router', 'r');
    $query->fields('r');
    $query->condition("r.path", "/api%%", "LIKE");
    $results = $query->execute()->fetchAll();

    $reader = new SimpleAnnotationReader();
    $reader->addNamespace('Drupal\bc_api_base\Annotation');

    $endpoints = [];

    // Get Base class Annotations.
    $reader = new SimpleAnnotationReader();
    $reader->addNamespace('Drupal\bc_api_base\Annotation');
    $class = "Drupal\bc_api_base\Controller\ApiControllerBase";
    $reflectionClass = new \ReflectionClass($class);
    $base_annotations = $reader->getClassAnnotations($reflectionClass);

    foreach ($results as $result) {
      $endpoint = [];

      $route = $this->routeProvider->getRouteByName($result->name);
      $deaults = $route->getDefaults();
      $controller_class_ex = explode(":", $deaults['_controller']);

      $class_name = $controller_class_ex[0];
      $reflectionClass = new \ReflectionClass($class_name);
      $annotations = $reader->getClassAnnotations($reflectionClass);

      if (is_subclass_of($class_name, "Drupal\bc_api_base\Controller\ApiControllerBase")) {
        $annotations = array_merge($base_annotations, $annotations);
      }

      $endpoints[] = [
        'route' => $route,
        'annotations' => $annotations,
        'is_subclass' => is_subclass_of($class_name, "Drupal\bc_api_base\Controller\ApiControllerBase"),
      ];
    }

    $build = [];
    foreach ($endpoints as $endpoint) {
      $build[] = [
        '#theme' => 'api_endpoint',
        '#endpoint_data' => $endpoint,
      ];
    }

    return $build;
  }

}
