wp.blocks.registerBlockType('esr/course-basket-button', {
  title: 'ESR Course Basket Button',
  icon: {
    //background: '#3c5d7c',
    foreground: '#e27980',
    src: 'welcome-learn-more',
  },
  category: 'common',
  attributes: {
    course_id: {type: 'integer'},
    wave_id: {type: 'integer'},
  },

  /* This configures how the content and color fields will work, and sets up the necessary elements */

  edit: function(props) {
    function updateCourseId(event) {
      props.setAttributes({course_id: event.target.value})
    }
    function updateCourses(event) {
      props.setAttributes({wave_id: event.target.value})
      esr_load_wave_courses(event.target.value);
    }
    return React.createElement(
      "div",
      null,
      React.createElement(
        "h3",
        null,
        "ESR Course Basket Button"
      ),
      React.createElement(
        "h4",
        null,
        "Select course wave"
      ),
      React.createElement("select", { id: 'esr_wave', onChange: updateCourses }, esr_load_waves()),
      React.createElement(
        "h4",
        null,
        "Select course"
      ),
      React.createElement("select", { id: 'esr_course', onChange: updateCourseId }, React.createElement("option", {value: 0}, 'Select course')),
    );
  },
  save: function(props) {
    return wp.element.createElement(
      "",
      { },
      "[esr_course_button course=\"" + props.attributes.course_id + "\"]"
    );
  },
});

function esr_load_waves() {
  var opts = [];
  opts[0] = React.createElement(
    "option",
    {value: 0},
    "Select wave"
  );
  var i = 1;
  $.each(esr_globals.waves, function (key, wave) {
    opts[i] = React.createElement(
      "option",
      {value: wave.id},
      wave.title
    );
    i = i + 1;
  });

  return opts;
}

function esr_load_wave_courses(wave_id) {
  $.post(ajaxurl, {
    "action": "esr_load_wave_courses",
    "wave_id": wave_id,
  }, function (response) {
    if (!jQuery.isEmptyObject(response)) {
      $.each(response, function (key, course) {
         $("#esr_course").append("<option value='" + course.id + "'>" + course.title + "</option>");
      })
    }
  });
}