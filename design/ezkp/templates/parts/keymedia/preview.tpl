{if not( is_set( $handler ) ) }
    {def $handler = $attribute.content}
{/if}
{if not( is_set( $image ) ) }
    {def $image = $handler.image}
{/if}

{if $hasImage}
    <div class="image-wrap">
        {if eq($attribute.content.id, 0)|not}
        <div class="remove-wrap">
            <span class="kp-icon16 remove-black"></span>
            <input class="button remove"
                   type="submit"
                   name="CustomActionButton[{$attribute.id}_delete_image]"
                   value="{'Remove current image'|i18n( 'content/edit' )}"/>
        </div>
        {/if}
        
        <button type="button" class="scale action"
            {if not( $handler.imageFits )}disabled="disabled"{/if}
            data-truesize='{$image.size|json}'
            data-versions='{$handler.toscale|json}'>
    
            {if $handler.imageFits}
            {'Scale variants'|i18n( 'content/edit' )}
            {else}
            {'Requires a bigger image'|i18n( 'content/edit' )}
            {/if}
        </button>
        
        {attribute_view_gui format=array(200,200) attribute=$attribute}
    </div>
{/if}
