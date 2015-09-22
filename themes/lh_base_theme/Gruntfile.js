module.exports = function(grunt) {
  require('jit-grunt')(grunt);
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
        options: {
          compress: true,
          yuicompress: true,
          optimization: 2,
          process: function(content, path) {
          	return grunt.template.process(content);
          }
        },
        files: {
          "style.css": "less/style.less", // destination file and source file
          "editor-style.css": "less/editor-style.less"
        }
      }
    },

	postcss: {
		options: {
			map: true, // inline sourcemaps

			processors: [
				require('autoprefixer')({browsers: 'last 2 versions'}), // add vendor prefixes
				require('cssnano')() // minify the result
			]
		},
		dist: {
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

	// Copy
	// Copy files from the vendor folder to need places elsewhere
	copy: {
		main: {
			files: [
				{expand: true, flatten: true, src: ['node_modules/**/*.eot', 'node_modules/**/*.ttf', 'node_modules/**/*.woff', 'node_modules/**/*.woff2'], dest: 'fonts/', filter: 'isFile'},
				// Copy all found font files from the vendor folder to the fonts folder
			]
		}
	},

	// WATCHER / SERVER

    // Watch
    watch: {
	    js: {
		    files: ['js/**/*.js', '!node_modules/**/*', 'bower_components/**/*'],
		    tasks: ['handle_js'],
			options: {
				livereload: true
			},
	    },
		less: {
			files: ['less/**/*.less'], // which files to watch
			tasks: ['less', 'postcss'],
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
		vendor: {
			files: ['node_modules/**/*'],
			task: ['copy']
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

