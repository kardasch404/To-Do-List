import axios from 'axios'

const API_BASE_URL = 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export const authAPI = {
  login: (credentials) => api.post('/auth/login', credentials),
  register: (userData) => {
    const config = userData instanceof FormData ? 
      { headers: { 'Content-Type': 'multipart/form-data' } } : {}
    return api.post('/auth/register', userData, config)
  },
  logout: () => api.post('/auth/logout')
}

export const taskAPI = {
  getTasks: () => api.get('/tasks'),
  createTask: (task) => api.post('/tasks', task),
  updateTask: (id, task) => api.put(`/tasks/${id}`, task),
  deleteTask: (id) => api.delete(`/tasks/${id}`)
}

export default api