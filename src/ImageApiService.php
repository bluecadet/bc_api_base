<?php

namespace Drupal\bc_api_base;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\crop\Entity\Crop;

/**
 * Provide methods to expose image based data for an API.
 */
class ImageApiService extends AssetApiServiceBase {

  /**
   * Image Factory.
   *
   * @var \Drupal\Core\Image\ImageFactory
   */
  protected $imageFactory;

  /**
   * Config Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The focal_point manager service.
   *
   * NULL if the optional focal_point module isn't installed.
   *
   * @var object|null
   */
  protected $focalPointManager;

  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(FileUrlGeneratorInterface $file_url_generator, ImageFactory $image_factory, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($file_url_generator);

    $this->imageFactory = $image_factory;
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;

    // focal_point is an optional integration -- look the service up at
    // runtime rather than injecting it, since sites without the focal_point
    // module installed have no such service to inject.
    // @phpstan-ignore-next-line
    $this->focalPointManager = (\Drupal::hasService('focal_point.manager')) ? \Drupal::service('focal_point.manager') : NULL; // phpcs:ignore DrupalPractice.Objects.GlobalDrupal.GlobalDrupal
  }

  /**
   * Get ALL data for an image.
   */
  public function getImageData($file, $image_styles = []) {
    // $this->imageFactory->get($file->getFileUri());
    $image_file = $this->imageFactory->get($file->getFileUri());

    if (is_null($image_file)) {
      $data = NULL;
    }
    else {
      $anchor = [];
      // Both focal_point and crop are optional -- only look up a crop if
      // both the service and the class are actually available.
      if ($this->focalPointManager && class_exists(Crop::class)) {
        $crop_type = $this->configFactory->get('focal_point.settings')->get('crop_type');
        $crop = Crop::findCrop($file->getFileUri(), $crop_type);
        if ($crop) {
          // @phpstan-ignore-next-line focal_point is an optional integration;
          // its class isn't resolvable unless the module is installed.
          $anchor = $this->focalPointManager->absoluteToRelative($crop->x->value, $crop->y->value, $image_file->getWidth(), $image_file->getHeight());
        }
      }
      $uri = $file->getFileUri();
      $url = $this->fileUrlGenerator->generateAbsoluteString($uri);
      $data = [
        'uri' => $uri,
        'url' => $url,
        'relativePath' => $this->getRelativePath($url),
        'origSize' => [
          'width' => $image_file->getWidth(),
          'height' => $image_file->getHeight(),
        ],
        'focalPoint' => ($anchor) ?: [],
      ];

      foreach ($image_styles as $style_name) {
        $style = $this->entityTypeManager->getStorage('image_style')->load($style_name);

        // @todo check style exists
        $url = $style->buildUrl($file->getFileUri());

        // Remove an h query param.
        if ($this->configFactory->get('image.settings')->get('suppress_itok_output') && $this->configFactory->get('image.settings')->get('allow_insecure_derivatives')) {
          $parsed_url = parse_url($url);
          if (isset($parsed_url['query'])) {
            $qp_vals = explode("&", $parsed_url['query']);

            $url_query_params = [];
            foreach ($qp_vals as $values) {
              $ex_vals = explode("=", $values);
              $url_query_params[$ex_vals[0]] = $ex_vals[1];
            }
            if (isset($url_query_params['h'])) {
              unset($url_query_params['h']);
            }
            $parsed_url['query'] = http_build_query($url_query_params);

            $url = $this->buildUrl($parsed_url);
          }
        }

        $data[$style_name] = [
          'uri' => $style->buildUri($file->getFileUri()),
          'url' => $url,
          'relativePath' => $this->getRelativePath($url),
        ];
      }
    }

    return $data;
  }

}
