/* jshint node:true */
module.exports = function (grunt) {
	'use strict';

	// load all tasks
	require('load-grunt-tasks')(grunt, { scope: 'devDependencies' });

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		// setting folder templates
		dirs: {
			dist: 'dist',
			src: 'src',
			lang: 'languages',
			templates: 'templates',
			vendor: 'vendor',
			thirds: 'thirds',
		},

		// Check the text domain
		checktextdomain: {
			standard: {
				options: {
					text_domain: ['imageseo'], //Specify allowed domain(s)
					create_report_file: 'true',
					keywords: [ //List keyword specifications
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d',
						'esc_attr_e:1,2d',
						'esc_attr_x:1,2c,3d',
						'_ex:1,2c,3d',
						'_n:1,2,4d',
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				files: [
					{
						src: [
							'**/*.php',
							'!**/node_modules/**',
						], //all php
						expand: true
					}
				]
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'languages',
				potHeaders: {
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			frontend: {
				options: {
					potFilename: 'imageseo.pot',
					exclude: [
						'node_modules/.*',
						'tests/.*',
						'tmp/.*'
					],
					processPot: function (pot) {
						return pot;
					}
				}
			}
		},

		po2mo: {
			files: {
				src: '<%= dirs.lang %>/*.po',
				expand: true
			}
		},

		clean: {
			init: {
				src: ['build/']
			},
		},
		copy: {
			build: {
				expand: true,
				src: [
					'**',
					'!node_modules/**',
					'!bin/**',
					'!.vscode/**',
					'!build/**',
					'!app/**',
					'!appv2/**',
					'!bin/**',
					'!tests/**',
					'!readme.md',
					'!README.md',
					'!phpcs.ruleset.xml',
					'!package-lock.json',
					'!Gruntfile.js',
					'!package.json',
					'!composer.json',
					'!composer.lock',
					'!postcss.config.js',
					'!webpack.config.js',
					'!*.zip',
					'!codeception.dist.yml',
					'!regconfig.json',
					'!SECURITY.md',
					'!tailwind.config.js',
					'!phpunit.xml',
					'!composer.phar',
					'!tsconfig.json',
					'!travis.yml',
					'!phpunit.xml.dist',
					'!friendsofphp',
					'!squizlabs',
					'!bin',
					'doctrine',
					'!php-cs-fixer',
				],
				dest: 'build/'
			}
		},
		compress: {
			build: {
				options: {
					pretty: true,                           // Pretty print file sizes when logging.
					archive: '<%= pkg.name %>-<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: '',
				src: [
					'**',
					'!node_modules/**',
					'!bin/**',
					'!.vscode/**',
					'!build/**',
					'!app/**',
					'!appv2/**',
					'!bin/**',
					'!tests/**',
					'!readme.md',
					'!README.md',
					'!phpcs.ruleset.xml',
					'!package-lock.json',
					'!Gruntfile.js',
					'!package.json',
					'!composer.json',
					'!composer.lock',
					'!postcss.config.js',
					'!webpack.config.js',
					'!*.zip',
					'!codeception.dist.yml',
					'!regconfig.json',
					'!SECURITY.md',
					'!tailwind.config.js',
					'!phpunit.xml',
					'!composer.phar',
					'!tsconfig.json',
					'!travis.yml',
					'!phpunit.xml.dist',
					'!friendsofphp',
					'!squizlabs',
					'!bin',
					'doctrine',
					'!php-cs-fixer',
				],
				dest: '<%= pkg.name %>'
			}
		},

	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-checktextdomain');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-compress');
	// Just an alias for pot file generation
	grunt.registerTask('pot', [
		'makepot'
	]);

	// Build task
	grunt.registerTask('build-archive', [
		'clean',
		'copy',
		'compress:build',
		'clean'
	]);

	grunt.registerTask('makemo', ['po2mo']);

};
