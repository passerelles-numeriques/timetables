<template>

  <div v-if="modules.length == 0" class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
      <div class="col-12">
        You must create a <router-link to="/admin/modules">module</router-link> first.
      </div>   
    </div>
  </div>

  <div v-else class="container">
    <div class="row">
      <div class="col-12">
        <h1>Lessons</h1>
      </div>
    </div>
    <div class="row">
      <input type="text" class="form-control" name="searchLesson" id="searchLesson" v-model="searchLesson" placeholder="Search">
    </div>
    <div class="row">
      <div class="col-10">
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-sm btn-outline-primary icon-button add-button" @click="showAddLesson()"><plus-icon /> Add</button>
      </div>
    </div>
    <div class="row table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Action</th>
            <th scope="col">Module</th>
            <th scope="col">Lesson</th>
            <th scope="col">Teacher</th>
            <th scope="col">Hours</th>
            <th scope="col">Curriculum</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="module in modules">
          <tr v-for="lesson in lessonsFilteredByName(module)" :key="lesson.id">
            <td>
              <button type="button" class="btn btn-sm btn-outline-primary icon-button" @click="showEditLesson(lesson)"><pencil-icon /> Edit</button>
              <button type="button" class="btn btn-sm btn-outline-danger icon-button" @click="showRemove(lesson)"><closeCircleOutline-icon /> Remove</button>
            </td>
            <td>{{lesson.module.name}}</td>
            <td>{{lesson.name}}</td>
            <td>{{lesson.teacher.name}}</td>
            <td>{{lesson.hours}}</td>
            <td>{{lesson.curriculum}}</td>
          </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div id="edit" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{modalTitle}} Lesson</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="lessonModule">Module</label>
              <select name="lessonModule" id="lessonModule" class="form-control" v-model="selectedModule">
                <option v-for="aModule in allModules" :key="aModule.id" :value="aModule.id">{{aModule.name}}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="lessonName">Lesson name</label>
              <input class="form-control" type="text" name="lessonName" id="lessonName" placeholder="Name of the lesson" v-model="sendLesson.name">
            </div>
            <div class="form-group">
              <label for="lessonTeacher">Teacher</label>
              <select name="lessonTeacher" id="lessonTeacher" class="form-control" v-model="selectedTeacher">
                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{teacher.name}}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="lessonHours">Hours</label>
              <input type="number" name="lessonHours" id="lessonHours" class="form-control" v-model="sendLesson.hours">
            </div>
            <div class="form-group">
              <label for="lessonCurriculum">Curriculum</label>
              <input type="text" name="lessonCurriculum" id="lessonCurriculum" class="form-control" v-model="sendLesson.curriculum">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="editLesson">{{modalTitle}}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="remove" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Remove Lesson</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to remove this lesson ({{sendLesson.name}})?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="removeLesson">Remove</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>
<script>
import AdminService from '../../services/AdminService';
import Lesson from '../../models/Lesson';

import PlusIcon from 'vue-material-design-icons/Plus.vue';
import PencilIcon from 'vue-material-design-icons/Pencil.vue';
import CloseCircleOutlineIcon from 'vue-material-design-icons/CloseCircleOutline.vue';

export default {
  name: 'Lessons',
  data() {
    return {
      modules: [],
      allModules: [],
      teachers: [],
      isCreate: false,
      modalTitle: 'Edit',
      searchLesson: '',
      sendLesson: new Lesson(),
      selectedTeacher: 0,
      selectedModule: 0,
      adminSrv: new AdminService()
    };
  },
  components: {
    PlusIcon,
    PencilIcon,
    CloseCircleOutlineIcon,
  },
  created: function() {
    this.loadLessons(true);
  },
  methods: {
    lessonsFilteredByName(module) {
      return module.lessons.filter(value =>
        value.name.toLowerCase().includes(this.searchLesson.toLowerCase())
      );
    },
    loadLessons(loadTeachers) {
      this.$emit('loader', true);
      if (loadTeachers) {
        this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/teachers`, (json) => {
            //Sort teachers by alphabetic order (names of resources)
            json.sort(function(a,b) {
                return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);
            });
          this.teachers = json;
        }, (exception) => {
          this.$emit('showErrorMessage', 'An error occured while trying to retrieve the teachers: ' + exception.message);
        });

        this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/modules`, (json) => {
            //Sort modules by alphabetic order (names of resources)
            json.sort(function(a,b) {
                return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);
            });
          this.allModules = json;
        }, (exception) => {
          this.$emit('showErrorMessage', 'An error occured while trying to retrieve the modules: ' + exception.message);
        });
      }

      //Get the combined list of modules, lessons, and the teacher of the lesson
      this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/modules/combined`, (json) => {
          this.modules = json;
          this.$emit('loader', false);
        });
    },
    showEditLesson(lesson) {
      this.isCreate = false;
      this.selectedTeacher = lesson.teacher.id;
      this.selectedModule = lesson.module.id;
      this.sendLesson = new Lesson(
        lesson.name,
        lesson.id,
        lesson.module.id,
        lesson.teacher.id,
        lesson.hours,
        lesson.curriculum
      );
      this.modalTitle = 'Edit';
      $('#edit').modal('show');
    },
    showAddLesson() {
      this.isCreate = true;
      this.sendLesson = new Lesson();
      this.modalTitle = 'Create';
      $('#edit').modal('show');
    },
    showRemove(lesson) {
      this.sendLesson = new Lesson(
        lesson.name,
        lesson.id,
        lesson.module.id,
        lesson.teacher.id,
        lesson.hours,
        lesson.curriculum
      );
      $('#remove').modal('show');
    },
    removeLesson() {
      this.modifyLesson(this.sendLesson, 'DELETE');
      $('#remove').modal('hide');
    },
    editLesson() {
      this.modifyLesson(this.sendLesson, this.isCreate ? 'POST' : 'PUT');
      $('#edit').modal('hide');
    },
    modifyLesson(lessonSubmit, verb) {
      this.$emit('loader', true);
      lessonSubmit.module = this.selectedModule;
      lessonSubmit.teacher = this.selectedTeacher;
      this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/lessons`, (json) => {
        if (json === true) {
          this.loadLessons(false);
        }
        this.$emit('loader', false);
      }, (exception) => {
        this.$emit('showErrorMessage', 'An error occured while trying to save the changes: ' + exception.message);
      },
      {
        mode: 'cors',
        method: verb,
        body: JSON.stringify({ lesson: lessonSubmit })
      });
    },
  }
};
</script>

<style scoped>
label {
  margin-bottom: 0.2rem;
}
.form-group {
  text-align: left;
  margin-bottom: 1rem;
}
.add-button {
  margin-bottom: 5px;
  margin-top: 15px;
}
.datepicker {
  display: inline-block;
}
</style>
