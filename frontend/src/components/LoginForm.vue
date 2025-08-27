<template>
  <div class="login-container">
    <div class="login-image">
      <img src="https://www.theorderexpert.com/wp-content/uploads/2023/03/how-to-actually-finish-your-to-do-list.jpg" alt="Task Management" />
    </div>
    <div class="login-form">
      <div class="form-header">
        <h2>Welcome Back</h2>
        <p>Sign in to your account</p>
      </div>
      
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Email Address</label>
          <input v-model="form.email" type="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input v-model="form.mot_de_passe" type="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" :disabled="loading" class="login-btn">
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>
      
      <p v-if="error" class="error">{{ error }}</p>
      
      <div class="form-footer">
        <p>Don't have an account? 
          <a href="#" @click="$emit('switch-to-register')" class="switch-link">Create one</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { authAPI } from '../services/api'

export default {
  name: 'LoginForm',
  emits: ['login-success', 'switch-to-register'],
  data() {
    return {
      form: {
        email: '',
        mot_de_passe: ''
      },
      loading: false,
      error: ''
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true
      this.error = ''
      
      try {
        const response = await authAPI.login(this.form)
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        this.$emit('login-success', response.data.user)
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
  background: #20252A;
}

.login-image {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  padding: 0;
}

.login-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.login-form {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 3rem;
  background: #20252A;
  color: #FFD900;
}

.form-header {
  text-align: center;
  margin-bottom: 2rem;
}

.form-header h2 {
  color: #FFD900;
  font-size: 2rem;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

.form-header p {
  color: #ccc;
  font-size: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #FFD900;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 1rem;
  border: 2px solid #333;
  border-radius: 8px;
  background: #2a3038;
  color: #FFD900;
  font-size: 1rem;
  transition: border-color 0.3s;
  box-sizing: border-box;
}

input:focus {
  outline: none;
  border-color: #FFD900;
}

input::placeholder {
  color: #888;
}

.login-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  color: #20252A;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: bold;
  transition: transform 0.2s;
}

.login-btn:hover {
  transform: translateY(-2px);
}

.login-btn:disabled {
  background: #666;
  color: #ccc;
  transform: none;
}

.error {
  color: #ff4757;
  text-align: center;
  margin-top: 1rem;
  padding: 0.75rem;
  background: rgba(255, 71, 87, 0.1);
  border-radius: 6px;
  border: 1px solid #ff4757;
}

.form-footer {
  text-align: center;
  margin-top: 2rem;
}

.form-footer p {
  color: #ccc;
}

.switch-link {
  color: #FFD900;
  text-decoration: none;
  font-weight: bold;
}

.switch-link:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .login-container {
    grid-template-columns: 1fr;
  }
  
  .login-image {
    display: none;
  }
  
  .login-form {
    padding: 2rem;
  }
}
</style>