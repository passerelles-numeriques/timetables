<template>
  <div class="container">
    <div class="alert alert-danger info-error" role="alert" v-show="haveError" v-html="errorInfo">
    </div>
    <div class="row">
      <div class="col-12">
        <h1>Modules</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-10">
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-sm btn-outline-primary icon-button add-button" @click="showAddModule()"><plus-icon /> Add</button>
      </div>
    </div>
    <div class="row table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Action</th>
            <th scope="col">Name</th>
            <th scope="col">Start date</th>
            <th scope="col">End date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="module in modules" :key="module.id">
            <td>
              <button type="button" class="btn btn-sm btn-outline-primary icon-button" @click="showEditModule(module)"><pencil-icon /> Edit</button>
              <button type="button" class="btn btn-sm btn-outline-danger icon-button" @click="showRemove(module)"><closeCircleOutline-icon /> Remove</button>
            </td>
            <td>{{module.name}}</td>
            <td>{{module.startDate | date}}</td>
            <td>{{module.endDate | date}}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="edit" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{modalTitle}} Module</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="moduleName">Module name</label>
              <input type="text" name="moduleName" id="moduleName" placeholder="Name of the module" v-model="sendModule.name">
            </div>
            <div class="form-group">
              <label for="moduleStartDate">Start Date</label>
              <datepicker class="datepicker" placeholder="Select Date" id="moduleStartDate" v-model="sendModule.startDate"></datepicker>

            </div>
            <div class="form-group">
              <label for="moduleEndDate">End Date</label>
              <datepicker class="datepicker" placeholder="Select Date" id="moduleEndDate" v-model="sendModule.endDate"></datepicker>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="editModule">{{modalTitle}}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="remove" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Remove Module</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sur you want to remove this module ({{sendModule.name}})?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="removeModule">Remove</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
/* global moment */
import Datepicker from 'vuejs-datepicker';
import AdminService from '../../services/AdminService';
import Module from '../../models/Module';

import PlusIcon from 'vue-material-design-icons/Plus.vue';
import PencilIcon from 'vue-material-design-icons/Pencil.vue';
import CloseCircleOutlineIcon from 'vue-material-design-icons/CloseCircleOutline.vue';

export default {
  name: 'Modules',
  components: {
    Datepicker,
    PlusIcon,
    PencilIcon,
    CloseCircleOutlineIcon,
  },
  data() {
    return {
      modules: [],
      isCreate: false,
      modalTitle: 'Edit',
      errorInfo: '',
      sendModule: new Module(),
      adminSrv: new AdminService()
    };
  },
  mounted() {
    this.$emit('changeActive', 'Administration');
    this.loadModules();
  },
  computed: {
    haveError() {
      return this.errorInfo !== '';
    }
  },
  methods: {
    loadModules() {
      this.$emit('loader', true);
      this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/modules`, (json) => {
        this.modules = json.map(module => new Module(module.name, module.id, this.getDate(module.startDate), this.getDate(module.endDate)));
        this.$emit('loader', false);
      });
    },
    showEditModule(module) {
      this.isCreate = false;
      this.sendModule = new Module(module.name, module.id, module.startDate, module.endDate);
      this.modalTitle = 'Edit';
      $('#edit').modal('show');
    },
    showAddModule() {
      this.isCreate = true;
      this.sendModule = new Module();
      this.modalTitle = 'Create';
      $('#edit').modal('show');
    },
    showRemove(module) {
      this.sendModule = new Module(module.name, module.id, module.startDate, module.endDate);
      $('#remove').modal('show');
    },
    removeModule() {
      this.modifyModules(this.sendModule, 'DELETE');
      $('#remove').modal('hide');
    },
    editModule() {
      if (!this.haveSameValue(this.sendModule)) {
        this.modifyModules(this.sendModule, this.isCreate ? 'POST' : 'PUT');
      }
      $('#edit').modal('hide');
    },
    formatDate(value) {
      if (!value) {
        return '';
      } else {
        let momentDate = moment(value).local();
        return momentDate.isValid() ? momentDate.format('LL') : '';
      }
    },
    getDate(dateStr) {
      let momentDate = moment(dateStr).local();
      return momentDate.isValid() ? momentDate.toDate() : new Date();
    },
    haveSameValue(selectedModule) {
      let areTheSame = false;
      this.modules.forEach(module => {
        if (module.id === selectedModule.id) {
          areTheSame = module.equals(selectedModule);
        }
      });
      return areTheSame;
    },
    modifyModules(moduleSubmit, verb) {
      this.$emit('loader', true);
      this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/modules`, (json) => {
        if (json.connected) {
          this.errorInfo = 'You cannot remove this module because lessons is using it';
          setTimeout(() => { this.errorInfo = ''; }, 5000);
        } else if (json === true) {
          this.loadModules();
        }
        this.$emit('loader', false);
      }, (ex) => {
        this.errorInfo = 'ERROR' + ex.message;
      }, 
      {
        mode: 'cors',
        method: verb,
        body: JSON.stringify({ module: moduleSubmit })
      });
    }
  },
  filters: {
    date(value) {
      if (!value) {
        return '';
      } else {
        let momentDate = moment(value).local();
        return momentDate.isValid() ? momentDate.format('LL') : '';
      }
    }
  }
};
</script>

<style scoped>
.add-button {
  margin-bottom: 5px;
}
.datepicker {
  display: inline-block;
}
</style>
