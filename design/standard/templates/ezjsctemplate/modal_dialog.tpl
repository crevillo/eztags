<div class="jqmDialog parent-selector-tree" id="parent-selector-tree">
	<div class="jqmdIn">
		<div class="jqmdTC"><span class="jqmdTCLeft"></span><span class="jqDrag">{'Select tag'|i18n( 'extension/eztags/tags/treemenu' )}</span><span class="jqmdTCRight"></span></div>
		<div class="jqmdBL"><div class="jqmdBR"><div class="jqmdBC"><div class="jqmdBCIn">
			<div id="content-tree">
				<div id="contentstructure">
					{include uri='design:ezjsctemplate/tree_menu.tpl' menu_persistence=false() root_tag=cond(is_set($root_tag), $root_tag, false())}
				</div>
			</div>
		</div></div></div></div>
		<a href="#" class="jqmdX jqmClose"></a>
	</div>
</div>