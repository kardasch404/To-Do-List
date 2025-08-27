<template>
  <div id="app">
    <div v-if="!isAuthenticated" class="auth-container">
      <LoginForm v-if="showLogin" @login-success="handleAuthSuccess" @switch-to-register="showLogin = false" />
      <RegisterForm v-else @register-success="handleAuthSuccess" @switch-to-login="showLogin = true" />
    </div>
    <KanbanBoard v-else-if="!showNotifications" :user="user" @logout="handleLogout" @show-notifications="showNotifications = true" />
    <NotificationsPage v-else @back-to-dashboard="showNotifications = false" />
  </div>
</template>

<script>
import LoginForm from './components/LoginForm.vue'
import RegisterForm from './components/RegisterForm.vue'
import KanbanBoard from './components/KanbanBoard.vue'
import NotificationsPage from './components/NotificationsPage.vue'

export default {
  name: 'App',
  components: {
    LoginForm,
    RegisterForm,
    KanbanBoard,
    NotificationsPage
  },
  data() {
    return {
      isAuthenticated: false,
      user: null,
      showLogin: true,
      showNotifications: false
    }
  },
  mounted() {
    const token = localStorage.getItem('token')
    const user = localStorage.getItem('user')
    if (token && user) {
      this.isAuthenticated = true
      this.user = JSON.parse(user)
    }
  },
  methods: {
    handleAuthSuccess(user) {
      this.isAuthenticated = true
      this.user = user
    },
    handleLogout() {
      this.isAuthenticated = false
      this.user = null
    }
  }
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  height: 100%;
  font-family: Arial, sans-serif;
  background-color: #20252A;
  overflow-x: hidden;
}

#app {
  width: 100vw;
  height: 100vh;
  margin: 0;
  padding: 0;
  background-color: #20252A;
}

.auth-container {
  width: 100%;
  height: 100vh;
  overflow: hidden;
}
</style>
