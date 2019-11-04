<template>
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <router-link class="navbar-brand" to="/">PNC Timetables</router-link>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <template v-for="menu in menus">
            <template v-if="!menu.admin || menu.admin && isAdmin()">
              <li class="nav-item dropdown" v-if="menu.submenus" :key="menu.name">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"  :class="{active: menu.name == active}">
                  {{menu.name}}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <router-link class="dropdown-item" :to="submenu.href" v-for="submenu in menu.submenus" :key="submenu.name" data-toggle="collapse" data-target=".navbar-collapse">{{submenu.name}}</router-link>
                </div>
              </li>
              <li class="nav-item" v-else :key="menu.name">
                <router-link :class="{active: menu.name == active}" :to="menu.href" class="nav-link" data-toggle="collapse" data-target=".navbar-collapse">{{menu.name}}</router-link>
              </li>
            </template>
          </template>
          <li class="nav-item">
            <button v-if="isAdmin()" class="btn btn-outline-danger my-2 my-sm-0 icon-button" @click="logout"><logout-icon /> Logout</button>
          </li>
        </ul>
      </div>
    </nav>
  </header>
</template>

<script>
import LogoutIcon from 'vue-material-design-icons/Logout.vue';

export default {
  name: 'Header',
  data() {
    return {
      menus: [
        {
          name: 'Teachers',
          href: '/calendar/teachers/'
        },
        {
          name: 'Students',
          href: '/calendar/students/'
        },
        {
          name: 'Rooms',
          href: '/calendar/rooms/'
        },
        {
          name: 'Printable',
          href: '/print/classes/',
          submenus: [
            {
              name: 'All Classes',
              href: '/print/classes'
            },
            {
              name: 'All Teachers',
              href: '/print/teachers'
            },
            {
              name: 'All Rooms',
              href: '/print/rooms'
            }
          ]
        },
        {
          name: 'Statistics',
          href: '/admin/stats/'
        },
        {
          name: 'Administration',
          admin: true,
          submenus: [
            {
              name: 'Modules',
              href: '/admin/modules/'
            },
            {
              name: 'Lessons',
              href: '/admin/lessons/'
            },
            {
              name: 'Manage Teachers',
              href: '/admin/teachers/'
            },
            {
              name: 'Manage Rooms',
              href: '/admin/rooms/'
            },
            {
              name: 'Manage Classes',
              href: '/admin/classes/'
            }
          ]
        }
      ]
    };
  },
  components: {
    LogoutIcon,
  },
  props: {
    active: {
      type: String,
      required: true,
      default: 'Teachers'
    }
  },
  methods: {
    logout: function() {
      sessionStorage.removeItem('username');
      sessionStorage.removeItem('password');
      this.$forceUpdate();
      this.$router.push('/calendar/teachers/');
    },
    isAdmin() {
      return (sessionStorage.getItem('username') !== null);
    },
    isParentMenu(menu) {
      let isParent = false;
      menu.submenus.forEach(submenu => {
        if (submenu.name === this.active) {
          isParent = true;
        }
      });
      return isParent;
    }
  }
};
</script>
