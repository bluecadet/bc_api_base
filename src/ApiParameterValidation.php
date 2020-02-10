<?php

namespace Drupal\bc_api_base;

/**
 * Validate query params.
 */
class ApiParameterValidation {

  /**
   * Validate against annotations.
   */
  public function validateQueryParams(array $annotations, $query_bag) {
    $errors = [];
    $params = [];

    foreach ($annotations as $annotation) {
      if (isset($annotation->params)) {
        foreach ($annotation->params as $param) {

          // Check required.
          if ($param->required && !$query_bag->has($param->name)) {
            $errors[] = [
              'param' => $param->name,
              'error_msg' => "Parameter '" . $param->name . "' is required.",
            ];
          }

          // Grab raw value.
          $raw_value = $query_bag->get($param->name);

          // Setting default if param is empty and default exists.
          if (is_null($raw_value) && $param->default) {
            $raw_value = $param->default;
          }

          switch ($param->type) {
            case "string":

              $params[$param->name] = $raw_value;
              break;

            case "bool":
            case "boolean":
              // Validate it is a bool.
              if ($query_bag->has($param->name) || $param->default) {
                $test_value = filter_var($raw_value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                if (is_null($test_value)) {
                  $errors[] = [
                    'param' => $param->name,
                    'error_msg' => "Parameter '" . $param->name . "' must be a boolean.",
                  ];
                }

                $params[$param->name] = filter_var($raw_value, FILTER_VALIDATE_BOOLEAN);
              }
              break;

            case "int":
              // Validate it is a int.
              if ($query_bag->has($param->name) || $param->default) {
                $test_value = filter_var($raw_value, FILTER_VALIDATE_INT);

                if ($test_value === FALSE) {
                  $errors[] = [
                    'param' => $param->name,
                    'error_msg' => "Parameter '" . $param->name . "' must be an int.",
                  ];
                }

                // Check range if it exists.
                if ((!is_null($param->rangeMin) && $test_value < $param->rangeMin) ||
                (!is_null($param->rangeMax) && $test_value > $param->rangeMax)) {

                  $errors[] = [
                    'param' => $param->name,
                    'error_msg' => "Parameter '" . $param->name . "' must be in range. [" . $param->rangeMin . ", " . $param->rangeMax . "]",
                  ];
                }

                $params[$param->name] = $test_value;
              }
              break;

            case "float":
              // Validate it is a float.
              if ($query_bag->has($param->name) || $param->default) {
                $test_value = filter_var($raw_value, FILTER_VALIDATE_FLOAT);

                if ($test_value === FALSE) {
                  $errors[] = [
                    'param' => $param->name,
                    'error_msg' => "Parameter '" . $param->name . "' must be an float.",
                  ];
                }

                // Check range if it exists.
                if ((!is_null($param->rangeMin) && $test_value < $param->rangeMin) ||
                (!is_null($param->rangeMax) && $test_value > $param->rangeMax)) {

                  $errors[] = [
                    'param' => $param->name,
                    'error_msg' => "Parameter '" . $param->name . "' must be in range. [" . $param->rangeMin . ", " . $param->rangeMax . "].",
                  ];
                }

                $params[$param->name] = $test_value;
              }
              break;

            case "enum":
              // Validate options.
              if ($query_bag->has($param->name) || $param->default) {
                if (!in_array($raw_value, $param->values)) {

                  $errors[] = [
                    'param' => $param->name,
                    'error_msg' => "Parameter '" . $param->name . "' must be in list. [" . implode(", ", $param->values) . "].",
                  ];
                }

                $params[$param->name] = $raw_value;
              }

              break;
          }
        }
      }
    }

    return [$params, $errors];
  }

}
