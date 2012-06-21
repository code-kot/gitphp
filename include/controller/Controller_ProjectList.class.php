<?php
/**
 * Controller for listing projects
 *
 * @author Christopher Han <xiphux@gmail.com>
 * @copyright Copyright (c) 2010 Christopher Han
 * @package GitPHP
 * @subpackage Controller
 */
class GitPHP_Controller_ProjectList extends GitPHP_ControllerBase
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->multiProject = true;
		parent::__construct();
	}

	/**
	 * Gets the template for this controller
	 *
	 * @return string template filename
	 */
	protected function GetTemplate()
	{
		if (isset($this->params['opml']) && ($this->params['opml'] === true)) {
			return 'opml.tpl';
		} else if (isset($this->params['txt']) && ($this->params['txt'] === true)) {
			return 'projectindex.tpl';
		}
		return 'projectlist.tpl';
	}

	/**
	 * Gets the cache key for this controller
	 *
	 * @return string cache key
	 */
	protected function GetCacheKey()
	{
		if (isset($this->params['opml']) && ($this->params['opml'] === true)) {
			return '';
		} else if (isset($this->params['txt']) && ($this->params['txt'] === true)) {
			return '';
		}
		return $this->params['order'] . '|' . (isset($this->params['search']) ? $this->params['search'] : '');
	}

	/**
	 * Gets the name of this controller's action
	 *
	 * @param boolean $local true if caller wants the localized action name
	 * @return string action name
	 */
	public function GetName($local = false)
	{
		if (isset($this->params['opml']) && ($this->params['opml'] === true)) {
			if ($local) {
				return __('opml');
			}
			return 'opml';
		} else if (isset($this->params['txt']) && ($this->params['txt'] === true)) {
			if ($local) {
				return __('project index');
			}
			return 'project index';
		}
		if ($local) {
			return __('projects');
		}
		return 'projects';
	}

	/**
	 * Read query into parameters
	 */
	protected function ReadQuery()
	{
		if (isset($_GET['o']))
			$this->params['order'] = $_GET['o'];
		else
			$this->params['order'] = 'project';
		if (isset($_GET['s']))
			$this->params['search'] = $_GET['s'];
	}

	/**
	 * Loads headers for this template
	 */
	protected function LoadHeaders()
	{
		if (isset($this->params['opml']) && ($this->params['opml'] === true)) {
			$this->headers[] = "Content-type: text/xml; charset=UTF-8";
			GitPHP_DebugLog::GetInstance()->SetEnabled(false);
			$this->preserveWhitespace = true;
		} else if (isset($this->params['txt']) && ($this->params['txt'] === true)) {
			$this->headers[] = "Content-type: text/plain; charset=utf-8";
			$this->headers[] = "Content-Disposition: inline; filename=\"index.aux\"";
			GitPHP_DebugLog::GetInstance()->SetEnabled(false);
		}
	}

	/**
	 * Loads data for this template
	 */
	protected function LoadData()
	{
		$this->tpl->assign('order', $this->params['order']);
		
		$this->projectList->Sort($this->params['order']);

		if ((empty($this->params['opml']) || ($this->params['opml'] !== true)) &&
		    (empty($this->params['txt']) || ($this->params['txt'] !== true)) &&
		    (!empty($this->params['search']))) {
		    	$this->tpl->assign('search', $this->params['search']);
			$matches = $this->projectList->Filter($this->params['search']);
			if (count($matches) > 0) {
				$this->tpl->assign('projectlist', $matches);
			}
		} else {
			if ($this->projectList->Count() > 0)
				$this->tpl->assign('projectlist', $this->projectList);
		}
	}

}
