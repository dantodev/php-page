<?php namespace Dtkahl\PageTest;

use Dtkahl\Page\Page;

class PageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Page
     */
    private $_page;

    public function setUp()
    {
        $this->_page = new Page();
    }

    public function testMagicCall()
    {
        $this->_page->section('foo', 'bar');
        $this->assertEquals("bar", $this->_page->section('foo'));
    }

    public function testSection()
    {
        $this->_page->sections->set('foo', 'bar');
        $this->assertEquals('bar', $this->_page->sections->get('foo'));
        $this->assertEquals(null, $this->_page->sections->get('bar2'));
    }

    public function testArea()
    {
        $this->_page->area('foo')->push('bar1');
        $this->assertEquals(['bar1'], $this->_page->area('foo')->toArray());
        $this->_page->area('foo')->push('bar2');
        $this->assertEquals(['bar1', 'bar2'], $this->_page->area('foo')->toArray());
    }

    public function testMetaAndOptions()
    {
        $this->_page->options->set('title_pattern', '%s | bar');
        $this->_page->meta->set('title', 'foo');
        $this->assertEquals(
            "<title>foo | bar</title>\n<meta name=\"twitter:title\" content=\"foo\">\n<meta property=\"og:title\" content=\"foo\">",
            $this->_page->renderMeta()
        );
    }

    public function testJavascripts()
    {
        $this->_page->addJavascript(['foo', 'bar']);
        $this->_page->option('js_path', 'custom/js/path');
        $this->assertEquals(
            "<script type=\"text/javascript\" src=\"custom/js/path/foo.js\"></script>\n<script type=\"text/javascript\" src=\"custom/js/path/bar.js\"></script>",
            $this->_page->renderJavascripts()
        );
    }

    public function testStylesheets()
    {
        $this->_page->addStylesheet(['foo2', 'bar2']);
        $this->_page->option('css_path', 'custom/css/path');
        $this->_page->option('js_async', true);
        $this->_page->option('css_async', true);
        $this->assertEquals(
            "<link type=\"text/css\" rel=\"stylesheet\" href=\"custom/css/path/foo2.css\" async>\n<link type=\"text/css\" rel=\"stylesheet\" href=\"custom/css/path/bar2.css\" async>",
            $this->_page->renderStylesheets()
        );
    }

}
