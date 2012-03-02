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
    {attribute_view_gui format=array(200,200) attribute=$attribute}
</div>
