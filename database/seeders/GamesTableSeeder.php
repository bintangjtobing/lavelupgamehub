<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GamesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('games')->insert([
            [
                'name' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/ad1427f3fe23204d0c1c90c7087e52fc.png',
                'game_url' => 'https://topup.levelupgamehub.com/game/mobile-legends',
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/54a9960aadb5de1938dec57081bdb642.png',
                'game_url' => 'https://topup.levelupgamehub.com/game/free-fire',
            ],
            [
                'name' => 'Ace Racer',
                'slug' => 'ace-razer',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/bfd70b679d0f58cb1bae3d801f600b9f.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/ace-razer',
            ],
            [
                'name' => 'AFK Journey',
                'slug' => 'afk-journey',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/52b571fc1a9c1343154fe775e0877f74-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/afk-journey',
            ],
            [
                'name' => 'Age of Empires Mobile',
                'slug' => 'age-of-empires-mobile',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/9d765d264bcd0d95046059bcab8ffa74-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/age-of-empires-mobile',
            ],
            [
                'name' => 'Alchemy Stars',
                'slug' => 'alchemy-stars',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/cd188923750867a85d99eadc56142a9b-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/alchemy-stars',
            ],
            [
                'name' => 'Arena Breakout',
                'slug' => 'arena-breakout',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/db543453012adc29a30a86f97347b96b.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/arena-breakout',
            ],
            [
                'name' => 'Arena Of Valor (No Bind Garena)',
                'slug' => 'aov-non-bind-garena',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/3d5475b5792c5eb133b003044e2c6031.png',
                'game_url' => 'https://topup.levelupgamehub.com/game/aov-non-bind-garena',
            ],
            [
                'name' => 'Black Clover M',
                'slug' => 'black-clover-m',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/348d0e953d050004e378b77663e1f216.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/black-clover-m',
            ],
            [
                'name' => 'Blood Strike',
                'slug' => 'blood-strike',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/ce020bb9c5108b4e8c7ae50843fcbad5.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/blood-strike',
            ],
            [
                'name' => 'Call of Duty Mobile (Bind FB)',
                'slug' => 'call-of-duty',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/fd69f8ba39b0a86cff11d4f266adc187.png',
                'game_url' => 'https://topup.levelupgamehub.com/game/call-of-duty',
            ],
            [
                'name' => 'Call of Duty Mobile (Bind Garena)',
                'slug' => 'call-of-duty-bind-garena',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/b2ddd9fed988ada8888f11917574b42b-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/call-of-duty-bind-garena',
            ],
            [
                'name' => 'Captain Tsubasa: Ace',
                'slug' => 'captai-tsubasa-ace',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/008bd276bf48ee7bea74309898c9d47c.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/captai-tsubasa-ace',
            ],
            [
                'name' => 'Cloud Song: Saga of Skywalkers',
                'slug' => 'cloud-song-saga-of-skywalkers',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/a16c353301eb96b6f9154c831a5227f5.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/cloud-song-saga-of-skywalkers',
            ],
            [
                'name' => 'Command & Conquer™: Legions',
                'slug' => 'command-and-conquer-legions',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/e948ad43811a358e7fde2c2b0e139d5b-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/command-and-conquer-legions',
            ],
            [
                'name' => 'Dragon POW!',
                'slug' => 'dragon-pow',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/581e11f4298dff80872c14bbf8352a39-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/dragon-pow',
            ],
            [
                'name' => 'Dragon Raja (SEA)',
                'slug' => 'dragon-raja-sea',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/04925da61211437f013288a68fc0093e.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/dragon-raja-sea',
            ],
            [
                'name' => 'EA SPORTS FC Mobile',
                'slug' => 'ea-sports-fc-mobile',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/5757540e6132cfbaedc828c07e43794e.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/ea-sports-fc-mobile',
            ],
            [
                'name' => 'Eggy Party',
                'slug' => 'eggy-party',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/7badfc9c208862640304508e02206725.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/eggy-party',
            ],
            [
                'name' => 'EVE Echoes',
                'slug' => 'eve-echoes',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/a612e89bb34584e1ab9211e43561cce1-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/eve-echoes',
            ],
            [
                'name' => 'Farlight 84',
                'slug' => 'farlight-84',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/2a0376e0f366fa622de52d62080dcf7c.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/farlight-84',
            ],
            [
                'name' => 'Genshin Impact',
                'slug' => 'genshin-impact',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/f905467c126c2b272aee268a735a5d14.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/genshin-impact',
            ],
            [
                'name' => 'Goddess of Victory: Nikke',
                'slug' => 'goddess-of-victory-nikke',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/dcde8a80e50f8fc477b0a0eb9baf8264.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/goddess-of-victory-nikke',
            ],
            [
                'name' => 'Growtopia',
                'slug' => 'growtopia',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/4d9546e2978bf81810ba38464e5f7a01.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/growtopia',
            ],
            [
                'name' => 'Harry Potter: Magic Awakened',
                'slug' => 'harry-potter-magic-awakened',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/2cab58a49f1513ec3276499a1d344e42.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/harry-potter-magic-awakened',
            ],
            [
                'name' => 'Hago',
                'slug' => 'hago',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/42c17b7bf0c1b04ac8a84e51e527a5b1.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/hago',
            ],
            [
                'name' => 'Honkai Impact 3',
                'slug' => 'honkai-impact-3',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/585df1cdced30710e55903868111ad5b.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/honkai-impact-3',
            ],
            [
                'name' => 'Honkai: Star Rail',
                'slug' => 'honkai-star-rail',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/2622c2a5ae77cd676ba24370dfe578a5.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/honkai-star-rail',
            ],
            [
                'name' => 'Honor of Kings',
                'slug' => 'honor-of-kings',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/0e0502c3cc466b50f70897275b107376.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/honor-of-kings',
            ],
            [
                'name' => 'Identity V',
                'slug' => 'identity-v',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/61c0a12953c1caed425dcee77c235381.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/identity-v',
            ],
            [
                'name' => 'Laplace M',
                'slug' => 'laplace-m',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/24455c988af9675fcddb985d656fdb19.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/laplace-m',
            ],
            [
                'name' => 'League of Legends: Wild Rift',
                'slug' => 'league-of-legends-wild-rift',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/1284bcb919179b6c023738affae7f9ce.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/league-of-legends-wild-rift',
            ],
            [
                'name' => 'League of Legends - PC',
                'slug' => 'league-of-legends-pc',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/ac3c170d55873ad12d8dd4a5e2dd3cdb.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/league-of-legends-pc',
            ],
            [
                'name' => 'Legends of Runeterra',
                'slug' => 'legends-of-runeterra',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/6ffed0654b291ffe7a4219cba580483a.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/legends-of-runeterra',
            ],
            [
                'name' => 'Light of Thel: New Era',
                'slug' => 'light-of-thel-new-era',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/dc2ab2ed125a8bfefae64a6a00870461.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/light-of-thel-new-era',
            ],
            [
                'name' => 'Lita',
                'slug' => 'lita',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/d116956a4539ab35824308d118351346.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/lita',
            ],
            [
                'name' => 'Love and Deepspace',
                'slug' => 'love-and-deepspace',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/e9e886df2eedf8bfc4cd55335fedf522-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/love-and-deepspace',
            ],
            [
                'name' => 'MARVEL SNAP',
                'slug' => 'marvel-snap',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/8f8b021b7742f143cba36519601dcb1e.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/marvel-snap',
            ],
            [
                'name' => 'Metal Slug: Awakening',
                'slug' => 'metal-slug-awakening',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/db14a797c4f2f671e23b2ad3136d42ba.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/metal-slug-awakening',
            ],
            [
                'name' => 'Mirage: Perfect Skyline',
                'slug' => 'mirage-perfect-skyline',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/0dc7c0e0bbeb0b2b3af3e03c58f40bd4.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/mirage-perfect-skyline',
            ],
            [
                'name' => 'NBA INFINITE',
                'slug' => 'nba-infinite',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/4c4d5504075b0088a7d08365b420db87-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/nba-infinite',
            ],
            [
                'name' => 'Never After (Global)',
                'slug' => 'never-after',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/ae0365523a2f2113de24931c00d1d010.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/never-after',
            ],
            [
                'name' => 'New State Mobile (NC)',
                'slug' => 'new-state-mobile',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/56db75b9651429442238d7547fe134e9.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/new-state-mobile',
            ],
            [
                'name' => 'Onmyoji Arena',
                'slug' => 'onmyoji-arena',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/3d509f9c3bdc8649781af6b5c8109d0d-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/onmyoji-arena',
            ],
            [
                'name' => 'Pixel Gun 3D',
                'slug' => 'pixel-gun-3d',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/f75db45019d83f7cb84e84e34bd393c4.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/pixel-gun-3d',
            ],
            [
                'name' => 'PUBG Mobile (Indonesia)',
                'slug' => 'pubg-mobile-indonesia',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/f6438e1b33a2af20aa6821c12b840548.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/pubg-mobile-indonesia',
            ],
            [
                'name' => 'PUBG Mobile (Global)',
                'slug' => 'pubg-mobile-global',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/c4df3c9ec8f0890c7b6fbe322368f24e.png',
                'game_url' => 'https://topup.levelupgamehub.com/game/pubg-mobile-global',
            ],
            [
                'name' => 'Punishing: Gray Raven',
                'slug' => 'punishing-gray-raven',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/336bf9f8767d6fec6ab42b6bafcab91d.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/punishing-gray-raven',
            ],
            [
                'name' => 'Ragnarok M - Eternal Love',
                'slug' => 'ragnarok-m-eternal-love',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/8f1d1e2774067755b349eeaf25e23843.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/ragnarok-m-eternal-love',
            ],
            [
                'name' => 'Ragnarok Origin',
                'slug' => 'ragnarok-origin',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/88ed8714bdcea576420f13ea8acb5a98.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/ragnarok-origin',
            ],
            [
                'name' => 'Ragnarok Origin Special Pack',
                'slug' => 'ragnarok-origin-special-pack',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/1e9fdcfc611f05a5e9b4ebb33480c7a8.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/ragnarok-origin-special-pack',
            ],
            [
                'name' => 'Ragnarok X: Next Generation',
                'slug' => 'ragnarok-x',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/ea1a50450a06c8262f9117e0ae21b412.png',
                'game_url' => 'https://topup.levelupgamehub.com/game/ragnarok-x',
            ],
            [
                'name' => 'Revelation Mobile',
                'slug' => 'revelation-mobile',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/eca1699f921309d75a42c677eb8027c1.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/revelation-mobile',
            ],
            [
                'name' => 'Sausage Man',
                'slug' => 'sausage-man',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/22a94bccf244eed256b0c61fb508b35b.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/sausage-man',
            ],
            [
                'name' => 'SEAL M SEA',
                'slug' => 'seal-m-sea',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/0085738606ccf63d3a1db3380c4c4f0f.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/seal-m-sea',
            ],
            [
                'name' => 'Speed Drifters',
                'slug' => 'speed-drifters',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/6473c415507aed3dedd6e641c7be3cb0.png',
                'game_url' => 'https://topup.levelupgamehub.com/game/speed-drifters',
            ],
            [
                'name' => 'State of Survival',
                'slug' => 'state-of-survival',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/d0b2d6344deedf6ef24f25c71fb1db2c.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/state-of-survival',
            ],
            [
                'name' => 'Super SUS',
                'slug' => 'super-sus',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/e4de995a17b731eb952480fc5102239c.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/super-sus',
            ],
            [
                'name' => 'Tarisland',
                'slug' => 'tarisland',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/c6bb708998ac19d839744c5a68f4a0d5-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/tarisland',
            ],
            [
                'name' => 'Teamfight Tactics Mobile',
                'slug' => 'teamfight-tactics-mobile',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/01680ee78a658a95ce1d6cb18bb0f571.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/teamfight-tactics-mobile',
            ],
            [
                'name' => 'Tom and Jerry: Chase',
                'slug' => 'tom-and-jerry-chase',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/5b23909a02b507db0da71f2581158778-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/tom-and-jerry-chase',
            ],
            [
                'name' => 'Tower Of Fantasy (SEA)',
                'slug' => 'tower-of-fantasy-sea',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/789d50b2484720594cee0f7b59df4f3b.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/tower-of-fantasy-sea',
            ],
            [
                'name' => 'Undawn (Bind Garena)',
                'slug' => 'undawn-bind-garena',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/b0e50623471e9f3f462af74c4e56d63b.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/undawn-bind-garena',
            ],
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/ac7dbc6c7f97282313b7d1c9f18f8dee.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/valorant',
            ],
            [
                'name' => 'Zepeto',
                'slug' => 'zepeto',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/98c6514c1c32e228bf850529160bfe25.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/zepeto',
            ],
            [
                'name' => 'Zenless Zone Zero',
                'slug' => 'zenless-zone-zero',
                'image_url' => 'https://topup.levelupgamehub.com/cdn/522f47b6d59041390c689d9d30aedcec-large.jpeg',
                'game_url' => 'https://topup.levelupgamehub.com/game/zenless-zone-zero',
            ],
        ]);
    }
}
