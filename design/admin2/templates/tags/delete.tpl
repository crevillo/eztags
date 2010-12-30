{def $children_object_count = 0}
{def $synonym_object_count = 0}

<div class="context-block tags-delete">
	<div class="box-header">
		<h1 class="context-title">{"Delete tag"|i18n('extension/eztags/tags/edit')}: {$tag.keyword|wash(xhtml)} [{$tag.id}]</h1>
		<div class="header-mainline"></div>
	</div>

	<div class="box-content">
		<form name="tagdeleteform" id="tagdeleteform" enctype="multipart/form-data" method="post" action={concat('tags/delete/', $tag.id)|ezurl}>
			<p>{'Are you sure you want to delete the "%keyword" tag? All children tags and synonyms will also be deleted and removed from existing objects.'|i18n( 'extension/eztags/tags/edit',, hash('%keyword', $tag.keyword|wash(xhtml)))}</p>

			<p>{'The tag you\'re about to delete has'|i18n( 'extension/eztags/tags/edit' )}:</p>
			<ul>
				<li>{'number of first level children tags'|i18n( 'extension/eztags/tags/edit' )}: {$tag.children|count}</li>
				{foreach $tag.children as $child}{set $children_object_count = $children_object_count|sum($child.related_objects|count)}{/foreach}
				<li>{'number of objects related to first level children tags'|i18n( 'extension/eztags/tags/edit' )}: {$children_object_count}</li>
				<li>{'number of synonyms'|i18n( 'extension/eztags/tags/edit' )}: {$tag.synonyms|count}</li>
				{foreach $tag.synonyms as $synonym}{set $synonym_object_count = $synonym_object_count|sum($synonym.related_objects|count)}{/foreach}
				<li>{'number of objects related to synonyms'|i18n( 'extension/eztags/tags/edit' )}: {$synonym_object_count}</li>
			</ul>

			<div class="controlbar">
				<div class="block">
					<input class="defaultbutton" type="submit" name="YesButton" value="{'Yes'|i18n( 'extension/eztags/tags/edit' )}" />
					<input class="button" type="submit" name="NoButton" value="{'No'|i18n( 'extension/eztags/tags/edit' )}" />
				</div>
			</div>
		</form>
	</div>
</div>

{undef}