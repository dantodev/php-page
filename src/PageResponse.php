<?php namespace Dtkahl\PageResponse;

use Dtkahl\HtmlTagBuilder\HtmlTagBuilder;
use Dtkahl\SimpleView\ViewRenderer;
use Slim\Http\Headers;
use Slim\Http\Response;


/**
 * Class PageResponse
 * @package Dtkahl\PageResponse
 */
class PageResponse extends Response {

	private $_renderer;
	private $_master_view;
  private $_render_data = [];
	private $_meta = [];
	private $_title_pattern = "%s";
	private $_javascripts = [];
	private $_stylesheets = [];
	private $_sections = [];

	public function __construct(ViewRenderer $renderer, string $master_view, array $render_data = [])
	{
		$this->_renderer    = $renderer;
		$this->_master_view = $master_view;
		$this->_render_data = $render_data;

		$headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
		parent::__construct(200, $headers);
	}

	public function render(array $render_data = [])
	{
		$this->getBody()->write($this->_renderer->render($this->_master_view, array_merge(
        $this->_render_data,
        $render_data,
        ['response' => $this]
    )));
		return $this;
	}

	public function view($file, $data = [])
	{
    return $this->_renderer->render( $file, $data);
	}

  /**
   * @param $type
   * @param $value
   * @return $this
   */
	public function setMeta($type, $value)
	{
    $this->_meta[$type] = $value;
		return $this;
	}

	/**
	 * @param $pattern
	 * @return $this
	 */
	public function setTitlePattern($pattern)
	{
		$this->_title_pattern = $pattern;
		return $this;
	}

  /**
   * @return string
   */
	public function renderMeta()
	{
		$html = [];
		foreach ($this->_meta as $type=>$value) {
		  switch ($type) {

			case 'title':
			  $html[] = (new HtmlTagBuilder('title', [], sprintf($this->_title_pattern, $value)))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'twitter:title', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['property' => 'og:title', 'content' => $value]))->render();
			  break;
			  
			case 'charset':
			  $html[] = (new HtmlTagBuilder('meta', ['charset' => $value]))->render();
			  break;

			case 'date':
			case 'copyright':
			case 'keywords':
			case 'viewport':
			case 'robots':
			case 'page-topic':
			case 'page-type':
			case 'og:type':
			case 'audience':
			case 'google-site-verification':
			case 'csrf-token':
			case 'twitter:site':
			  $html[] = (new HtmlTagBuilder('meta', ['name' => $type, 'content' => $value]))->render();
			  break;

			case 'twitter:card':
			case 'local':
			case 'og:site_name':
			  $html[] = (new HtmlTagBuilder('meta', ['property' => $type, 'content' => $value]))->render();
			  break;

			case 'description':
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'description', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'twitter:description', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['property' => 'og:description', 'content' => $value]))->render();
			  break;

			case 'image':
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'description', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'twitter:description', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['property' => 'og:description', 'content' => $value]))->render();
			  break;

			case 'url':
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'twitter:url', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['property' => 'og:url', 'content' => $value]))->render();
			  break;

			case 'author':
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'author', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['property' => 'og:author', 'content' => $value]))->render();
			  break;

			case 'publisher':
			  $html[] = (new HtmlTagBuilder('meta', ['name' => 'publisher', 'content' => $value]))->render();
			  $html[] = (new HtmlTagBuilder('meta', ['property' => 'og:publisher', 'content' => $value]))->render();
			  break;

			case 'language':
			  $html[] = (new HtmlTagBuilder('meta', ['http-equiv' => 'content-language', 'content' => $value]))->render();
			  break;

			case 'raw':
			  $html[] = $value;
			  break;
		  }
		}
		return implode("\n", $html);
	}

  /**
   * @param string|string[] $js
   * @return $this
   */
	public function addJavascript($js)
	{
		$this->_javascripts = array_merge($this->_javascripts, (array) $js);
		return $this;
	}

  /**
   * @param string|string[] $css
   * @return $this
   */
	public function addStylesheet($css)
	{
		$this->_stylesheets = array_merge($this->_stylesheets, (array) $css);
		return $this;
	}

  /**
   * @return string
   */
	public function renderJavascripts()
	{
		$html = "";
		foreach ($this->_javascripts as $js) {
      $html .= (new HtmlTagBuilder('script', [
        'type' => "text/javascript",
        'src' => "js/$js.js"
      ]))->render()."\n";
		}
		return $html;
	}

  /**
   * @return string
   */
	public function renderStylesheets()
	{
		$html = "";
		foreach ($this->_stylesheets as $css) {
      $html .= (new HtmlTagBuilder('script', [
          'type' => "text/css",
          'rel' => "stylesheet",
          'src' => "css/$css.css"
      ]))->render()."\n";
		}
		return $html;
	}

  /**
   * @param $name
   * @param $value
   * @return $this
   */
  public function setSection($name, $value)
  {
    $this->_sections[$name] = $value;
    return $this;
  }

  /**
   * @param $name
   * @return string
   */
	public function renderSection($name)
	{
		return array_key_exists($name, $this->_sections) ? $this->_sections[$name] : '';
	}

}
