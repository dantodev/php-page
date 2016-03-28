<?php namespace Dtkahl\PageResponse;

use Dtkahl\ArrayTools\Collection;
use Dtkahl\ArrayTools\Map;
use Dtkahl\HtmlTagBuilder\HtmlTagBuilder;
use Dtkahl\SimpleView\ViewRenderer;
use Slim\Http\Headers;
use Slim\Http\Response;


/**
 * Class PageResponse
 * @package Dtkahl\PageResponse
 * @property Map $meta;
 * @property Map $options;
 * @property Map $sections;
 * @property Map $render_data;
 */
class PageResponse extends Response {

	private $_renderer;
  private $_render_data;
	private $_meta;
	private $_options;
	private $_scripts;
	private $_styles;
	private $_sections;

  /**
   * @param ViewRenderer $renderer
   * @param string $master_view
   * @param array $render_data
   */
	public function __construct(ViewRenderer $renderer, $master_view, array $render_data = [])
	{
		$this->_renderer    = $renderer;
		$this->_render_data = new Map($render_data);
		$this->_meta 				= new Map();
		$this->_options 		= new Map(['master_view' => (string) $master_view]);
    $this->_sections  	= new Map();
    $this->_scripts			= new Collection();
    $this->_styles			= new Collection();

		$headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
		parent::__construct(200, $headers);
	}

  /**
   * @param string $name
   * @return null
   */
  public function __get($name)
  {
    if (in_array($name, ['meta', 'options', 'sections', 'render_data'])) {
      return $this->{'_'.$name};
    }
    return null;
  }

  /**
   * @param array $render_data
   * @return $this
   */
	public function render(array $render_data = [])
	{
		$this->getBody()->write($this->view($this->_options->get('master_view'), array_merge(
        $this->_render_data->toArray(),
        $render_data,
        ['response' => $this]
    )));
		return $this;
	}

  /**
   * @param $file
   * @param array $data
   * @return string
   */
	public function view($file, $data = [])
	{
    return $this->_renderer->render($file, $data);
	}

  /**
   * @return string
   */
	public function renderMeta()
	{
		$html = [];
		foreach ($this->_meta->toArray() as $type=>$value) {
		  switch ($type) {

			case 'title':
        $title = sprintf($this->options->get('title_pattern', '%s'), $value);
			  $html[] = (new HtmlTagBuilder('title', [], $title))->render();
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
		$this->_scripts->merge((array) $js);
		return $this;
	}

  /**
   * @param string|string[] $css
   * @return $this
   */
	public function addStylesheet($css)
	{
    $this->_styles->merge((array) $css);
		return $this;
	}

  /**
   * @return string
   */
	public function renderJavascripts()
	{
		$elements = [];
		while ($js = $this->_scripts->next()) {
      $elements[] = (new HtmlTagBuilder('script', [
        'type' => "text/javascript",
        'src' => "js/$js.js"
      ]))->render();
		}
		return implode("\n", $elements);
	}

  /**
   * @return string
   */
	public function renderStylesheets()
	{
    $elements = [];
    while ($css = $this->_styles->next()) {
      $elements[] = (new HtmlTagBuilder('link', [
          'type' => "text/css",
          'rel' => "stylesheet",
          'src' => "css/$css.css"
      ]))->render();
		}
		return implode("\n", $elements);
	}

}
