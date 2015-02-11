module.exports = function(grunt) {
  require('jit-grunt')(grunt);

  grunt.initConfig({

	// LESS / CSS

	// Compile Less
	// Compile the less files
    less: {
      development: {
        options: {
          compress: true,
          yuicompress: true,
          optimization: 2
        },
        files: {
          "style.css": "less/style.less", // destination file and source file
          "editor-style.css": "less/editor-style.less"
        }
      }
    },

    // JAVASCRIPT

    // JS HINT
    // How's our code quality
    jshint: {
	    options: {
			reporter: require('jshint-stylish'),
			force: true,
	    },
    	all: ['js/**/*.js', '!js/**/*.min.js', '!js/bootstrap/**/*.js', '!js/vendor/**/*.js']
  	},

    // Concat
    // Join together the needed files.
	concat_in_order: {
		main: {
			files: {
				'js/mstartup.ng.min.js': ['js/mstartup.ng.js'],
				'js/main.min.js': ['js/main.js']
			},
			options: {
			    extractRequired: function(filepath, filecontent) {
				    var path = require('path');

			        var workingdir = path.normalize(filepath).split(path.sep);
			        workingdir.pop();

			        var deps = this.getMatches(/@depend\s"(.*\.js)"/g, filecontent);
			        deps.forEach(function(dep, i) {
			            var dependency = workingdir.concat([dep]);
			            deps[i] = path.join.apply(null, dependency);
			        });
			        return deps;
			    },
			    extractDeclared: function(filepath) {
			        return [filepath];
			    },
			    onlyConcatRequiredFiles: true
			}
		}
	},

	// Uglify
	// We minify the files, we just concatenated
	uglify: {
	    mstartup: {
	      options: {
	      },
	      files: {
	        'js/mstartup.ng.min.js': ['js/mstartup.ng.min.js'],
	        'js/main.min.js': ['js/main.min.js']
	      }
	    }
	},

	// WATCHER / SERVER

    // Watch
    watch: {
	    js: {
		    files: ['js/**/*.js'],
		    tasks: ['handle_js'],
			options: {
				livereload: true
			},
	    },
		less: {
			files: ['less/**/*.less'], // which files to watch
			tasks: ['less'],
			options: {
				// livereload: true
			},
		},
		css: {
			files: ['**/*.css', '*.css', ],
			tasks: [],
			options: {
				livereload: true
			}
		},
		livereload: {
			files: ['js/*.min.js', '**/*.php', '**/*.html'], // Watch all files
			options: {
				livereload: true
			}
		},
    }
  });

  grunt.registerTask( 'handle_js', ['concat_in_order', 'uglify'] );

};

