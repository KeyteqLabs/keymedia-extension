var processName = function(filename)
{
    // Removes the path prefix + file ending (.handlebar)
    return 'keymedia/' + filename.split('/').pop().slice(0, -10);
};

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
                    wrapped: true
                },
                files: {
                    'design/ezexceed/javascript/templates.js': 'design/ezexceed/**/*.handlebar'
                }
            },
            standard: {
                options: {
                    processName: processName,
                    wrapped: true
                },
                files: {
                    'design/standard/javascript/templates.js': 'design/standard/**/*.handlebar'
                }
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
