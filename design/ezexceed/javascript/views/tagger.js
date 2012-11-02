define(['shared/view'], function(View)
{
    return View.extend({

        initialize : function(options)
        {
            _.bindAll(this);
            this.collection = this.model.get('tags');
            this.collection.on('add remove', this.save, this);
            return this;
        },

        events : {
            'change input:text' : 'inputChange',
            'keyup input:text' : 'inputChange',
            'click button.tag' : 'add',
            'click .tags button.close' : 'remove'
        },

        keys : {
            'enter input:text' : 'add'
        },

        // Save any ad
        save : function()
        {
            this.trigger('save');
            this.model.save().success(this.saved);
        },

        saved : function()
        {
            this.trigger('saved');
            this.renderTags();
        },

        remove : function(e) {
            e.preventDefault();
            e.stopPropagation();
            var target = this.$(e.currentTarget);
            var tag = target.data('tag');
            this.collection.remove(this.collection.get(tag));
        },

        add : function(e) {
            e.preventDefault();
            e.stopPropagation();
            var tag = this.$input.val();
            this.collection.add({
                id : tag,
                tag : tag
            });
            this.$input.val('').focus();
        },

        inputChange : function(e) {
            var val = this.$input.val();
            this.$button.attr('disabled', val.length === 0);
            return this;
        },

        render : function(media) {
            this.$list = this.$('.tags');
            this.$input = this.$('input:text');
            this.$button = this.$('button.tag');
            this.renderTags();
            return this;
        },

        // Render tags
        renderTags : function() {
            var html = this.collection.map(function(tag)
            {
                return this.template('keymedia/tag', tag.toJSON());
            }, this);
            this.$('.tags').html(html);
        }
    });
});
