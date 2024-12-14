import axios from "axios";

const axiosClient = axios.create({
    // Set the base URL for the API endpoints.
    baseURL: 'http://127.0.0.1:8000/api',  // Local development API URL (can be changed for production)
})

export default axiosClient;