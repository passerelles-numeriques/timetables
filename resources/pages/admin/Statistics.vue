<template>
  <div class="container">

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Lesson: </label>
      <div class="col-sm-10">
        <select class="form-control" v-model="selected">
          <optgroup v-for="module in optionModules" v-bind:key="module.id" v-bind:label="module.name">
            <option v-for="lesson in module.lessons" v-bind:key="lesson.id" v-bind:value="lesson.id">
              {{ lesson.name }} with {{ lesson.teacher.name }}
            </option>
          </optgroup>
        </select>
      </div>
    </div>

    <div class="report" v-show="lessonSelected">
      <div class="row">
        <div class="col-6 table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col">Sessions information</th>
                <th scope="col">Value from the sessions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">Number of Sessions Done</th>
                <td>{{sessionsDone}}h</td>
              </tr>
              <tr>
                <th scope="row">Number of Sessions Planned</th>
                <td>{{sessionsPlanned}}h</td>
              </tr>
              <tr class="important-row">
                <th scope="row">Number of Sessions Done and Planned</th>
                <td>{{sessionsDonePlanned}}h</td>
              </tr>
              <tr>
                <th scope="row">Number of hour Left</th>
                <td :class="{'to-much-session': tooMuchSession}">{{sessionsLeft}}h</td>
              </tr>
              <tr class="important-row">
                <th scope="row">Total of hours</th>
                <td>{{totalSession}}h</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-6 chart-container">
          <canvas id="sessions-chart" ref="sessionsChart"></canvas>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div id="sessions-calendar" ref="sessionsCalendar"></div>
        </div>
      </div>
    </div>
  </div>

</template>
<script>
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

