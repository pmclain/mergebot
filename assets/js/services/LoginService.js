import Axios from 'axios';

const URL_LOGIN = '/login';

class LoginService {
    static login(username, password) {
        return Axios.post(
            URL_LOGIN,
            {username: username, password: password},
            {
                headers: {
                    'Content-Type': 'application/json',
                }
            }
        );
    }
}

export default LoginService;