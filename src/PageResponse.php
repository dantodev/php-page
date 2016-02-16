<?php namespace Dtkahl\PageResponse;

use Slim\App;
use Slim\Http\Headers;
use Slim\Http\Response;


/**
 * Class PageResponse
 * @package Dtkahl\PageResponse
 * @property \Slim\App $_app
 * @property \Dtkahl\SimplePhpView\ViewRenderer$_view
 */
class PageResponse extends Response {

	private $_app;
	private $_view;
	private $_master_view;
	private $_meta = [];
	private $_title_pattern = "%s";
	private $_javascripts = [];
	private $_stylesheets = [];
	private $_render_data = [];
	private $_sections = [];

	public function __construct(App $app)
	{
		$this->_app 			= $app;
		$container  			= $this->_app->getContainer();

		$this->_view 			= $container->get('view');

		$headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
		parent::__construct(200, $headers);
	}

	public function render()
	{
		$this->getBody()->write($this->_view->render($this->getMasterView(), $this->getRenderData()));
		return $this;
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

	public function view($file, $data = [])
	{
    return $this->_view->render( $file, $data);
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
		$this->_javascripts = array_merge($this->_javascripts, arrnize($data));
		return $this;
	}

  /**
   * @param $data
   * @return $this
   */
	public function addStylesheets($data)
	{
		$this->_stylesheets = array_merge($this->_stylesheets, arrnize($data));
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
	
	public function renderData()
	{
    $params = func_get_args(); // PHP7 will make that better ...
		$this->_render_data = array_merge($this->_render_data, call_user_func_array('arrnize', $params)); // this will also be better with PHP7 ...
	}

	public function getRenderData()
	{
		return array_merge($this->_render_data, ['response' => $this]);
	}
	
	public function section()
	{
    $params = func_get_args(); // PHP7 will make that better ...
		$this->_sections = array_merge($this->_sections, call_user_func_array('arrnize', $params)); // this will also be better with PHP7 ...
		return $this;
	}
	
	public function renderSection($name)
	{
		return array_key_exists($name, $this->_sections) ? $this->_sections[$name] : '';
	}

}
