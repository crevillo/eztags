        {if is_unset( $menu_persistence )}
    {def $menu_persistence = ezini('TreeMenu','MenuPersistence','eztags.ini')|eq('enabled')}
{/if}
<div id="tags-tree">
{def $tags = fetch( 'tags', 'treemenu', hash() )}

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
    $("#tags-tree").dynatree({
        persist: true,
        minExpandLevel: 2,
        children: {/literal}{$tags}{literal},
        imagePath: '/',
        icon: null,
        clickFolderMode: 1,
        onActivate: function(node) { 
             if( node.data.href ) {
                window.location.href = node.data.href; 
             }
        }
    });

});
</script>
{/literal}
