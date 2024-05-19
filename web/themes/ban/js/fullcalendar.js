/**
 * @file
 * ban behaviors.
 */
(function (Drupal, once, $) {
  'use strict';
  Drupal.behaviors.ban_fullcalendar = {
    attach(context, settings) {

      once('workout-plan-calendar', '#workout-plan-calendar', context).forEach(function () {

        const calendarEl = document.getElementById('workout-plan-calendar');
        // Get events from settings drupal drotion_node_workout_plan

        const workout_plan_id = settings.drotion_node_workout_plan.workout_plan_id;
        const events = getEvents(workout_plan_id);
        const calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          events: events,
          eventClick: function (info) {
            const id = info.event.id;
            const workout_plan_id = info.event.extendedProps.workout_plan_id;
            const url = `/workout-plan/${workout_plan_id}/session/${id}`;

            const ajaxSettings = {
              url: `/paragraphs_edit/node/${workout_plan_id}/paragraphs/${id}/edit?_wrapper_format=drupal_modal`,
              dialogType: 'modal',
              title: '',
              dialog: { width: 800 },
            };
            const ajaxObject = Drupal.ajax(ajaxSettings);
            ajaxObject.execute();

          },
        });
        calendar.render();
      });

      function getEvents(workout_plan_id) {
        const sessions = settings.drotion_node_workout_plan.sessions;
        let events = [];
        const defaultEvent = {
          display: 'background',
          workout_plan_id: workout_plan_id,
        };

        for (let i = 0; i < sessions.length; i++) {
          const title = sessions[i].title;
          const start = sessions[i].date;
          const id = sessions[i].id;

          const done = sessions[i].done == 1;
          let color = 'orange';
          // If the session is not done and date is in the past change color to red
          // If the session is done and date is in the past change color to green
          // If the session in the future change color to blue
          if (new Date(start) < new Date()) {
            color = done ? 'green' : 'red';
          }
          events.push({
            id,
            title,
            start,
            color,
            ...defaultEvent,
          });
        }
        return events;
      }

    },
  };

}(Drupal, once, jQuery));
