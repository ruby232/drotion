/**
 * @file
 * ban behaviors.
 */
(function (Drupal, once, $) {
  'use strict';
  Drupal.behaviors.ban_fullcalendar = {
    attach(context, settings) {
      const calendarId = 'workout-plan-calendar';

      $(once('workout-plan-calendar-drupal-modal', '#drupal-modal')).on('dialogclose', function(event) {
        loadEvents();
      });

      once('workout-plan-calendar', '#workout-plan-calendar', context).forEach(function () {

        const calendarEl = document.getElementById(calendarId);
        // Get events from settings drupal drotion_node_workout_plan

        const workout_plan_id = settings.drotion_node_workout_plan.workout_plan_id;
        Drupal.behaviors.ban_fullcalendar.calendar = new FullCalendar.Calendar(calendarEl, {
          events: [],
          initialView: 'dayGridMonth',
          eventClick: function (info) {
            const id = info.event.id;
            const workout_plan_id = info.event.extendedProps.workout_plan_id;
            const ajaxSettings = {
              url: `/paragraphs_edit/node/${workout_plan_id}/paragraphs/${id}/edit?_wrapper_format=drupal_modal`,
              dialogType: 'modal',
              dialog: { width: 800 },
            };
            const ajaxObject = Drupal.ajax(ajaxSettings);
            ajaxObject.execute();
          },
          dateClick: function (info) {
            const events = Drupal.behaviors.ban_fullcalendar.events;
            const date = info.dateStr;
            // check if there is event in this date
            const event = events.find(event => event.start === date);
            if (event) {
              return;
            }

            const url = `/paragraphs_add/node/${workout_plan_id}/paragraphs/field_sessions/workout_plan_day/add?edit[field_day][widget][0][value][date]=${date}&_wrapper_format=drupal_modal`;
            const ajaxSettings = {
              url: url,
              dialogType: 'modal',
              title: '',
              dialog: { width: 800 },
            };
            const ajaxObject = Drupal.ajax(ajaxSettings);
            ajaxObject.execute();
          },
        });
        Drupal.behaviors.ban_fullcalendar.calendar.render();
        loadEvents();
      });

      function processEventResponse(response) {
        let events = [];
        const sessions = response.sessions;
        const defaultEvent = { display: 'background' };

        sessions.forEach(function (session) {
          const done = session.done == 1;
          let color = 'orange';
          // If the session is not done and date is in the past change color to red
          // If the session is done and date is in the past change color to green
          // If the session in the future change color to blue
          if (new Date(session.start) < new Date()) {
            color = done ? 'green' : 'red';
          }
          events.push({
            color,
            ...session,
            ...defaultEvent,
          });
        });
        Drupal.behaviors.ban_fullcalendar.events = events;
        Drupal.behaviors.ban_fullcalendar.calendar.removeAllEvents();
        Drupal.behaviors.ban_fullcalendar.calendar.addEventSource(events);
      }

      function loadEvents() {
        const workout_plan_id = settings.drotion_node_workout_plan.workout_plan_id
        const url = `/api/v1/workout_plan/sessions/${workout_plan_id}`;
        $.get(url, null, processEventResponse);
      }

    },
  };

}(Drupal, once, jQuery));
