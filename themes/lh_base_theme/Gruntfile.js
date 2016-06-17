module.exports = function(grunt) {
  require('jit-grunt')(grunt);
  require('time-grunt')(grunt);
  grunt.template.addDelimiters('underscoresaving', '<##', '##>');
  grunt.template.setDelimiters('underscoresaving');

  grunt.initConfig({

  	// Define variables
    pkg:     grunt.file.readJSON("package.json"),

	// LESS / CSS

	// Compile Less
	// Compile the less files
    less: {
      development: {
        files: {
          "style.css": "less/style.less", // destination file and source file
          "editor-style.css": "less/editor-style.less",
          "admin/admin.css": "admin/less/admin.less"
        }
      }
    },

	postcss: {
		options: {
			map: false, // inline sourcemaps

			processors: [
				require('autoprefixer')({browsers: 'last 2 versions'}), // add vendor prefixes
				require('cssnano')
			]
		},
		dist: {
			files: {
				"style.css": "style.css", // destination file and source file
				"editor-style.css": "editor-style.css",
				"admin/admin.css": "admin/admin.css"
			}
		}
	},

    // JAVASCRIPT

    // Concat
    // Join together the needed files.
	concat_in_order: {
		main: {
			files: {
				'js/main.min.js': ['js/main.js'],
				'admin/admin.min.js': ['admin/admin.js']
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
	        'js/main.min.js': ['js/main.min.js'],
	        'admin/admin.min.js': ['admin/admin.min.js']
	      }
	    }
	},

	// WATCHER / SERVER

    // Watch
    watch: {
    	options: { interval: 5007 },

	    js: {
		    files: ['js/**/*.js', '!js/**/*.min.js', '!node_modules/**/*', '!bower_components/**/*'],
		    tasks: ['handle_js'],
			options: {
				livereload: true
			},
	    },
		less: {
			files: ['less/**/*.less'], // which files to watch
			tasks: ['handle_less'],
			options: {
				// livereload: true
			},
		},
		css: {
			files: ['**/*.css', '*.css', '!node_modules/**/*', '!bower_components/**/*'],
			tasks: [],
			options: {
				livereload: true
			}
		},
		livereload: {
			files: ['js/*.min.js', '**/*.php', '**/*.html', '!node_modules/**/*', '!bower_components/**/*'], // Watch all files
			options: {
				livereload: true
			}
		},
    }
  });

  grunt.registerTask( 'handle_js', ['concat_in_order', 'uglify'] );
  grunt.registerTask( 'handle_less', ['less', 'newer:postcss'] );
};

