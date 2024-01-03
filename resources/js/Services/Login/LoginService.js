import axios from "axios";

export class ImageService {
    static getImages() {
        const url = "https://api.grupoflesan.com/api/getImages";
        return axios.get(url);
    }
}