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
    $this->assertInstanceOf(Response::class, $this->_response->render(['foo2' => 'bar2']));
    $this->assertEquals(
      '<!DOCTYPE html><html><head></head><body><div>bar</div><div>bar2</div></body></html>',
        $this->_response->getBody()->__toString()
    );
  }

  public function testSection()
  {
    $this->_response->setSection('foo', 'bar');
    $this->assertEquals('bar', $this->_response->renderSection('foo'));
    $this->assertEquals(null, $this->_response->renderSection('bar2'));
  }

  public function testMetaAndTitlePattern()
  {
    $this->_response
      ->setTitlePattern('%s | bar')
      ->setMeta('title', 'foo');
    $this->assertEquals(
        "<title>foo | bar</title>\n<meta name=\"twitter:title\" content=\"foo\">\n<meta property=\"og:title\" content=\"foo\">",
        $this->_response->renderMeta()
    );
  }

  public function testJavascripts()
  {
    $this->_response->addJavascript(['foo', 'bar']);
    $this->assertEquals(
        "<script type=\"text/javascript\" src=\"js/foo.js\"></script>\n<script type=\"text/javascript\" src=\"js/bar.js\"></script>",
        $this->_response->renderJavascripts()
    );
  }

  public function testStylesheets()
  {
    $this->_response->addStylesheet(['foo2', 'bar2']);
    $this->assertEquals(
        "<script type=\"text/javascript\" src=\"js/foo.js\"></script>\n<script type=\"text/javascript\" src=\"js/bar.js\"></script>",
        $this->_response->renderStylesheets()
    );
  }

}