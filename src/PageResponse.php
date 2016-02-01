<?php namespace Dtkahl\PageResponse;

use Slim\Http\Headers;
use Slim\Http\Response;

class PageResponse extends Response {

	private $_driver;
	private $_master_view;
	private $_meta = [];
	private $_title_pattern = "%s";
	private $_javascripts = [];
	private $_stylesheets = [];
	private $_render_data = [];
	private $_sections = [];

	public function __construct(Callable $driver)
	{
		$this->_driver = $driver; // TODO resolveDriver + ability to add Driver
    $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
    parent::__construct(200, $headers);

	}

	public function render()
	{
		return call_user_func($this->_driver, $this);
	}

  /**
   * @param string $master_view
   * @return $this
   */
	public function setMasterView($master_view)
	{
		$this->_master_view = $master_view;
    return $this;
	}

  /**
   * @return string
   */
	public function getMasterView()
	{
		return $this->_master_view;
	}

  /**
   * @param $type
   * @param $value
   * @return $this
   */
	public function meta($type, $value)
	{
		$this->_meta = array_merge($this->_meta, arrnize($type, $value));
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
			  $html[] = buildHtmlTag('title', [], sprintf($this->_title_pattern, ...$value));
			  $html[] = buildHtmlTag('meta', ['name' => 'twitter:title', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['property' => 'og:title', 'content' => $value]);
			  break;
			  
			case 'charset':
			  $html[] = buildHtmlTag('meta', ['charset' => $value]);
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
			  $html[] = buildHtmlTag('meta', ['name' => $type, 'content' => $value]);
			  break;

			case 'twitter:card':
			case 'local':
			case 'og:site_name':
			  $html[] = buildHtmlTag('meta', ['property' => $type, 'content' => $value]);
			  break;

			case 'description':
			  $html[] = buildHtmlTag('meta', ['name' => 'description', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['name' => 'twitter:description', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['property' => 'og:description', 'content' => $value]);
			  break;

			case 'image':
			  $html[] = buildHtmlTag('meta', ['name' => 'description', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['name' => 'twitter:description', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['property' => 'og:description', 'content' => $value]);
			  break;

			case 'url':
			  $html[] = buildHtmlTag('meta', ['name' => 'twitter:url', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['property' => 'og:url', 'content' => $value]);
			  break;

			case 'author':
			  $html[] = buildHtmlTag('meta', ['name' => 'author', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['property' => 'og:author', 'content' => $value]);
			  break;

			case 'publisher':
			  $html[] = buildHtmlTag('meta', ['name' => 'publisher', 'content' => $value]);
			  $html[] = buildHtmlTag('meta', ['property' => 'og:publisher', 'content' => $value]);
			  break;

			case 'language':
			  $html[] = buildHtmlTag('meta', ['http-equiv' => 'content-language', 'content' => $value]);
			  break;

			case 'raw':
			  $html[] = $value;
			  break;
		  }
		}
		return implode("\n", $html);
	}

  /**
   * @param $data
   * @return $this
   */
	public function addJavascript($data)
	{
		array_merge($this->_javascripts, arrnize($data));
		return $this;
	}

  /**
   * @param $data
   * @return $this
   */
	public function addStylesheets($data)
	{
		array_merge($this->_stylesheets, arrnize($data));
		return $this;
	}

  /**
   * @return string
   */
	public function renderJavascripts()
	{
		$html = "";
		foreach ($this->_javascripts as $js) {
			$html .= sprintf('<script type="text/javascript" src="%s.js"></script>\n', $js);
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
			$html .= sprintf('<link type="text/css" rel="stylesheet" href="%s.css"></script>\n', $css);
		}
		return $html;
	}
	
	public function renderData(...$params)
	{
		$this->_render_data = array_merge($this->_render_data, arrnize(...$params));
	}
	
	public function getRenderData()
	{
		return $this->_render_data;
	}
	
	public function section(...$params)
	{
		$this->_sections = array_merge($this->_sections, arrnize(...$params));
		return $this;
	}
	
	public function getSections()
	{
		return $this->_sections;
	}

}