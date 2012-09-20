var baseUrl = 'design/ezexceed/javascript/';
var handlebarsConf = {
    keymedia : {
        options : {
            processName : function(filename)
            {
                // Removes the path prefix + file ending (.handlebar)
                console.log(filename);
                return 'keymedia/' + filename.split('/').pop().slice(0, -10);
            },
            wrapped : true
        },
        files : {}
    }
};
handlebarsConf.keymedia.files[baseUrl + 'templates.js'] = baseUrl + '**/*.handlebar';

module.exports = function(grunt) {
    grunt.initConfig({
        lint: {
            all: [
                'grunt.js',
                baseUrl + '!(libs|plugins)/**/!(templates).js'
            ]
        },
        jshint: {
            options: {
                browser: true,
                curly: false
            }
        },

        handlebars: handlebarsConf,

        requirejs: {
            compile: {
                options: {
                    removeCombined: false,
                    out : baseUrl + 'compiled.js',
                    modules: [
                        {
                            name: 'keymedia'
                        }
                    ]
                }
            }
        },

        watch: {
            files: ['**/*.handlebar'],
            tasks: 'handlebars'
        }
    });

    grunt.loadNpmTasks('grunt-contrib');

    grunt.registerTask('default', 'lint handlebars');
};
