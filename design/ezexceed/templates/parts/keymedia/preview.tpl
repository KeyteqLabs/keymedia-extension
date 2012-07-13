{if not( is_set( $handler ) ) }
    {def $handler = $attribute.content}
{/if}
{if not( is_set( $media ) ) }
    {def $media = $handler.media}
{/if}

{if $media}
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
        
        {include uri="design:parts/overlay_action_button.tpl"
            media=$media handler=$handler}

        {attribute_view_gui format=array(200,200) attribute=$attribute fetchinfo=true()}
    </div>
{/if}
