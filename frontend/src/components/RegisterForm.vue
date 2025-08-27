<template>
  <div class="register-container">
    <div class="register-image">
      <img src="https://www.theorderexpert.com/wp-content/uploads/2023/03/how-to-actually-finish-your-to-do-list.jpg" alt="Task Management" />
    </div>
    <div class="register-form">
      <div class="form-header">
        <h2>Create Account</h2>
        <p>Join us to manage your tasks efficiently</p>
      </div>
      
      <form @submit.prevent="handleRegister">
        <div class="form-row">
          <div class="form-group">
            <label>Full Name</label>
            <input v-model="form.full_name" type="text" placeholder="Enter your full name" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input v-model="form.email" type="email" placeholder="Enter your email" required>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Phone Number</label>
            <input v-model="form.phone_number" type="tel" placeholder="Enter phone number" required>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input v-model="form.address" type="text" placeholder="Enter your address" required>
          </div>
        </div>
        
        <div class="form-group">
          <label>Profile Image (Optional)</label>
          <input @change="handleImageUpload" type="file" accept="image/*" class="file-input">
        </div>
        
        <div class="form-group">
          <label>Password</label>
          <input v-model="form.mot_de_passe" type="password" placeholder="Create a strong password" required>
        </div>
        
        <button type="submit" :disabled="loading" class="register-btn">
          {{ loading ? 'Creating Account...' : 'Create Account' }}
        </button>
      </form>
      
      <p v-if="error" class="error">{{ error }}</p>
      
      <div class="form-footer">
        <p>Already have an account? 
          <a href="#" @click="$emit('switch-to-login')" class="switch-link">Sign in</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { authAPI } from '../services/api'

export default {
  name: 'RegisterForm',
  emits: ['register-success', 'switch-to-login'],
  data() {
    return {
      form: {
        full_name: '',
        email: '',
        phone_number: '',
        address: '',
        mot_de_passe: '',
        image: null
      },
      loading: false,
      error: ''
    }
  },
  methods: {
    handleImageUpload(event) {
      this.form.image = event.target.files[0]
    },
    
    async handleRegister() {
      this.loading = true
      this.error = ''
      
      try {
        const formData = new FormData()
        Object.keys(this.form).forEach(key => {
          if (this.form[key] !== null) {
            formData.append(key, this.form[key])
          }
        })
        
        const response = await authAPI.register(formData)
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        this.$emit('register-success', response.data.user)
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.register-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
  background: #20252A;
}

.register-image {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  padding: 0;
}

.register-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.register-form {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 2rem;
  background: #20252A;
  color: #FFD900;
  overflow-y: auto;
}

.form-header {
  text-align: center;
  margin-bottom: 1.5rem;
}

.form-header h2 {
  color: #FFD900;
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

.form-header p {
  color: #ccc;
  font-size: 0.9rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.3rem;
  color: #FFD900;
  font-weight: 500;
  font-size: 0.9rem;
}

input {
  width: 100%;
  padding: 0.8rem;
  border: 2px solid #333;
  border-radius: 6px;
  background: #2a3038;
  color: #FFD900;
  font-size: 0.9rem;
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

.file-input {
  padding: 0.5rem;
  border: 2px dashed #333;
  background: #2a3038;
}

.register-btn {
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
  margin-top: 0.5rem;
}

.register-btn:hover {
  transform: translateY(-2px);
}

.register-btn:disabled {
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
  margin-top: 1.5rem;
}

.form-footer p {
  color: #ccc;
  font-size: 0.9rem;
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
  .register-container {
    grid-template-columns: 1fr;
  }
  
  .register-image {
    display: none;
  }
  
  .register-form {
    padding: 1.5rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>