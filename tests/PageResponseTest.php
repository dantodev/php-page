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

  public function testMagicCall()
  {
    $this->_response->section('foo', 'bar');
    $this->assertEquals("bar", $this->_response->section('foo'));
  }

  public function testRender()
  {
    $this->assertInstanceOf(Response::class, $this->_response->render(['foo2' => 'bar2']));
    $this->assertEquals(
      '<!DOCTYPE html><html><head></head><body><div>bar</div><div>bar2</div></body></html>',
        $this->_response->getBody()->__toString()
    );
  }

  public function testRenderData()
  {
    $this->_response->render_data->set('foo3', 'bar3');
    $this->assertEquals('bar3', $this->_response->render_data->get('foo3'));
  }

  public function testSection()
  {
    $this->_response->sections->set('foo', 'bar');
    $this->assertEquals('bar', $this->_response->sections->get('foo'));
    $this->assertEquals(null, $this->_response->sections->get('bar2'));
  }

  public function testMetaAndOptions()
  {
    $this->_response->options->set('title_pattern', '%s | bar');
    $this->_response->meta->set('title', 'foo');
    $this->assertEquals(
        "<title>foo | bar</title>\n<meta name=\"twitter:title\" content=\"foo\">\n<meta property=\"og:title\" content=\"foo\">",
        $this->_response->renderMeta()
    );
  }

  public function testJavascripts()
  {
    $this->_response->addJavascript(['foo', 'bar']);
    $this->_response->option('js_path', 'custom/js/path');
    $this->assertEquals(
        "<script type=\"text/javascript\" src=\"custom/js/path/foo.js\"></script>\n<script type=\"text/javascript\" src=\"custom/js/path/bar.js\"></script>",
        $this->_response->renderJavascripts()
    );
  }

  public function testStylesheets()
  {
    $this->_response->addStylesheet(['foo2', 'bar2']);
    $this->_response->option('css_path', 'custom/css/path');
    $this->assertEquals(
        "<link type=\"text/css\" rel=\"stylesheet\" src=\"custom/css/path/foo2.css\">\n<link type=\"text/css\" rel=\"stylesheet\" src=\"custom/css/path/bar2.css\">",
        $this->_response->renderStylesheets()
    );
  }

}