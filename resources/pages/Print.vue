<template>
  <div class="container">
    <div class="no-print">
      <button class="btn btn-primary" id="previous" @click="prevStep()" :disabled="calendars.length == 0"><chevronLeft-icon /> Previous</button>
      <button class="btn btn-primary" id="today" @click="goToToday()" :disabled="calendars.length == 0">Today</button>
      <button class="btn btn-primary" id="next" @click="nextStep()" :disabled="calendars.length == 0">Next <chevronRight-icon /></button>
      <button class="btn btn-primary" id="today" @click="print()"><printer-icon /> Print</button>
      <span>&nbsp;&nbsp;</span>
      <timerSand-icon v-if="iconLoad === 'loading'" />
      <check-icon v-if="iconLoad === 'ok'"  />
      <cancel-icon v-if="iconLoad === 'problem'"  />
      <i class="mdi" :class="iconLoad"></i>
      <span id="counter">{{counter}}</span>
    </div>
    <div class="page" id="timetables">
      <div class="page-break" v-for="(calendarData, index) in calendarsData" :key="index">
        <h1 class="break">{{calendarData.name}}</h1>
        <div :id="calendarData.selector" :ref="calendarData.selector"></div>
      </div>
    </div>
  </div>
</template>

<script>
import { Calendar } from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';

import TimerSandIcon from 'vue-material-design-icons/TimerSand.vue';
import CancelIcon from 'vue-material-design-icons/Cancel.vue';
import PrinterIcon from 'vue-material-design-icons/Printer.vue';
import CheckIcon from 'vue-material-design-icons/Check.vue';
import ChevronLeftIcon from 'vue-material-design-icons/ChevronLeft.vue';
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue';

export default {
  name: 'Print',
  data() {
    return {
      calendarDataList: null,
      calendarsGoogleId: null,
      counter: 'Loading...',
      calendars: [],
      iconLoad: 'loading',
      calendarsData: [],
      firstTime: true,
      printSelected: '',
      search: 'classes',
    };
  },
  components: {
    TimerSandIcon,
    CancelIcon,
    PrinterIcon,
    CheckIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
  },
  created: function() {
    this.printSelected = 'Classes';
    this.search = 'classes';
    switch (this.$route.params.type) {
      case 'rooms':
        this.printSelected = 'Rooms';
        this.search = 'rooms';
        break;
      case 'classes':
        this.printSelected = 'Classes';
        this.search = 'classes';
        break;
      case 'teachers':
        this.printSelected = 'Teachers';
        this.search = 'teachers';
        break;
    }
    this.$emit('changeActive', 'Printable');
    this.$emit('loader', true);
    this.$emit('showErrorMessage', '');
    this.iconLoad = 'loading';
    this.loadCalendarId();
  },
  updated: function() {
    if (this.firstTime) {
      this.calendarsData.forEach(ressource => {
        this.createTimeTableWithRessource(
          ressource.googleId,
          ressource.selector
        );
      });
      this.firstTime = false;
    }
  },
  methods: {
    loadCalendarId: function() {
      fetch(`${this.baseURL}api/v1/calendar/${this.search}`, {
        mode: 'cors'
      })
        .then(request => request.json())
        .then(data => {
          this.calendarsGoogleId = data;
          let i = 1;
          this.calendarsGoogleId.forEach(ressource => {
            this.calendarsData.push({
              name: ressource.name,
              googleId: ressource.google_id,
              selector: 'timetable-' + i
            });
            i++;
          });
          if (i == 1) {   //if no calendar
            this.counter = 'no calendar';
            this.iconLoad = 'problem';
            this.$emit('loader', false);
          }
        })
        .catch(err => {
          console.error('error requesting classes', err);
        });
    },
    createTimeTableWithRessource: function(googleId, calendarId) {
      let calendar = new Calendar(this.$refs[calendarId][0], {
        plugins: [ timeGridPlugin ],
        defaultView: 'timeGridWeek',
        minTime: '7:00:00',
        maxTime: '20:00:00',
        firstDay: 1,
        weekNumbers: true,
        header: {
          left: '',
          center: '',
          right: ''
        },
        selectable: false,
        selectHelper: false,
        editable: false,
        allDaySlot: false,
        events: (fetchInfo, successCallback, failureCallback) => {
          let events = [];
          const startTmp = (fetchInfo.start.getTime()/1000).toFixed(0);
          const endTmp = (fetchInfo.end.getTime()/1000).toFixed(0);
          fetch(
            `${this.baseURL}api/v1/google/events/${googleId}?start=${startTmp}&end=${endTmp}&showClasses=false`,
            { mode: 'cors' }
          )
            .then(response => response.json())
            .then(json => {
              events = json;
              //Sort the events by their start date
              //Solve print issue,see: https://github.com/fullcalendar/fullcalendar/issues/4432
              /*events.sort(function(a, b) {
                return  new Date(a.start) - new Date(b.start);
              });*/

              events.forEach((event, index) => {
                events[index].title = event.title.split(',').join('\n');
              });
              successCallback(events)
            })
            .catch(err => {
              console.error(err.message);
              let calendarNb = +calendarId.split('-')[1];
              this.calendars[calendarNb - 1].destroy();
              this.calendars.splice(calendarNb - 1, 1);
              this.$emit('showErrorMessage', 'LoadCalendars problem. Please contact PNC WEP team if refreshing the page doesn\'t solve the problem.');
              this.iconLoad = 'problem';
              failureCallback(events);
            });
        },
        loading: bool => {
          this.$emit('loader', bool);
          this.iconLoad = bool ? 'loading' : 'ok';
          this.counter = bool ? 'Loading...' : 'Ready.';
        }
      });
      calendar.render();
      //Hide the toolbar to make the printed calendar fit into the page
      this.$refs[calendarId][0].querySelector('.fc-toolbar').style.display = 'none'
      
      this.calendars.push(calendar)
      this.updateCounter(this.calendarsGoogleId)
    },
    updateCounter: function(calendarsGoogleId) {
      this.counter = `(${this.calendars.length}/${
        Object.keys(calendarsGoogleId).length
      })`;
      if (this.calendars.length === Object.keys(calendarsGoogleId).length) {
        this.counter = 'Ready.';
        this.$emit('loader', false);
        this.iconLoad = 'ok';
      }
    },
    nextStep: function() {
      this.calendars.forEach(calendar => {
        calendar.next()
      });
    },
    prevStep: function() {
      this.calendars.forEach(calendar => {
        calendar.prev()
      });
    },
    goToToday: function() {
      this.calendars.forEach(calendar => {
        calendar.today()
      });
    },
    print: function() {
      window.print()
    }
  }
};
</script>

<style scoped>
.break {
  text-align: left;
}
.page-break {
  page-break-after: always;
}
.page {
  width: 29cm;
}
.icon-checked {
  color: #0fc70f;
}

@media print {
    @page {
        size: A4 landscape;
        margin: 0.5cm;
    }
}
</style>
