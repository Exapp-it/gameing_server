const axios = require("axios");
const URLSearchParams = require("url").URLSearchParams;

// Создаем класс для настроек
class Settings {
    constructor(data, api, url, authToken) {
        this.data = data;
        this.api = api;
        this.url = url;
        this.authToken = authToken;
    }

    // Метод класса для конвертации данных в URLSearchParams
    toURLSearchParams() {
        let params = new URLSearchParams();
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
        const response = await axios.post(
            settings.url,
            settings.toURLSearchParams(),
            {
                headers: settings.headers,
            }
        );

        console.log(response.data);
    } catch (error) {
        console.error(error);
    }
}

// Выполняем запрос
let settings = new Settings(
    {
        user_id: "1",
        user_ip: "127.0.0.1",
        user_game_token:
            "$2y$10$OgaBaEsWd8nP79ywqbliUei2kWsPAV9S3uDU.i5gGoMfYwNYu31hu",
        currency: "KZT",
        game_code: 1008,
        game_name: "StarBurst",
        free_rounds: {
            id: 1,
            win: "1700",
            round_win: "250",
            count: 7,
            played: 12,
        },
    },
    "do-update-features-user-ingame",
    "http://127.0.0.1:8000/api/b2bslots_service/update-features",
    "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE2OTExODQ5MDYsImV4cCI6MTY5MTE4ODUwNiwibmJmIjoxNjkxMTg0OTA2LCJqdGkiOiJKTjVNd2ZvUGZSZW5iUTVPIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.GfWcDEQzSkS5TKmoaYY1oUcOJ-iSa0wumeV2akyjmrw"
);

performRequest(settings);
