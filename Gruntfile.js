var _ = require('lodash');

var processName = function(filename)
{
    // Removes the path prefix + file ending (.handlebar)
    return filename.split('/').pop().slice(0, -10);
};

var base = 'design/ezexceed/javascript/templates/';
var templateNames = ['alert', 'browser', 'item', 'nohits', 'scaler', 'scaledversion', 'scalerattributes', 'tag'];
var templates = {};
_.each(templateNames, function(name) {
    templates[base + name + '.js'] = base + name + '.handlebar';
});

module.exports = function(grunt) {
    grunt.initConfig({
        jshint: {
            all: [
                'grunt.js',
                'design/standard/javascript/keymedia/*js',
                'design/ezexceed/javascript/views/*js',
                'design/ezexceed/javascript/(config|models|keymedia).js'
            ],
            options: {
                browser: true,
                curly: false
            }
        },

        handlebars: {
            exceed: {
                options: {
                    processName: processName,
                    wrapped: true,
                    namespace: false,
                    amd: true
                },
                files: templates
            }
        },

        watch: {
            files: ['**/*.handlebar'],
            tasks: 'handlebars'
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-handlebars');

    grunt.registerTask('default', ['jshint', 'handlebars']);
};
