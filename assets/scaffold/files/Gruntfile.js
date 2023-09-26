'use strict';

module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    //grunt.loadNpmTasks('grunt-remove');
    require('grunt-remove')(grunt);

    // Define the configuration for all the tasks
    grunt.initConfig({

        // autoborna assets dir path
        autoborna: {
            // configurable paths
            bundleAssets: 'app/bundles/**/Assets/css',
            pluginAssets: 'plugins/**/Assets/css',
            rootAssets: 'media/css'
        },

        // Watches files for changes and runs tasks based on the changed files
        watch: {
            less: {
                files: ['<%= autoborna.bundleAssets %>/**/*.less', '<%= autoborna.bundleAssets %>/../builder/*.less'],
                tasks: ['less']
            }
        },

        // Compiles less files in bundle's Assets/css root and single level directory to CSS
        less: {
            files: {
                src: ['<%= autoborna.bundleAssets %>/*.less', '<%= autoborna.pluginAssets %>/*.less', '<%= autoborna.bundleAssets %>/*/*.less', '<%= autoborna.bundleAssets %>/../builder/*.less'],
                expand: true,
                rename: function (dest, src) {
                    return dest + src.replace('.less', '.css')
                },
                dest: ''
            },
            options: {
                javascriptEnabled: true
            }
        },

        // Remove prod's css files to force recompilation
        remove: {
            default_options: {
                trace: true,
                fileList: ['<%= autoborna.rootAssets %>/app.css', '<%= autoborna.rootAssets %>/libraries.css'],
                tasks: ['remove'],
                dest: ''
            }
        }
    });

    grunt.registerTask('compile-less', [
        'less',
        'watch'
    ]);
};
