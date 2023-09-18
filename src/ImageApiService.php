<?php

namespace Drupal\bc_api_base;

use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileUrlGenerator;
use Drupal\Core\Image\ImageFactory;
use Drupal\crop\Entity\Crop;

/**
 * Provide methods to expose image based data for an API.
 */
class ImageApiService extends AssetApiServiceBase {

  /**
   * Image Factory.
   *
   * @var Drupal\Core\Image\ImageFactory
   */
  protected $imageFactory;

  /**
   * Config Factory.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Image Factory.
   *
   * @var Drupal\focal_point\FocalPointManager|null
   */
  protected $focalPointManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(FileUrlGenerator $file_url_generator, ImageFactory $image_factory, ConfigFactoryInterface $config_factory) {
    parent::__construct($file_url_generator);

    $this->imageFactory = $image_factory;
    $this->configFactory = $config_factory;

    // Load the focal point manager if it exists.
    // phpcs:ignore
    $this->focalPointManager = (\Drupal::hasService('focal_point.manager')) ? \Drupal::service('focal_point.manager') : NULL;
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
      $crop_type = $this->configFactory->get('focal_point.settings')->get('crop_type');
      $crop = Crop::findCrop($file->getFileUri(), $crop_type);
      if ($crop) {
        $anchor = $this->focalPointManager->absoluteToRelative($crop->x->value, $crop->y->value, $image_file->getWidth(), $image_file->getHeight());
      }
      $data = [
        'uri' => $file->getFileUri(),
        'url' => $file->url(),
        'relativePath' => $this->getRelativePath($file->url()),
        'origSize' => [
          'width' => $image_file->getWidth(),
          'height' => $image_file->getHeight(),
        ],
        'focalPoint' => ($anchor) ?: [],
      ];

      foreach ($image_styles as $style_name) {
        $style = ImageStyle::load($style_name);

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
