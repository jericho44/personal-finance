import axios from 'axios';
import { useEnvironmentStore } from '@src/stores/environment';
export default () => {
    const _token = localStorage.getItem('lp_token');
    const environmentStore = useEnvironmentStore();
    return axios.create({
        baseURL: environmentStore.data.apiURL,
        withCredentials: false,
        headers: {
            Authorization: _token ? 'Bearer ' + _token : '',
            'X-Tenant': localStorage.getItem('db_version')
        }
    });
}
