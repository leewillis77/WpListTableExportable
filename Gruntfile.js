module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		less: {
			development: {
				options: {
					paths: ["css"],
					compress: true,
				},
				files: {
					"css/wlte-admin.css": "less/wlte-admin.less"
				}
			}
		},
		watch: {
			css: {
				files: [
					'less/wlte-admin.less'
				],
				tasks: [ 'less' ],
			},
		}
	});

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['less']);

};