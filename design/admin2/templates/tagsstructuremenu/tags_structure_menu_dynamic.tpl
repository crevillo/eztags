{if is_unset( $menu_persistence )}
    {def $menu_persistence = ezini('TreeMenu','MenuPersistence','eztags.ini')|eq('enabled')}
{/if}
<div id="tags-tree">
<ul>
    <li><a href="">Top Level</a>
    {def $tags = fetch( 'tags', 'list', hash( 'parent_tag_id', 0 ))}
    {if $tags|count|gt(0)}
    <ul>
    {foreach $tags as $tag}
        <li><a href={concat( '/tags/id/', $tag.id )|ezurl}>{$tag.keyword}</a></li>
    {/foreach}
    </ul>
    {/if}
    </li>
</ul>
</div>


{ezscript_require( array( 'ezjsc::jquery', 
                          'dynatree/jquery-ui.custom.min.js', 
                          'dynatree/jquery.cookie.js', 
                          'dynatree/jquery.dynatree.min.js' ) )}
{ezcss_require( array( 'dynatree/skin/ui.dynatree.css', 
                       'dynatree/eztags-dynatree.css' ))}
{literal}
<script type="text/javascript">
$(function(){
    $("#tags-tree").dynatree();
});
</script>
{/literal}
