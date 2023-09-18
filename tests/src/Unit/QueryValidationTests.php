<?php

namespace Drupal\Tests\bc_api_base\Unit;

use Drupal\bc_api_base\Annotation\ApiDoc;
use Drupal\bc_api_base\Annotation\ApiParam;
use Drupal\bc_api_base\ApiParameterValidation;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Unit tests for the MyStatus utility class.
 *
 * @group bc_api
 * @group bc_api_base
 * @group bc_api_base:unit
 */
class QueryValidationTests extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    // Nothing to do here.
    parent::setUp();
  }

  /**
   * Test String param.
   */
  public function testStringParam() {
    $val = new ApiParameterValidation();

    $annotations = [
      new ApiDoc(),
    ];

    // No errors on a single string param.
    $param1 = new ApiParam();
    $param1->name = "string1";
    $param1->type = "string";
    $param1->description = "";
    $param1->default = NULL;
    $param1->required = FALSE;

    $annotations[0]->params = [$param1];

    $query_bag = new ParameterBag([
      "string1" => "test",
    ]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["string1" => "test"], $result1[0], "1.1 There should be one param set.");
    $this->assertEquals([], $result1[1], "1.2 There should be no errors.");

    // Default for string param.
    $param2 = new ApiParam();
    $param2->name = "string2";
    $param2->type = "string";
    $param2->description = "";
    $param2->default = "Does this appear";
    $param2->required = FALSE;

    $annotations[0]->params = [$param2];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["string2" => "Does this appear"], $result1[0], "2.1 There should be one param set from default value.");
    $this->assertEquals([], $result1[1], "2.2 There should be no errors.");

    // Error on required param.
    $param3 = new ApiParam();
    $param3->name = "string3";
    $param3->type = "string";
    $param3->description = "";
    $param3->default = "";
    $param3->required = TRUE;

    $annotations[0]->params = [$param3];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["string3" => ""], $result1[0], "3.1 There should be one param set from default value.");
    $this->assertEquals('string3', $result1[1][0]['param'], "3.2 There should be an errors.");

  }

  /**
   * Test Boolean param.
   */
  public function testBoolParam() {
    $val = new ApiParameterValidation();

    $annotations = [
      new ApiDoc(),
    ];

    // No errors on a single bool param.
    $param1 = new ApiParam();
    $param1->name = "bool1";
    $param1->type = "bool";
    $param1->description = "";
    $param1->default = NULL;
    $param1->required = FALSE;

    $annotations[0]->params = [$param1];

    $query_bag = new ParameterBag([
      "bool1" => TRUE,
    ]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["bool1" => TRUE], $result1[0], "1.1 There should be one param set.");
    $this->assertEquals([], $result1[1], "1.2 There should be no errors.");

    // Default for bool param.
    $param2 = new ApiParam();
    $param2->name = "bool2";
    $param2->type = "bool";
    $param2->description = "";
    $param2->default = TRUE;
    $param2->required = FALSE;

    $annotations[0]->params = [$param2];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["bool2" => TRUE], $result1[0], "2.1 There should be one param set from default value.");
    $this->assertEquals([], $result1[1], "2.2 There should be no errors.");

    // Error on required param.
    $param3 = new ApiParam();
    $param3->name = "bool3";
    $param3->type = "string";
    $param3->description = "";
    $param3->default = TRUE;
    $param3->required = TRUE;

    $annotations[0]->params = [$param3];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["bool3" => TRUE], $result1[0], "3.1 There should be one param set from default value.");
    $this->assertEquals('bool3', $result1[1][0]['param'], "3.2 There should be an errors.");

    // Different Values of TRUE. String|bool|int in queray param.
    $param4 = new ApiParam();
    $param4->name = "bool4";
    $param4->type = "bool";
    $param4->description = "";
    $param4->default = TRUE;
    $param4->required = TRUE;

    $param5 = new ApiParam();
    $param5->name = "bool5";
    $param5->type = "bool";
    $param5->description = "";
    $param5->default = TRUE;
    $param5->required = TRUE;

    $param6 = new ApiParam();
    $param6->name = "bool6";
    $param6->type = "bool";
    $param6->description = "";
    $param6->default = TRUE;
    $param6->required = TRUE;

    $annotations[0]->params = [$param4, $param5, $param6];

    $query_bag = new ParameterBag([
      "bool4" => TRUE,
      "bool5" => "true",
      "bool6" => 1,
    ]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals([
      "bool4" => TRUE,
      "bool5" => TRUE,
      "bool6" => TRUE,
    ], $result1[0], "4.1 There should be one param set from default value.");
    $this->assertEquals([], $result1[1], "4.2 There should be no errors.");

  }

  /**
   * Test Int param.
   */
  public function testIntParam() {
    $val = new ApiParameterValidation();

    $annotations = [
      new ApiDoc(),
    ];

    // No errors on a single int param.
    $param1 = new ApiParam();
    $param1->name = "int1";
    $param1->type = "int";
    $param1->description = "";
    $param1->default = NULL;
    $param1->required = FALSE;

    $annotations[0]->params = [$param1];

    $query_bag = new ParameterBag([
      "int1" => 4,
    ]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["int1" => 4], $result1[0], "1.1 There should be one param set.");
    $this->assertEquals([], $result1[1], "1.2 There should be no errors.");

    // Default for int param.
    $param2 = new ApiParam();
    $param2->name = "int2";
    $param2->type = "int";
    $param2->description = "";
    $param2->default = 42;
    $param2->required = FALSE;

    $annotations[0]->params = [$param2];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["int2" => 42], $result1[0], "2.1 There should be one param set from default value.");
    $this->assertEquals([], $result1[1], "2.2 There should be no errors.");

    // Error on required param.
    $param3 = new ApiParam();
    $param3->name = "int3";
    $param3->type = "int";
    $param3->description = "";
    $param3->default = 42;
    $param3->required = TRUE;

    $annotations[0]->params = [$param3];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["int3" => 42], $result1[0], "3.1 There should be one param set from default value.");
    $this->assertEquals('int3', $result1[1][0]['param'], "3.2 There should be one error.");

    // Error on Float value.
    $param4 = new ApiParam();
    $param4->name = "int4";
    $param4->type = "int";
    $param4->description = "";
    $param4->default = NULL;
    $param4->required = FALSE;

    $annotations[0]->params = [$param4];

    $query_bag = new ParameterBag([
      "int4" => 42.5,
    ]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["int4" => FALSE], $result1[0], "4.1 There should be one param set.");
    $this->assertEquals('int4', $result1[1][0]['param'], "4.2 There should be no errors.");

  }

  /**
   * Test Float param.
   */
  public function testFloatParam() {
    $val = new ApiParameterValidation();

    $annotations = [
      new ApiDoc(),
    ];

    // No errors on a single int param.
    $param1 = new ApiParam();
    $param1->name = "float1";
    $param1->type = "float";
    $param1->description = "";
    $param1->default = NULL;
    $param1->required = FALSE;

    $annotations[0]->params = [$param1];

    $query_bag = new ParameterBag([
      "float1" => 42.5,
    ]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["float1" => 42.5], $result1[0], "1.1 There should be one param set.");
    $this->assertEquals([], $result1[1], "1.2 There should be no errors.");

    // Default for int param.
    $param2 = new ApiParam();
    $param2->name = "float2";
    $param2->type = "float";
    $param2->description = "";
    $param2->default = 42.5;
    $param2->required = FALSE;

    $annotations[0]->params = [$param2];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["float2" => 42.5], $result1[0], "2.1 There should be one param set from default value.");
    $this->assertEquals([], $result1[1], "2.2 There should be no errors.");

    // Error on required param.
    $param3 = new ApiParam();
    $param3->name = "float3";
    $param3->type = "float";
    $param3->description = "";
    $param3->default = 42.5;
    $param3->required = TRUE;

    $annotations[0]->params = [$param3];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["float3" => 42.5], $result1[0], "3.1 There should be one param set from default value.");
    $this->assertEquals('float3', $result1[1][0]['param'], "3.2 There should be one error.");
  }

  /**
   * Test Enum param.
   */
  public function testEnumParam() {
    $val = new ApiParameterValidation();

    $annotations = [
      new ApiDoc(),
    ];

    // No errors on a single int param.
    $param1 = new ApiParam();
    $param1->name = "enum1";
    $param1->type = "enum";
    $param1->description = "";
    $param1->default = NULL;
    $param1->required = FALSE;
    $param1->values = ["Bert", "Ernie", "Oscar"];

    $annotations[0]->params = [$param1];

    $query_bag = new ParameterBag([
      "enum1" => "Bert",
    ]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["enum1" => "Bert"], $result1[0], "1.1 There should be one param set.");
    $this->assertEquals([], $result1[1], "1.2 There should be no errors.");

    // Default for int param.
    $param2 = new ApiParam();
    $param2->name = "enum2";
    $param2->type = "enum";
    $param2->description = "";
    $param2->default = "Ernie";
    $param2->required = FALSE;
    $param2->values = ["Bert", "Ernie", "Oscar"];

    $annotations[0]->params = [$param2];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["enum2" => "Ernie"], $result1[0], "2.1 There should be one param set from default value.");
    $this->assertEquals([], $result1[1], "2.2 There should be no errors.");

    // Error on required param.
    $param3 = new ApiParam();
    $param3->name = "enum3";
    $param3->type = "enum";
    $param3->description = "";
    $param3->default = 42;
    $param3->required = TRUE;
    $param3->values = ["Bert", "Ernie", "Oscar"];

    $annotations[0]->params = [$param3];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["enum3" => 42], $result1[0], "3.1 There should be one param set from default value.");
    $this->assertEquals("enum3", $result1[1][0]['param'], "3.2 There should be one error.");

    // Test bad input for default value.
    $param4 = new ApiParam();
    $param4->name = "enum4";
    $param4->type = "enum";
    $param4->description = "";
    $param4->default = "Elmo";
    $param4->required = FALSE;
    $param4->values = ["Bert", "Ernie", "Oscar"];

    $annotations[0]->params = [$param4];

    $query_bag = new ParameterBag([]);

    $result1 = $val->validateQueryParams($annotations, $query_bag);

    $this->assertEquals(["enum4" => "Elmo"], $result1[0], "4.1 There should be one param set from default value.");
    $this->assertEquals("enum4", $result1[1][0]['param'], "4.2 There should be an errors.");
  }

}
