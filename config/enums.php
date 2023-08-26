<?php

return [
    "transaction_status" => [
        1 => 'CREATED',
        2 => 'PENDING',
        3 => 'DONE',
        4 => 'CONFIRMED BY YOUR CLIENT',
        5 => 'CANCELLED'
    ],
    "currency" => [
        "IMPSINR",
        "PAYTMINR",
        "UPIINR",
        "PHPEINR",
        "CARDUZS",
        "HUMOUZS",
        "PPRTRY",
        "BANKTRY",
        "DNZTRY",
        "ZRTTRY",
        "VKFTRY",
        "CARDKZT",
        "GBP\EUR",
    ],
    'roles' => [
        'client',
        'analyst',
        'manager',
        'admin'
    ],
    'user_currency' => [
        'RUB',
        'USD',
        'EUR',
        'KZT',
        'SOM'
    ],
    'game_types' => [
        'tomhorn' => 1,
        'mancala' => 2,
        'b2bslots' => 3,
    ],
    'tomHornCodes' => [
        'ok'              => 0,
        'general_err'     => 1,
        'params_err'      => 2,
        'sign_err'        => 3,
        'partner_err'     => 4,
        'identity_err'    => 5,
        'funds_err'       => 6,
        'currency_err'    => 8,
        'rollback_err'    => 9,
        'limit_err'       => 10,
        'reference_err'   => 11,
        'transaction_err' => 12
    ],
    'mancalaCodes' => [
        'NoErrors' => 0, // No errors.
        'HashMismatch' => 1, // Hash Mismatch.
        'PartnerDisabled' => 2, // Partner’s access is disabled.
        'CreateTokenError' => 3, // Token creation Error.
        'InternalServiceError' => 4, // Internal service error.
        'PlayerBlocked' => 5, // Player is blocked.
        'PlayerRegistrationFailed' => 6, // Error has occurred during players registration.
        'CurrencyNotFound' => 7, // Requested currency was not found.
        'GameNotFound' => 8, // Requested game was not found.
        'GameNotAllowed' => 9, // Requested game is disabled.
        'ExtraDataTooLong' => 10, //Number of symbols in the parameter exceeded predefined maximum number of characters.
        'BonusFSExternalIdAlreadyExist' => 11, // External ID of the bonus campaign already exists.
        'BalanceError' => 213,
    ],

    'b2bslotsCodes' => [
        "Success" => 0,
        "Error" => 1,
        "*" => 2,
        "BalanceError" => 3,
        "TokenNotFound" => 4,
        "UserNotFound" => 5,
        "UserBlocked" => 6,
        "TransactionNotFound" => 7,
        "TransactionExpired" => 8,
    ],

    'b2bslotsErrors' => [
        'user' => 'User not found',
        'auth' => 'Authorization error',
        'ip' => 'Wrong IP address',
        'currency' => 'Currency error',
        'game' => 'Game error',
    ],

    'transaction_types' => [
        'withdraw' => 1,
        'deposit' => 2
    ],

    'costs' => [
        'tomhorn' => 0.09,
        'mancala' => 0.012,
        'b2bslots' => 0.08,
        'pay_system' => 0.1,
    ],

    'affiliate' => [
        'RewardPercent' => 0.4,
    ]
];
