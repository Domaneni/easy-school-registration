jQuery(function ($) {
  $(document).ready(function () {
    $("body").on("click", ".esr-student-download-button", function (e) {
      e.preventDefault();
      $.post(ajaxurl, {
        "action": "esr_ics_generate_student_calendar",
        "wave_id": $(this).data("wave-id")
      }, function (response) {
        if (!jQuery.isEmptyObject(response)) {
          var cal = ics("default", "Calendar", response.timezone);
          $.each(response.halls, function (key, waveData) {
            esr_add_hall_courses(cal, waveData);
          });
          cal.download("classes");
        }
      });
    });

    function esr_add_hall_courses(cal, waveData) {
      $.each(waveData.courses, function (key, course) {
        if ((course.from !== "") && (course.to !== "")) {
          cal.addEvent(course.title, course.id, course.title, waveData.hall, course.from, course.to, waveData.timezone, {
            freq: "WEEKLY",
            interval: 1,
            count: course.weeks
          });
        }
      });
    }
  });
});
