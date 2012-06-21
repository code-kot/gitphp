<?php
/**
 * Controller for displaying a log
 *
 * @author Christopher Han
 * @copyright Copyright (c) 2010 Christopher Han
 * @package GitPHP
 * @subpackage Controller
 */
class GitPHP_Controller_Log extends GitPHP_ControllerBase
{

	/**
	 * Gets the template for this controller
	 *
	 * @return string template filename
	 */
	protected function GetTemplate()
	{
		if (isset($this->params['short']) && ($this->params['short'] === true)) {
			return 'shortlog.tpl';
		}
		return 'log.tpl';
	}

	/**
	 * Gets the cache key for this controller
	 *
	 * @return string cache key
	 */
	protected function GetCacheKey()
	{
		return $this->params['hash'] . '|' . $this->params['page'] . '|' . (isset($this->params['mark']) ? $this->params['mark'] : '');
	}

	/**
	 * Gets the name of this controller's action
	 *
	 * @param boolean $local true if caller wants the localized action name
	 * @return string action name
	 */
	public function GetName($local = false)
	{
		if (isset($this->params['short']) && ($this->params['short'] === true)) {
			if ($local) {
				return __('shortlog');
			}
			return 'shortlog';
		}
		if ($local) {
			return __('log');
		}
		return 'log';
	}

	/**
	 * Read query into parameters
	 */
	protected function ReadQuery()
	{
		if (isset($_GET['h']))
			$this->params['hash'] = $_GET['h'];
		else
			$this->params['hash'] = 'HEAD';
		if (isset($_GET['pg']))
			$this->params['page'] = $_GET['pg'];
		else
			$this->params['page'] = 0;
		if (isset($_GET['m']))
			$this->params['mark'] = $_GET['m'];
	}

	/**
	 * Loads data for this template
	 */
	protected function LoadData()
	{
		$commit = $this->GetProject()->GetCommit($this->params['hash']);
		$this->tpl->assign('commit', $commit);
		$this->tpl->assign('head', $this->GetProject()->GetHeadCommit());
		$this->tpl->assign('page',$this->params['page']);

		$revlist = new GitPHP_Log($this->GetProject(), $commit, 101, ($this->params['page'] * 100));
		$revlist->SetCompat($this->GetProject()->GetCompat());
		if ($this->config->HasKey('largeskip')) {
			$revlist->SetSkipFallback($this->config->GetValue('largeskip'));
		}

		if ($revlist->GetCount() > 100) {
			$this->tpl->assign('hasmorerevs', true);
			$revlist->SetLimit(100);
		}
		$this->tpl->assign('revlist', $revlist);

		if (isset($this->params['mark'])) {
			$this->tpl->assign('mark', $this->GetProject()->GetCommit($this->params['mark']));
		}
	}

}
