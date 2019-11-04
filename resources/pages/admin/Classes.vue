<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1>Classes</h1>
      </div>
    </div>
    <div class="row">
      <input type="text" class="form-control" name="searchClass" id="searchClass" v-model="searchClass" placeholder="Search">
    </div>
    <div class="row">
      <div class="col-10">
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-sm btn-outline-primary icon-button add-button" @click="showAddClass()"><plus-icon /> Add</button>
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
          <tr v-for="classRoom in classesFiltered" :key="classRoom.id">
            <td>
              <button type="button" class="btn btn-sm btn-outline-primary icon-button" @click="showEditClass(classRoom)"><pencil-icon /> Edit</button>
              <button type="button" class="btn btn-sm btn-outline-danger icon-button" @click="showRemove(classRoom)"><closeCircleOutline-icon /> Remove</button>
            </td>
            <td>{{classRoom.name}}</td>
            <td>{{classRoom.googleId}}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="edit" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{modalTitle}} Class</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="classRoomName">Class name</label>
              <input class="form-control" type="text" name="classRoomName" id="classRoomName" placeholder="Name of the class" v-model="sendClass.name">
            </div>
            <div class="form-group">
              <label for="classGoogleId">Google id</label>
              <input type="email" name="classGoogleId" id="classGoogleId" class="form-control" v-model="sendClass.googleId">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="editClass">{{modalTitle}}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="remove" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Remove Class</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sur you want to remove this classRoom ({{sendClass.name}})?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="removeClass">Remove</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
import AdminService from '../../services/AdminService';
import Class from '../../models/Class';

import PlusIcon from 'vue-material-design-icons/Plus.vue';
import PencilIcon from 'vue-material-design-icons/Pencil.vue';
import CloseCircleOutlineIcon from 'vue-material-design-icons/CloseCircleOutline.vue';

export default {
  name: 'Classes',
  data() {
    return {
      classes: [],
      isCreate: false,
      modalTitle: 'Edit',
      searchClass: '',
      sendClass: new Class(),
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
    this.loadClasses();
  },
  computed: {
    classesFiltered() {
      return this.classes.filter(value =>
        value.name.toLowerCase().includes(this.searchClass.toLowerCase())
      );
    }
  },
  methods: {
    loadClasses() {
      this.$emit('loader', true);
      fetch(`${this.baseURL}api/v1/calendar/classes`, {
        mode: 'cors'
      })
        .then(response => response.json())
        .then(json => {
          this.classes = json.map(classRoom => new Class(classRoom.name, classRoom.id, classRoom.google_id));
          this.$emit('loader', false);
        });
    },
    showEditClass(classRoom) {
      this.isCreate = false;
      this.sendClass = new Class(classRoom.name, classRoom.id, classRoom.googleId);
      this.modalTitle = 'Edit';
      $('#edit').modal('show');
    },
    showAddClass() {
      this.isCreate = true;
      this.sendClass = new Class();
      this.modalTitle = 'Create';

      $('#edit').modal('show');
    },
    showRemove(classRoom) {
      this.sendClass = classRoom;
      $('#remove').modal('show');
    },
    removeClass() {
      this.modifyClasses(this.sendClass, 'DELETE');
      $('#remove').modal('hide');
    },
    editClass() {
      if (!this.haveSameValue(this.sendClass)) {
        this.modifyClasses(this.sendClass, this.isCreate ? 'POST' : 'PUT');
      }
      $('#edit').modal('hide');
    },
    haveSameValue(selectedClass) {
      let areTheSame = false;
      this.classes.forEach(classRoom => {
        if (classRoom.id === selectedClass.id) {
          areTheSame = classRoom.equals(selectedClass);
        }
      });
      return areTheSame;
    },
    modifyClasses(classRoomSubmit, verb) {
      this.$emit('loader', true);
      this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/classes`, (json) => {
        if (json === true) {
          this.loadClasses();
        }
        this.$emit('loader', false);
      }, (exception) => {
        this.$emit('showErrorMessage', 'An error occured while trying to save the changes: ' + exception.message);
      },
      {
        mode: 'cors',
        method: verb,
        body: JSON.stringify({ classRoom: classRoomSubmit })
      });
    },
    getModuleName(value) {
      if (!value) return '';
      let moduleN = this.modules.find(module => module.id === value);
      return moduleN ? moduleN.name : '';
    },
    getClassName(value) {
      if (!value) return '';
      let classRoomN = this.classes.find(classRoom => classRoom.id === value);
      return classRoomN ? classRoomN.name : '';
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
