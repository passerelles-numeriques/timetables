<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <select id="select" class="form-control" v-model="selected">
          <option value="">Select a {{resourceName}}...</option>
          <option v-for="resource in resources" :key="resource.id" :value="resource.google_id">{{resource.name}}</option>
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-12">&nbsp;</div>
    </div>

    <div class="row">
      <div class="col-12">
        <div id="timetable"></div>
      </div>
    </div>
  </div>
</template>
<script>

import { Calendar } from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';

/* global FullCalendar */
export default {
  name: 'Calendar',
  data() {
    return {
      calendar: null,
      selected: '',
      menu: 'teachers',
      search: 'teachers',
      resourceName: 'teacher',
      resources: []
    };
  },
  created: function() {
    //Update the selected menu in navbar
    switch (this.$route.params.type) {
      case 'rooms':
        this.menu = 'Rooms';
        this.resourceName = 'room';
        break;
      case 'students':
        this.menu = 'Students';
        this.resourceName = 'class';
        break;
      case 'teachers':
        this.menu = 'Teachers';
        this.resourceName = 'teacher';
        break;
    }
    this.$emit('changeActive', this.menu);

    //Load the dropdown of teachers
    this.loadResource();
    
    //If a calendar was previously selected, 
    // update the FullCalendar widget accordingly (note: id must be in json of resources)
    let lastVisited = localStorage.getItem(this.resourceName);
    if (lastVisited !== null) {
      let googleCalId = 0;
      this.resources.forEach(function(element, index) {
          if (element.google_id === lastVisited)
            googleCalId = element.google_id;
      });
      if (googleCalId != 0) {
        this.selected = googleCalId;  //watchable
      }
    }
  },
  mounted: function() {
    const calendarEl = document.getElementById('timetable');
    this.calendar = new Calendar(calendarEl, {
      plugins: [ dayGridPlugin, timeGridPlugin ],
      defaultView: 'timeGridWeek',
      allDaySlot: false,
      nowIndicator: true,
      minTime: '07:00:00',
      maxTime: '20:00:00',
      hiddenDays: [0], // index of days 0=Sunday, 1=Monday example: [0,1,2,3,4,5,6]
      weekNumbers: true,
      height: 'auto',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      loading: bool => {
        this.$emit('loader', bool);
      },
      displayEventTime: false
    });
    this.calendar.render();
  },
  methods: {
    
    loadResource: function() {
      this.$emit('loader', true);
      this.publicService.getResourceByName(this.$route.params.type,
       (json) => {
          this.resources = json;  //watchable
        this.$emit('loader', false);
        },
        (exception) => {
          this.$emit('showErrorMessage', 'An error occured while trying to load the resource: ' + exception.message);
        });
    },

    updateCalendar: function(selectedCalendarId) {
      if (selectedCalendarId !== '') {
        this.$emit('showErrorMessage', '');
        this.calendar.removeAllEvents();
        this.calendar.removeAllEventSources();
        this.calendar.addEventSource((fetchInfo, successCallback, failureCallback) => {
          let events = [];
          const startTmp = (fetchInfo.start.getTime()/1000).toFixed(0);
          const endTmp = (fetchInfo.end.getTime()/1000).toFixed(0);
          fetch(
            `${this.baseURL}api/v1/google/events/${selectedCalendarId}?start=${startTmp}&end=${endTmp}`,
            { mode: 'cors' }
          )
            .then(response => {
              if (response.status === 200) {
                return response.json();
              } else {
                throw response.text();
              }
            })
            .then(json => {
              events = json;
              events.forEach((event, index) => {
                events[index].title = event.title.split(',').join('\n');
              });
              successCallback(events);
            })
            .catch(err => {
              this.calendar.removeAllEvents();
              this.$emit('showErrorMessage', 'ERROR: Please contact PNC WEP team if refreshing the page doesn\'t solve the problem.');
              console.log(err.message);
              failureCallback(events);
            });
        });
      }
    }
  },
  watch: {

    selected: function(newValue, oldValue) {
      localStorage.setItem(this.resourceName, newValue);
      this.updateCalendar(newValue);
    }
  }
};
</script>

<style scoped>
#timetable {
  margin-bottom: 10px;
}
</style>
