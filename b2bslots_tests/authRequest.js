const axios = require("axios");
const URLSearchParams = require("url").URLSearchParams;

// Создаем класс для настроек
class Settings {
    constructor(id, data, api, url, authToken) {
        this.id = id;
        this.data = data;
        this.api = api;
        this.url = url;
        this.authToken = authToken;
    }

    // Метод класса для конвертации данных в URLSearchParams
    toURLSearchParams() {
        let params = new URLSearchParams();
        params.append("id", this.id);
        params.append("data", JSON.stringify(this.data));
        params.append("api", this.api);
        return params;
    }

    // Метод класса для получения заголовков
    get headers() {
        return {
            Authorization: `Bearer ${this.authToken}`,
            "Content-Type": "application/x-www-form-urlencoded",
        };
    }
}

// Функция для выполнения запроса
async function performRequest(settings) {
    try {
        const response = await axios.post(settings.url, settings.toURLSearchParams(), {
            headers: settings.headers,
        });

        console.log(response.data);
    } catch (error) {
        console.error(error);
    }
}

// Выполняем запрос
let settings = new Settings(
    "416",
    {
        user_id: "1",
        user_ip: "127.0.0.1",
        user_auth_token: "0e587ff3f636946f176b726a08ea0176",
        currency: "KZT",
        game_code: 1008,
        game_name: "Star Burst",
    },
    "do-auth-user-ingame",
    "http://127.0.0.1:8000/api/cabinet/start_game",
    "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE2OTEyNzEwMTAsImV4cCI6MTY5MTI3NDYxMCwibmJmIjoxNjkxMjcxMDEwLCJqdGkiOiJOYXlzeWE4Q1lnZ3V3Y1R5Iiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.lQgWeDPQjuritEhpN4wXbWAesg3N8jVKePXgnxM8FAI"
);

performRequest(settings);
