<?php namespace Dtkahl\PageResponse;

use Dtkahl\SimpleView\ViewRenderer;
use Slim\Http\Response;

class PageResponseTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var PageResponse
   */
  private $_response;

  public  function setUp()
  {
    $this->_response = new PageResponse(new ViewRenderer(__DIR__.'/testviews/'), 'layout.php', ['foo' => 'bar']);
  }

  public function testRender()
  {
    $this->assertEquals(
      '<!DOCTYPE html><html><head></head><body><div>bar</div><div>bar2</div></body></html>',
      $this->_response->render(['foo2' => 'bar2'])->getBody()->__toString()
    );
  }

  public function testSection()
  {

  }

  public function testMeta()
  {

  }

  public function testJavascripts()
  {

  }

  public function testStylesheets()
  {

  }

}