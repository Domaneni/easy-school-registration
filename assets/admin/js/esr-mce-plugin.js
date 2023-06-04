(function () {
  var $settings = {};
  tinymce.PluginManager.add("esr_mce_button", function (editor, url) {
    jQuery.post(ajaxurl, {"action": "esr_tinymce_load_settings"}, function (response) {
      $settings = response;
    });
    editor.addButton("esr_mce_button", {
      text: "Waves",
      icon: "icon dashicons-welcome-learn-more",
      classes: "esr-tinymce-button",
      title: "Insert School Shortcode",
      onclick: function () {
        editor.windowManager.open({
          title: "Insert School Shortcode",
          classes: "bg-show-more",
          body: [
            {
              type: "listbox",
              name: "esrType",
              label: "Type",
              values: [
                {
                  text: "Course registration",
                  value: "esr_course_registration"
                },
                {
                  text: "Wave schedule",
                  value: "esr_wave_schedule"
                }]
            },
            {
              type: "listbox",
              name: "esrStyle",
              label: "Style",
              values: $settings.styles
            },
            {
              type: "listbox",
              name: "esrWavesIds",
              label: "Waves",
              values: $settings.waves
            },
            {
              type: "checkbox",
              name: "esrZoomEnabled",
              label: "Enable automatic zoom"
            },
            {
              type: "checkbox",
              name: "esrGroupFilterEnabled",
              label: "Enable group filter"
            },
            {
              type: "checkbox",
              name: "esrGroupFilterHide",
              label: "Hide not selected groups"
            },
            {
              type: "checkbox",
              name: "esrLevelFilterEnabled",
              label: "Enable level filter"
            },
            {
              type: "checkbox",
              name: "esrLevelFilterHide",
              label: "Hide not selected levels"
            },
            {
              type: "listbox",
              name: "esrSpecificGroup",
              label: "Show specific group",
              values: $settings.groups
            },
            {
              type: "listbox",
              name: "esrShowHover",
              label: "Show hover with limits",
              values: [
                {
                  "text": "Choose an option",
                  "value": ""
                }, {
                  "text": "Number of registrations",
                  "value": "registrations"
                }, {
                  "text": "Places left",
                  "value": "places_left"
                }
              ]
            }
          ],
          onsubmit: function (e) {
            let esr_shortcode_name = e.data.esrType;
            let esr_shortcode_options = [];
            esr_shortcode_options.push(`type="${e.data.esrStyle}"`);
            esr_shortcode_options.push(`waves="${e.data.esrWavesIds}"`);
            if (e.data.esrZoomEnabled) {
              esr_shortcode_options.push(`automatic_zoom="1"`);
            }
            if (e.data.esrGroupFilterEnabled) {
              esr_shortcode_options.push(`show_group_filter="1"`);
              if (e.data.esrGroupFilterHide) {
                esr_shortcode_options.push(`group_filter_hide_courses="1"`);
              }
            }
            if (e.data.esrLevelFilterEnabled) {
              esr_shortcode_options.push(`show_level_filter="1"`);
              if (e.data.esrLevelFilterHide) {
                esr_shortcode_options.push(`level_filter_hide_courses="1"`);
              }
            }
            if (e.data.esrShowHover !== "") {
              esr_shortcode_options.push(`hover_option="${e.data.esrShowHover}"`);
            }
            if (e.data.esrSpecificGroup !== "") {
              esr_shortcode_options.push(`filter_group="${e.data.esrSpecificGroup}"`);
            }
            editor.insertContent(`[${esr_shortcode_name + " " + esr_shortcode_options.join(" ")}]`);
          }
        });
      }
    });
  });
})();
