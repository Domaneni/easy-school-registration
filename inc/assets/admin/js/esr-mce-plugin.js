!function(e){var t={};function r(o){if(t[o])return t[o].exports;var l=t[o]={i:o,l:!1,exports:{}};return e[o].call(l.exports,l,l.exports,r),l.l=!0,l.exports}r.m=e,r.c=t,r.d=function(e,t,o){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(r.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var l in e)r.d(o,l,function(t){return e[t]}.bind(null,l));return o},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="",r(r.s=11)}({11:function(e,t){var r;r={},tinymce.PluginManager.add("esr_mce_button",(function(e,t){jQuery.post(ajaxurl,{action:"esr_tinymce_load_settings"},(function(e){r=e})),e.addButton("esr_mce_button",{text:"Waves",icon:"icon dashicons-welcome-learn-more",classes:"esr-tinymce-button",title:"Insert School Shortcode",onclick:function(){e.windowManager.open({title:"Insert School Shortcode",classes:"bg-show-more",body:[{type:"listbox",name:"esrType",label:"Type",values:[{text:"Course registration",value:"esr_course_registration"},{text:"Wave schedule",value:"esr_wave_schedule"}]},{type:"listbox",name:"esrStyle",label:"Style",values:r.styles},{type:"listbox",name:"esrWavesIds",label:"Waves",values:r.waves},{type:"checkbox",name:"esrZoomEnabled",label:"Enable automatic zoom"},{type:"checkbox",name:"esrGroupFilterEnabled",label:"Enable group filter"},{type:"checkbox",name:"esrGroupFilterHide",label:"Hide not selected groups"},{type:"checkbox",name:"esrLevelFilterEnabled",label:"Enable level filter"},{type:"checkbox",name:"esrLevelFilterHide",label:"Hide not selected levels"},{type:"listbox",name:"esrSpecificGroup",label:"Show specific group",values:r.groups},{type:"listbox",name:"esrShowHover",label:"Show hover with limits",values:[{text:"Choose an option",value:""},{text:"Number of registrations",value:"registrations"},{text:"Places left",value:"places_left"}]}],onsubmit:function(t){let r=t.data.esrType,o=[];o.push(`type="${t.data.esrStyle}"`),o.push(`waves="${t.data.esrWavesIds}"`),t.data.esrZoomEnabled&&o.push('automatic_zoom="1"'),t.data.esrGroupFilterEnabled&&(o.push('show_group_filter="1"'),t.data.esrGroupFilterHide&&o.push('group_filter_hide_courses="1"')),t.data.esrLevelFilterEnabled&&(o.push('show_level_filter="1"'),t.data.esrLevelFilterHide&&o.push('level_filter_hide_courses="1"')),""!==t.data.esrShowHover&&o.push(`hover_option="${t.data.esrShowHover}"`),""!==t.data.esrSpecificGroup&&o.push(`filter_group="${t.data.esrSpecificGroup}"`),e.insertContent(`[${r+" "+o.join(" ")}]`)}})}})}))}});