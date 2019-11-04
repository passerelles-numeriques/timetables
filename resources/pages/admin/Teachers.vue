<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1>Teachers</h1>
      </div>
    </div>
    <div class="row">
      <input type="text" class="form-control" name="searchTeacher" id="searchTeacher" v-model="searchTeacher" placeholder="Search">
    </div>
    <div class="row">
      <div class="col-10">
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-sm btn-outline-primary icon-button add-button" @click="showAddTeacher()"><plus-icon /> Add</button>
      </div>
    </div>
    <div class="row table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Action</th>
            <th scope="col">Name</th>
            <th scope="col">Google id</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="teacher in teachersFiltered" :key="teacher.id">
            <td>
              <button type="button" class="btn btn-sm btn-outline-primary icon-button" @click="showEditTeacher(teacher)"><pencil-icon /> Edit</button>
              <button type="button" class="btn btn-sm btn-outline-danger icon-button" @click="showRemove(teacher)"><closeCircleOutline-icon /> Remove</button>
            </td>
            <td>{{teacher.name}}</td>
            <td>{{teacher.googleId}}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="edit" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{modalTitle}} Teacher</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="teacherName">Teacher name</label>
              <input class="form-control" type="text" name="teacherName" id="teacherName" placeholder="Name of the teacher" v-model="sendTeacher.name" required>
            </div>
            <div class="form-group">
              <label for="teacherGoogleId">Google id</label>
              <input type="email" name="teacherGoogleId" id="teacherGoogleId" class="form-control" v-model="sendTeacher.googleId" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="editTeacher">{{modalTitle}}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="remove" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Remove Teacher</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sur you want to remove this teacher ({{sendTeacher.name}})?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="removeTeacher">Remove</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
import AdminService from '../../services/AdminService';
import Teacher from '../../models/Teacher';

import PlusIcon from 'vue-material-design-icons/Plus.vue';
import PencilIcon from 'vue-material-design-icons/Pencil.vue';
import CloseCircleOutlineIcon from 'vue-material-design-icons/CloseCircleOutline.vue';

export default {
  name: 'Teachers',
  data() {
    return {
      teachers: [],
      isCreate: false,
      modalTitle: 'Edit',
      searchTeacher: '',
      sendTeacher: new Teacher(),
      adminSrv: new AdminService()
    };
  },
  components: {
    PlusIcon,
    PencilIcon,
    CloseCircleOutlineIcon,
  },
  mounted() {
    this.$emit('changeActive', 'Administration');
    this.loadTeachers();
  },
  computed: {
    teachersFiltered() {
      return this.teachers.filter(value =>
        value.name.toLowerCase().includes(this.searchTeacher.toLowerCase())
      );
    }
  },
  methods: {
    loadTeachers() {
      this.$emit('loader', true);
      fetch(`${this.baseURL}api/v1/calendar/teachers`, {
        mode: 'cors'
      })
        .then(response => response.json())
        .then(json => {
          //Sort teachers by alphabetic order (names of resources)
          json.sort(function(a,b) {
              return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);
          });
          this.teachers = json.map(teacher => new Teacher(teacher.name, teacher.id, teacher.google_id));
          this.$emit('loader', false);
        });
    },
    showEditTeacher(teacher) {
      this.isCreate = false;
      this.sendTeacher = new Teacher(teacher.name, teacher.id, teacher.googleId);
      this.modalTitle = 'Edit';
      $('#edit').modal('show');
    },
    showAddTeacher() {
      this.isCreate = true;
      this.sendTeacher = new Teacher();
      this.modalTitle = 'Create';

      $('#edit').modal('show');
    },
    showRemove(teacher) {
      this.sendTeacher = teacher;
      $('#remove').modal('show');
    },
    removeTeacher() {
      this.modifyTeachers(this.sendTeacher, 'DELETE');
      $('#remove').modal('hide');
    },
    editTeacher() {
      if (!this.haveSameValue(this.sendTeacher)) {
        this.modifyTeachers(this.sendTeacher, this.isCreate ? 'POST' : 'PUT');
      }
      $('#edit').modal('hide');
    },
    haveSameValue(selectedTeacher) {
      let areTheSame = false;
      this.teachers.forEach(teacher => {
        if (teacher.id === selectedTeacher.id) {
          areTheSame = teacher.equals(selectedTeacher);
        }
      });
      return areTheSame;
    },
    modifyTeachers(teacherSubmit, verb) {
      this.$emit('loader', true);
      this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/teachers`, (json) => {
        if (json === true) {
          this.loadTeachers();
        }
        this.$emit('loader', false);
      }, (exception) => {
        this.$emit('showErrorMessage', 'An error occured while trying to save the changes: ' + exception.message);
      },
      {
        mode: 'cors',
        method: verb,
        body: JSON.stringify({ teacher: teacherSubmit })
      });
    },
    getModuleName(value) {
      if (!value) return '';
      let moduleN = this.modules.find(module => module.id === value);
      return moduleN ? moduleN.name : '';
    },
    getTeacherName(value) {
      if (!value) return '';
      let teacherN = this.teachers.find(teacher => teacher.id === value);
      return teacherN ? teacherN.name : '';
    }
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
</style>
