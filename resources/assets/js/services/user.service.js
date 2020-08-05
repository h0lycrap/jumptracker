import axios from 'axios';
import authHeader from './auth-header';

const API_URL = 'https://urtjumptracker.herokuapp.com/api/auth/';

class UserService {

  getUserBoard() {
    return axios.get(API_URL + 'user', { headers: authHeader() });
  }

}

export default new UserService();