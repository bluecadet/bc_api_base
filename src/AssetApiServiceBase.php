<?php

namespace Drupal\bc_api_base;

use Drupal\Core\File\FileUrlGenerator;

/**
 * Provide methods to expose image based data for an API.
 */
class AssetApiServiceBase {

  /**
   * File url generator object.
   *
   * @var \Drupal\Core\File\FileUrlGenerator
   */
  protected $fileUrlGenerator;

  /**
   * {@inheritdoc}
   */
  public function __construct(FileUrlGenerator $file_url_generator) {
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * Get basic information from a file.
   */
  public function getFileData($file) {
    $data = [
      'uri' => $file->getFileUri(),
      'url' => $file->url(),
      'relativePath' => $this->getRelativePath($file->url()),
    ];

    return $data;
  }

  /**
   * Get relative path of an image file.
   */
  public function getRelativePath(string $path) {
    $replaced_relative_path = '';
    if ($path !== '') {
      $transformed = $this->fileUrlGenerator->transformRelative($file_url);
      $relative_path = urldecode($transformed);
      // TODO: replace with actual public files path.
      $replaced_relative_path = str_replace('/sites/default/files/', '', $relative_path);
    }
    return $replaced_relative_path;
  }

  /**
   * Build url string from parts.
   *
   * @param array $parts
   *   Should be an array in the form returned by php's parse_url().
   *   https://www.php.net/manual/en/function.parse-url.php.
   *
   * @return string
   *   Full url string.
   */
  protected function buildUrl(array $parts) {
    $scheme = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';

    $host = ($parts['host'] ?? '');
    $port = isset($parts['port']) ? (':' . $parts['port']) : '';

    $user = ($parts['user'] ?? '');

    $pass = isset($parts['pass']) ? (':' . $parts['pass']) : '';
    $pass = ($user || $pass) ? "$pass@" : '';

    $path = ($parts['path'] ?? '');
    $query = (isset($parts['query']) && !empty($parts['query'])) ? ('?' . $parts['query']) : '';
    $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';

    return implode('', [
      $scheme,
      $user,
      $pass,
      $host,
      $port,
      $path,
      $query,
      $fragment,
    ]);
  }

}
