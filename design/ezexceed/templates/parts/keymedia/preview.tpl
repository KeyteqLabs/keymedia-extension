{if not( is_set( $handler ) ) }
    {def $handler = $attribute.content}
{/if}
{if not( is_set( $media ) ) }
    {def $media = $handler.media}
{/if}

{if $hasMedia}
    <div class="image-wrap">
        {if eq($attribute.content.id, 0)|not}
        <div class="remove-wrap">
            <span class="kp-icon16 remove-black"></span>
            <input class="button remove"
                   type="submit"
                   name="CustomActionButton[{$attribute.id}_delete_media]"
                   value="{'Remove current media'|i18n( 'content/edit' )}"/>
        </div>
        {/if}
        
        <button type="button" class="scale action"
            {if not( $handler.mediaFits )}disabled="disabled"{/if}
            data-truesize='{$media.size|json}'
            data-versions='{$handler.toscale|json}'>
    
            {if $handler.mediaFits}
            {'Scale variants'|i18n( 'content/edit' )}
            {else}
            {'Requires a bigger media'|i18n( 'content/edit' )}
            {/if}
        </button>
        
        {attribute_view_gui format=array(200,200) attribute=$attribute fetchinfo=true()}
    </div>
{/if}