import Chart from 'chart.js';
import AdminService from '../../services/AdminService';
export default {
  name: 'Statistics',
  data() {
    return {
      selected: '',
      optionModules: [],
      sessionsChart: null,
      sessionValue: [],
      sessions: [],
      calendar: null,
      moduleForDate: null,
      adminSrv: new AdminService()
    };
  },
  created: function() {
    //Get the combined list of modules, lessons, and the teacher of the lesson
    this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/modules/combined`, (json) => {
        this.optionModules = json;
        if (this.optionModules.length == 0) {
          this.$emit('showErrorMessage', `You must create a <a href="${this.baseURL}admin/modules">module</a> first.`);
        }
        this.$emit('loader', false);
      });
  },
  computed: {
    sessionsDone() {
      return this.sessionValue[0];
    },
    sessionsPlanned() {
      return this.sessionValue[1];
    },
    sessionsDonePlanned() {
      return this.sessionValue[0] + this.sessionValue[1];
    },
    sessionsLeft() {
      return this.sessionValue[2] - this.sessionValue[0] - this.sessionValue[1];
    },
    totalSession() {
      return this.sessionValue[2];
    },
    lessonSelected() {
      return this.sessionValue[0] !== undefined && this.sessionValue[1] !== undefined && this.sessionValue[2] !== undefined;
    },
    tooMuchSession() {
      return this.sessionsLeft < 0;
    }
  },
  mounted() {
    this.$emit('changeActive', 'Statistics');
    this.$emit('loader', true);
    this.sessionsChart = new Chart(this.$refs.sessionsChart, {
      type: 'doughnut',
      data: {
        labels: ['Sessions Done', 'Sessions Planned', 'Session left'],
        datasets: [
          {
            label: 'Sessions',
            data: this.sessionValue,
            backgroundColor: [
              '#28a745', // green
              '#17a2b8', // blue
              '#dc3545' // red
            ]
          }
        ]
      }
    });
    this.calendar = new Calendar(this.$refs.sessionsCalendar, {
      plugins: [ dayGridPlugin ],
      defaultView: 'dayGridMonth',
      allDaySlot: false,
      nowIndicator: true,
      minTime: '07:00:00',
      maxTime: '20:00:00',
      hiddenDays: [0], // index of days 0=Sunday, 1=Monday example: [0,1,2,3,4,5,6]
      height: 'auto',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,listYear'
      },
      validRange: (nowDate) => {
        return {
          start: this.moduleForDate ? this.moduleForDate.startDate : '',
          end: this.moduleForDate ? this.moduleForDate.endDate : ''
        };
      }
    });
    this.calendar.render();
  },
  methods: {
    changeLesson(lesson) {
      this.$emit('loader', true);
      let url = `${this.baseURL}api/v1/admin/stats/events/${this.selected}`;
      // Get events lesson
      this.adminSrv.adminFetch(url, (json) => {
        let isCompleteLesson = true;
        if (json.noLesson === true) {
          this.$emit('showErrorMessage', 'This lesson doesn\'t exist');
          isCompleteLesson = false;
        }
        if (json.noModule === true) {
          this.$emit('showErrorMessage', 'No module for this lesson. You can add one by <a href="/admin/lessons" class="alert-link">clicking here</a>');
          isCompleteLesson = false;
        }
        if (json.noTeacher === true) {
          this.$emit('showErrorMessage', 'No teacher for this lesson. You can add one by <a href="/admin/lessons" class="alert-link">clicking here</a>');
          isCompleteLesson = false;
        }
        if (isCompleteLesson) {
          this.sessions = json;
          for (let i = 0; i < 3; i++) {
            this.sessionValue.pop();
          }
          let nbSessionsDone = 0;
          let nbSessionsPlanned = 0;
          let dateNow = moment().local().hours(23).minutes(59).seconds(59);
          this.sessions.forEach(session => {
            let endSession = moment(session.end).local();
            let startSession = moment(session.start).local();
            let diff = endSession.diff(startSession, 'hours');
            if (endSession.isBefore(dateNow)) {
              nbSessionsDone += diff;
              session.color = '#28a745';
            } else {
              nbSessionsPlanned += diff;
              session.color = '#17a2b8';
            }
          });
          this.sessionValue.push(nbSessionsDone);
          this.sessionValue.push(nbSessionsPlanned);
          this.sessionValue.push(lesson.hours);
        } else {
          this.sessionValue.splice(0, this.sessionValue.length);
        }
        this.$emit('loader', false);
      });
    },
    updateChart() {
      this.sessionsChart.data.datasets[0].data[0] = this.sessionsDone;
      this.sessionsChart.data.datasets[0].data[1] = this.sessionsPlanned;
      this.sessionsChart.data.datasets[0].data[2] = this.tooMuchSession ? 0 : this.sessionsLeft;
      this.sessionsChart.update();
    },
    updateCalendar() {
      this.calendar.removeAllEvents();
      this.calendar.removeAllEventSources();
      if (this.sessions.length > 0) {
        this.calendar.addEventSource(this.sessions);
        this.calendar.gotoDate(this.sessions[0].start);
      }
    },
    findLesson(lessonId) {
      //_this.optionModules[1].lessons[2]
      for (let k1 in this.optionModules) {
        for (let k2 in this.optionModules[k1].lessons) {
          if (this.optionModules[k1].lessons[k2].id == lessonId) {
            return this.optionModules[k1].lessons[k2];
          }
        }
      }
    },
    findModule(moduleId) {
      for (let k1 in this.optionModules) {
          if (this.optionModules[k1].id == moduleId) {
            return this.optionModules[k1];
          }
      }
    },
  },
  watch: {
    selected: function(newLessonID, oldLesson) {
      this.$emit('showErrorMessage', '');
      if (newLessonID !== '') {
        let lesson = this.findLesson(newLessonID);
        this.moduleForDate = this.findModule(lesson.module);
        this.calendar.render();
        this.changeLesson(lesson);
      }
    },
    sessionValue: function(newSessionValue, oldSessionValue) {
      this.updateChart();
      this.updateCalendar();
    }
  },
};
</script>

<style>
.report {
  margin-top: 1em;
}
.important-row {
  border: 2px solid #aab7c3;
}
.to-much-session {
  color: #dc3545;
  font-weight: bold;
}
</style>
