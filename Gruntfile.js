const sass = require('node-sass');

module.exports = function (grunt) {
  grunt.initConfig({
    sass: {
      options: {
        implementation: sass,
        sourceMap: false
      },
      dist: {
        files: {
          "./inc/assets/admin/css/esr-admin-settings.css": "./assets/admin/scss/esr-admin-settings.scss",
          "./inc/assets/admin/css/esr-student-admin.css": "./assets/admin/scss/esr-student-admin.scss",
          "./inc/assets/admin/css/esr-menu-separator.css": "./assets/admin/scss/esr-menu-separator.scss",
          "./inc/assets/admin/css/esr-tinymce.css": "./assets/admin/scss/esr-tinymce.scss",
          "./inc/assets/web/css/esr-style.css": "./assets/web/scss/esr-style.scss"
        }
      }
    },
    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: "./inc/assets/admin/css/",
          src: ["*.css", "!*.min.css"],
          dest: "./inc/assets/admin/css/",
          ext: ".min.css"
        },{
          expand: true,
          cwd: "./inc/assets/web/css/",
          src: ["*.css", "!*.min.css"],
          dest: "./inc/assets/web/css/",
          ext: ".min.css"
        }]
      }
    },
    concat: {
      js: {
        src: ["./assets/admin/js/esr-admin.js"],
        dest: "./inc/assets/admin/js/esr-production.js"
      },
      courseAdmin: {
        src: ["./assets/admin/js/course/esr-course-admin.js", "./assets/admin/js/other/esr-datatable.js"],
        dest: "./inc/assets/admin/js/esr-course-admin.js"
      },
      waveAdmin: {
        src: ["./assets/admin/js/wave/esr-wave-admin.js", "./assets/admin/js/other/esr-datatable.js"],
        dest: "./inc/assets/admin/js/esr-wave-admin.js"
      },
      teacherAdmin: {
        src: ["./assets/admin/js/teacher/esr-teacher-admin.js", "./assets/admin/js/other/esr-datatable.js"],
        dest: "./inc/assets/admin/js/esr-teacher-admin.js"
      },
      dataChanging: {
        src: ["./assets/admin/js/esr-data-changing.js"],
        dest: "./inc/assets/admin/js/esr-data-changing.js"
      },
      students: {
        src: ["./assets/admin/js/students/esr-students-admin.js"],
        dest: "./inc/assets/admin/js/esr-students.js"
      },
      studentsAdmin: {
        src: ["./assets/admin/js/esr-student-admin.js"],
        dest: "./inc/assets/admin/js/esr-student-admin.js"
      },
      tinyMCE: {
        src: ["./assets/admin/js/esr-mce-plugin.js"],
        dest: "./inc/assets/admin/js/esr-mce-plugin.js"
      }
    }
  });

  grunt.loadNpmTasks("grunt-sass");
  grunt.loadNpmTasks("grunt-contrib-cssmin");
 // grunt.loadNpmTasks("grunt-minified");
  grunt.loadNpmTasks("grunt-contrib-concat");

  grunt.registerTask("default", ["concat", "sass", "cssmin"/*, "minified"*/]);

};