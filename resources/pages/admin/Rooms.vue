<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1>Rooms</h1>
      </div>
    </div>
    <div class="row">
      <input type="text" class="form-control" name="searchRoom" id="searchRoom" v-model="searchRoom" placeholder="Search">
    </div>
    <div class="row">
      <div class="col-10">
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-sm btn-outline-primary icon-button add-button" @click="showAddRoom()"><plus-icon /> Add</button>
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
          <tr v-for="room in roomsFiltered" :key="room.id">
            <td>
              <button type="button" class="btn btn-sm btn-outline-primary icon-button" @click="showEditRoom(room)"><pencil-icon /> Edit</button>
              <button type="button" class="btn btn-sm btn-outline-danger icon-button" @click="showRemove(room)"><closeCircleOutline-icon /> Remove</button>
            </td>
            <td>{{room.name}}</td>
            <td>{{room.googleId}}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="edit" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{modalTitle}} Room</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="roomName">Room name</label>
              <input class="form-control" type="text" name="roomName" id="roomName" placeholder="Name of the room" v-model="sendRoom.name">
            </div>
            <div class="form-group">
              <label for="roomGoogleId">Google id</label>
              <input type="email" name="roomGoogleId" id="roomGoogleId" class="form-control" v-model="sendRoom.googleId">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="editRoom">{{modalTitle}}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="remove" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Remove Room</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sur you want to remove this room ({{sendRoom.name}})?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="removeRoom">Remove</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
import AdminService from '../../services/AdminService';
import Room from '../../models/Room';

import PlusIcon from 'vue-material-design-icons/Plus.vue';
import PencilIcon from 'vue-material-design-icons/Pencil.vue';
import CloseCircleOutlineIcon from 'vue-material-design-icons/CloseCircleOutline.vue';

export default {
  name: 'Rooms',
  data() {
    return {
      rooms: [],
      isCreate: false,
      modalTitle: 'Edit',
      searchRoom: '',
      sendRoom: new Room(),
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
    this.loadRooms();
  },
  computed: {
    roomsFiltered() {
      return this.rooms.filter(value =>
        value.name.toLowerCase().includes(this.searchRoom.toLowerCase())
      );
    }
  },
  methods: {
    loadRooms() {
      this.$emit('loader', true);
      fetch(`${this.baseURL}api/v1/calendar/rooms`, {
        mode: 'cors'
      })
        .then(response => response.json())
        .then(json => {
          this.rooms = json.map(room => new Room(room.name, room.id, room.google_id));
          this.$emit('loader', false);
        });
    },
    showEditRoom(room) {
      this.isCreate = false;
      this.sendRoom = new Room(room.name, room.id, room.googleId);
      this.modalTitle = 'Edit';
      $('#edit').modal('show');
    },
    showAddRoom() {
      this.isCreate = true;
      this.sendRoom = new Room();
      this.modalTitle = 'Create';

      $('#edit').modal('show');
    },
    showRemove(room) {
      this.sendRoom = room;
      $('#remove').modal('show');
    },
    removeRoom() {
      this.modifyRooms(this.sendRoom, 'DELETE');
      $('#remove').modal('hide');
    },
    editRoom() {
      if (!this.haveSameValue(this.sendRoom)) {
        this.modifyRooms(this.sendRoom, this.isCreate ? 'POST' : 'PUT');
      }
      $('#edit').modal('hide');
    },
    haveSameValue(selectedRoom) {
      let areTheSame = false;
      this.rooms.forEach(room => {
        if (room.id === selectedRoom.id) {
          areTheSame = room.equals(selectedRoom);
        }
      });
      return areTheSame;
    },
    modifyRooms(roomSubmit, verb) {
      this.$emit('loader', true);
      this.adminSrv.adminFetch(`${this.baseURL}api/v1/admin/rooms`, (json) => {
        if (json === true) {
          this.loadRooms();
        }
        this.$emit('loader', false);
      }, (exception) => {
        this.$emit('showErrorMessage', 'An error occured while trying to save the changes: ' + exception.message);
      },
      {
        mode: 'cors',
        method: verb,
        body: JSON.stringify({ room: roomSubmit })
      });
    },
    getModuleName(value) {
      if (!value) return '';
      let moduleN = this.modules.find(module => module.id === value);
      return moduleN ? moduleN.name : '';
    },
    getRoomName(value) {
      if (!value) return '';
      let roomN = this.rooms.find(room => room.id === value);
      return roomN ? roomN.name : '';
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
