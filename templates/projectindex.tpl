{foreach from=$projectlist item=proj}
{$proj->GetProject()|escape}
{/foreach}
