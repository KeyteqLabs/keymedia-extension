KeyMedia.views.Upload = Backbone.View.extend({

    maxSize : '25mb',
    headers : {
        'Accept' : 'application/json, text/javascript, */*; q=0.01'
    },

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'uploaded', 'added', 'progress');
        this.options = options;
        this.uploadCallback = options.uploaded;
        return this;
    },

    uploaded : function(up, file, info)
    {
        if (!('response' in info)) return true;

        var data = JSON.parse(info.response);
        if ('content' in data && 'image' in data.content)
        {
            var image = data.content.image;
            console.log(image);
            if (this.uploadCallback)
            {
                this.uploadCallback(image.id, image.host, image.scalesTo.ending);
            }

            if ('html' in data.content)
                this.$('.keymedia-image').replaceWith($(data.content.html));
        }

        this.$('.upload-progress').fadeOut();

        return this;
    },

    progress : function(up, file) {
        this.$('.progress').css('width', file.percent + '%');
    },

    added : function(up, files) {
        up.start();
        this.$('.upload-progress').show();
    },

    render : function(response) {
        var attrId = this.model.get('attributeId');
        this.uploader = new plupload.Uploader({
            runtimes : 'html5,html4',
            browse_button : 'keymedia-local-file-' + attrId,
            container : 'keymedia-local-file-container-' + attrId,
            max_file_size : this.maxSize,
            url : this.options.prefix + '/keymedia::upload',
            multipart_params : {
                'AttributeID' : attrId,
                'ContentObjectVersion' : this.options.version,
                'ContentObjectID' : this.options.objectId
            },
            headers : this.headers
        });
        this.uploader.init();
        this.uploader.bind('FileUploaded', this.uploaded);
        this.uploader.bind('UploadProgress', this.progress);
        this.uploader.bind('FilesAdded', this.added);
        return this;
    }
});
