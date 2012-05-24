KeyMedia.views.Upload = Backbone.View.extend({

    browseButton : null,
    browseContainer : null,
    maxSize : '25mb',

    headers : {
        'Accept' : 'application/json, text/javascript, */*; q=0.01'
    },

    initialize : function(options)
    {
        _.bindAll(this, 'render', 'uploaded', 'added', 'progress');
        this.browseButton = 'keymedia-local-file-' + this.model.id;
        this.browseContainer = 'keymedia-local-file-container-' + this.model.id;
        this.options = options;
        this.uploadCallback = options.uploaded;
        return this;
    },

    url : function()
    {
        return this.options.prefix + '/keymedia::upload';
    },

    uploaded : function(up, file, info)
    {
        if (!('response' in info)) return true;

        var data = JSON.parse(info.response);
        if ('content' in data && 'image' in data.content)
        {
            var image = data.content.image;
            if (this.uploadCallback)
            {
                this.uploadCallback(image.id, image.host, image.scalesTo.ending);
            }

            /* Commenting this out might break image update shizzle in standard design
            if ('html' in data.content)
                this.$('.keymedia-image').replaceWith($(data.content.html));
            */
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
        var button = this.$('#' + this.browseButton),
            text = button.val() + ' (Max ' + this.maxSize + ')';
        button.val(text);

        var settings = {
            runtimes : 'html5,html4',
            container : this.browseContainer,
            browse_button : this.browseButton,
            max_file_size : this.maxSize,
            url : this.url(),
            multipart_params : {
                'AttributeID' : this.model.id,
                'ContentObjectVersion' : this.options.version,
                'http_accept' : 'json' //Because of some strange failing when html4 is used
            },
            headers : this.headers
        };

        if ($('#ezxform_token_js').length)
        {
            /**
             * Ugly hack to go with ezformtoken
             */
            settings.multipart_params.ezxform_token = $('#ezxform_token_js').attr('title');
        }
        this.uploader = new plupload.Uploader(settings);
        this.uploader.init();
        this.uploader.bind('FileUploaded', this.uploaded);
        this.uploader.bind('UploadProgress', this.progress);
        this.uploader.bind('FilesAdded', this.added);
        return this;
    }
});
