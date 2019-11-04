// This Class caches the public resources (Teachers, Rooms, and classes)
export default class PublicService {

  constructor(baseUrl) {
    this.baseURL = baseUrl;
    this.config = {
      "global" : {
        "refreshInterval": 600000,
        "cleanInterval": 900000,
        "fetchOption": { "mode": "cors" }
      },
      "modified" : {
        "url": `${this.baseURL}api/v1/app/files/modified`
      },
      "teachers" : {
        "url": `${this.baseURL}api/v1/calendar/teachers`
      },
      "classes" : {
        "url": `${this.baseURL}api/v1/calendar/classes`
      },
      "rooms" : {
        "url": `${this.baseURL}api/v1/calendar/rooms`
      },
      "modules" : {
        "url": `${this.baseURL}api/v1/admin/modules`
      },
      "lessons" : {
        "url": `${this.baseURL}api/v1/admin/lessons`
      },
      "modulesCombined" : {
        "url": `${this.baseURL}api/v1/admin/modules/combined`
      }
    };
    this.refreshLastTimeModified(this);
    //Refresh cache every 10 minutes
    this.interval = setTimeout(this.refreshLastTimeModified, this.config.global.refreshInterval, this);
    //Clean cache every 30 minutes from things older than 2 months
    this.interval = setTimeout(this.cleanOldCalendarsInCache, this.config.global.cleanInterval, this);
  }

  refreshLastTimeModified(context) {
    fetch(
      context.config.modified.url, {mode: 'cors'})
      .then(response => {
        if (response.status === 200) {
          return response.json();
        } else {
          throw response.text();
        }
      })
      .then(json => {
        localStorage.setItem(context.config.modified.url, JSON.stringify(json));
      })
      .catch(err => {
        console.log('[Cache Service]' + err.message);
      });
  }

  getCredentials()
  {
    return "";
  }

  //Create an anonymous helper that will help us to fectch the resource synchronously
  request(url, callbackSuccess, callbackError) {
    fetch(
      `${url}`,
      this.config.global.fetchOption
    )
      .then(response => {
        if (response.status === 200) {
          return response.json();
        } else {
          throw response.text();
        }
      })
      .then(json => {
        //Sort options by alphabetic order (names of resources)
        json.sort(function(a,b) {
          return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);
        });
        let storedResource = {
          data: json,
          timestamp: Math.round((new Date()).getTime() / 1000)
        };
        localStorage.setItem(url, JSON.stringify(storedResource));
        callbackSuccess(json);
      })
      .catch(exception => {
        if (!callbackError) {
          callbackError(exception);
        } else {
          console.log(exception.message);
        }
      });
  }

  getResource(name, url, callbackSuccess, callbackError) {
    //Not yet into the cache
    let storedResource = null;
    if (localStorage.getItem(url) === null) {
      this.request(url, callbackSuccess, callbackError);
    } else {
      //Into the cache but modified since last access
      storedResource = JSON.parse(localStorage.getItem(url));
      let files = JSON.parse(localStorage.getItem(this.config.modified.url));  //Last time modified
      if (files==null) {
        this.refreshLastTimeModified();
        callbackSuccess(storedResource.data);
      } else {
        if (storedResource.timestamp < files[name]) { //check the timestamp
          this.request(url, callbackSuccess, callbackError);
        } else {
          callbackSuccess(storedResource.data);
        }
      }
    }
  }

  getResourceByName(resourceName, callbackSuccess, callbackError) {
    switch (resourceName.toLowerCase()) {
      case 'rooms':
      this.getResource('rooms', this.config.rooms.url, callbackSuccess, callbackError);
      break;
    case 'students':
      this.getResource('students', this.config.classes.url, callbackSuccess, callbackError);
      break;
    case 'teachers':
      this.getResource('teachers', this.config.teachers.url, callbackSuccess, callbackError);
      break;
    }
  }

  getTeachers(callbackSuccess, callbackError) {
    this.getResource('teachers', this.config.teachers.url, callbackSuccess, callbackError);
  }

  getRooms(callbackSuccess, callbackError) {
    this.getResource('rooms', this.config.rooms.url, callbackSuccess, callbackError);
  }

  getClasses(callbackSuccess, callbackError) {
    this.getResource('classes', this.config.classes.url, callbackSuccess, callbackError);
  }

  getModules(callbackSuccess, callbackError) {
    this.getResource('modules', this.config.modules.url, callbackSuccess, callbackError);
  }

  getLessons(callbackSuccess, callbackError) {
    this.getResource('lessons', this.config.lessons.url, callbackSuccess, callbackError);
  }
  
  getModulesCombined(callbackSuccess, callbackError) {
    this.getResource('modulesCombined', this.config.modulesCombined.url, callbackSuccess, callbackError);
  }

  //TODO cleanOldCalendarsInCache
  cleanOldCalendarsInCache(context) {
    Object.keys(localStorage).forEach(function(key){
        //console.log('[cleanOldCalendarsInCache]' + localStorage.getItem(key));
    });
  }

  //Get a calendar:
  // 1. Return immediately if something is into the cache
  // 2. Make an HTTP request to check if data was changed in the background
  // 3. Compare hash of data payload and emit callBackWhenActual if the data changed meanwhile
  getCalendar(selectedCalendarId, startTmp, endTmp, callBackWhenActual) {
    return true;
  }

  sha256(str) {
    var buffer = new TextEncoder("utf-8").encode(str);
    return crypto.subtle.digest("SHA-256", buffer).then(function (hash) {
      return hex(hash);
    });
  }

}
