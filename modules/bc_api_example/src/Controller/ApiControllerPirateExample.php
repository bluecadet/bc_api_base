<?php

namespace Drupal\bc_api_example\Controller;

use Drupal\bc_api_base\Controller\ApiControllerBase;

/**
 * Example API Controller Class.
 *
 * @ApiDoc(
 *   params = {
 *     @ApiParam(
 *       name = "status",
 *       type = "bool",
 *       description = "Filter on node status.",
 *       default = "TRUE",
 *     ),
 *     @ApiParam(
 *       name = "nat",
 *       type = "int",
 *       description = "Filter on nationality. This should use the taxonomy id of the nationality.",
 *     ),
 *   }
 * )
 */
class ApiControllerPirateExample extends ApiControllerBase {

  /**
   * {@inheritdoc}
   */
  public function getApiCacheTime($id = NULL) {
    // Parent method looks at settings form and takes into account list or
    // detail.
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheId() {
    $cid = "SOMETHING_UNIQUE";

    if (!empty($this->params)) {
      $cid .= ":" . implode(":", $this->params);
    }

    $cid .= ":page-" . $this->page;
    $cid .= ":limit-" . $this->limit;

    return $cid;
  }

  /**
   * {@inheritdoc}
   */
  public function initCacheTags() {
    $this->cacheTags = [
      'myAwesomeCoolCacheTag',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setParams() {
    // It would be good to always run this.
    parent::setParams();

    $this->privateParams['status'] = 1;

    // Here we also want to handle any bad param errors...
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultPlatform() {
    return "cinder";
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceQueryResult() {
    // Just having this here as an example.
    // Most times no need to override this method.
    parent::getResourceQueryResult();
  }

  /**
   * {@inheritdoc}
   */
  public function getResourceListQueryResult() {
    // This method should be overridden for any endpoint.
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->condition('status', $this->privateParams['status']);
    $query->condition('type', 'pirate');

    $count_query = clone $query;

    $query->range(($this->page * $this->limit), $this->limit);
    $entity_ids = $query->execute();

    // Must set total result count so we can properly page.
    $this->resultTotal = (int) $count_query->count()->execute();

    // Process Items.
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($entity_ids);

    $this->rawData = $nodes;
  }

  /**
   * {@inheritdoc}
   */
  public function buildAllResourceData() {
    $data = [];

    foreach ($this->rawData as $node) {
      $this->cacheTags = array_merge($this->cacheTags, $node->getCacheTags());
      $created_changed = $this->transformer->createdChangedFieldVals($node);

      $item = [
        'nid' => (int) $node->id(),
        'cms_title' => $node->label(),
        'created' => $created_changed[0],
        'updated' => $created_changed[1],
      ];
      $data[] = $item;
    }

    $this->data = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function buildLinks() {
    // This method should be overridden for any endpoint.
    $base_url = $this->request->getSchemeAndHttpHost() . $this->request->getPathInfo();
    $tmp_query_params = $this->params;
    $tmp_query_params['platform'] = $this->platform;
    $tmp_query_params['limit'] = $this->limit;

    if ($this->page == 0) {
      $this->prev = "";
    }
    else {
      $tmp_query_params['page'] = $this->page - 1;
      $this->prev = $base_url . "?" . http_build_query($tmp_query_params);
    }

    if ($this->resultTotal > (($this->page + 1) * $this->limit)) {
      $tmp_query_params['page'] = $this->page + 1;
      $this->next = $base_url . "?" . http_build_query($tmp_query_params);
    }
    else {

      $this->next = "";
    }

  }

}
