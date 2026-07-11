<?php

/**
 * Zambia Cities & Towns — grouped by province
 * Used for route origin/destination selects across the BookMyBus platform.
 *
 * Usage:
 *   config('zambia_cities')          — full array keyed by province
 *   config('zambia_cities.Lusaka')   — cities in a specific province
 *
 * To get a flat list of all cities (e.g. for validation):
 *   collect(config('zambia_cities'))->flatten()->toArray()
 */

return [

    'Lusaka' => [
        'Lusaka',
        'Kafue',
        'Chongwe',
        'Siavonga',
        'Luangwa',
    ],

    'Copperbelt' => [
        'Kitwe',
        'Ndola',
        'Chingola',
        'Mufulira',
        'Luanshya',
        'Kalulushi',
        'Chililabombwe',
        'Mpongwe',
        'Lufwanyama',
    ],

    'Southern' => [
        'Livingstone',
        'Choma',
        'Mazabuka',
        'Monze',
        'Kalomo',
        'Namwala',
        'Gwembe',
        'Itezhi-Tezhi',
        'Pemba',
        'Zimba',
    ],

    'Central' => [
        'Kabwe',
        'Kapiri Mposhi',
        'Mkushi',
        'Serenje',
        'Chibombo',
        'Mumbwa',
        'Itawa',
    ],

    'Eastern' => [
        'Chipata',
        'Petauke',
        'Lundazi',
        'Katete',
        'Nyimba',
        'Chadiza',
        'Mambwe',
        'Vubwi',
    ],

    'Northern' => [
        'Kasama',
        'Mpika',
        'Mporokoso',
        'Luwingu',
        'Kaputa',
        'Mbala',
        'Chilubi',
        'Mungwi',
    ],

    'Luapula' => [
        'Mansa',
        'Kawambwa',
        'Nchelenge',
        'Samfya',
        'Milenge',
        'Chembe',
        'Chipili',
        'Lunga',
    ],

    'Western' => [
        'Mongu',
        'Kaoma',
        'Senanga',
        'Sesheke',
        'Kalabo',
        'Limulunga',
        'Lukulu',
        'Shangombo',
    ],

    'North-Western' => [
        'Solwezi',
        'Kasempa',
        'Mwinilunga',
        'Kabompo',
        'Zambezi',
        'Chavuma',
        'Ikelenge',
        'Mushindamo',
    ],

    'Muchinga' => [
        'Chinsali',
        'Isoka',
        'Nakonde',
        'Mpika',
        'Shiwang\'andu',
        'Lavushi Manda',
        'Kanchibiya',
    ],

];
