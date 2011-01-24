{ezcss_require(array('tagssuggest.css', 'jqmodal.css', 'contentstructure-tree.css'))}
{ezscript_require(array('ezjsc::jquery', 'ezjsc::jqueryio', 'jqModal.js', 'jquery.tagsSuggest-dev.js', 'tagsSuggest-init.js'))}
{def $root_tag = fetch(tags, object, hash(tag_id, $attribute.contentclass_attribute.data_int1))}

{default attribute_base=ContentObjectAttribute}
<div class="tagssuggest" id="tagssuggest">
	<label>{'Selected tags'|i18n( 'extension/eztags/datatypes' )}:</label>
	<div class="tags-list tags-listed no-results">
		<p class="loading">{'Loading'|i18n( 'extension/eztags/datatypes' )}...</p>
		<p class="no-results">{'There are no selected tags'|i18n( 'extension/eztags/datatypes' )}.</p>
	</div>

	{if $attribute.contentclass_attribute.data_int1|eq(0)}
		<label>{'Suggested tags'|i18n( 'extension/eztags/datatypes' )}:</label>
		<div class="tags-list tags-suggested no-results">
			<p class="loading">{'Loading'|i18n( 'extension/eztags/datatypes' )}...</p>
			<p class="no-results">{'There are no tags to suggest'|i18n( 'extension/eztags/datatypes' )}.</p>
		</div>
	{/if}

	<div class="tagssuggestfieldwrap">
		<input class="tagssuggestfield" type="text" size="70" name="xxx_{$attribute_base}_eztags_data_text_{$attribute.id}" value="" autocomplete="off"  />
	</div>

	{if fetch('user', 'has_access_to', hash('module', 'tags', 'function', 'add'))}
		<input type="button" value="{'Add new'|i18n( 'extension/eztags/datatypes' )}" name="AddTagButton" class="button-add-tag button-disabled" disabled="disabled">
	{/if}

	<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}" class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier} tagnames" type="hidden" name="{$attribute_base}_eztags_data_text_{$attribute.id}" value="{$attribute.content.keyword_string|wash(xhtml)}"  />

	<input id="ezcoa2-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}" class="box tagpids" type="hidden" name="{$attribute_base}_eztags_data_text2_{$attribute.id}" value="{$attribute.content.parent_string|wash(xhtml)}"  />

	<input type="hidden" name="eztags_subtree_limit" id="eztags_subtree_limit" value="{$attribute.contentclass_attribute.data_int1}" />
</div>

{include uri='design:ezjsctemplate/modal_dialog.tpl' root_tag=$root_tag}
{/default}