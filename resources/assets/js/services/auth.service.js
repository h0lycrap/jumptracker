import axios from 'axios';

const API_URL = 'https://urtjumptracker.herokuapp.com/api/auth/';//

class AuthService {
  login(user) {
    return axios
      .post(API_URL + 'login', {
        email: user.email,
        password: user.password
      })
      .then(response => {
        if (response.data.accessToken) {
          localStorage.setItem('user', JSON.stringify(response.data));
        }

        return response.data;
      });
  }

  updateProgress(progress, id) {
      return axios
        .post(API_URL + 'updateProgress', {
            progress: progress,
            id: id,
        })
        .then(response => {
            localStorage.setItem('user', JSON.stringify(response.data));
            return response.data;
        });
  }

  logout() {
    localStorage.removeItem('user');
  }

  register(user) {
    return axios.post(API_URL + 'register', {
      name: user.name,
      email: user.email,
      password: user.password
    });
  }
}

export default new AuthService();