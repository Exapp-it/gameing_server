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
        user_game_token: "$2y$10$OgaBaEsWd8nP79ywqbliUei2kWsPAV9S3uDU.i5gGoMfYwNYu31hu",
        currency: "KZT",
        turn_id: 4,
        transaction_id: "2fa7bfb2-a03a-11e6-8347-asdasdasdasdasdsdfsdf",
        game_code: 1008,
        game_name: "Star Burst",
        credit_amount: "0.02",
        round_id: 496,
        credit_type: "regular",
    },
    "do-credit-user-ingame",
    "http://127.0.0.1:8000/api/b2bslots_service/credit",
    "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE2OTExODQ5MDYsImV4cCI6MTY5MTE4ODUwNiwibmJmIjoxNjkxMTg0OTA2LCJqdGkiOiJKTjVNd2ZvUGZSZW5iUTVPIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.GfWcDEQzSkS5TKmoaYY1oUcOJ-iSa0wumeV2akyjmrw"
);

performRequest(settings);
