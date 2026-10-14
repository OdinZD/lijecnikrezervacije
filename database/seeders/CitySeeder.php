<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * All larger Croatian cities (county seats + notable towns) plus the Zadar
     * county micro-towns kept from the original demo so the "Zadar + okolica"
     * radius example still works. Coordinates are approximate town centres —
     * precise enough for the haversine radius search.
     *
     * @var list<array{0: string, 1: string, 2: string, 3: float, 4: float}>
     */
    private array $cities = [
        // [name, postal, county, lat, lng]
        ['Zagreb', '10000', 'Grad Zagreb', 45.8150, 15.9819],
        ['Split', '21000', 'Splitsko-dalmatinska', 43.5081, 16.4402],
        ['Rijeka', '51000', 'Primorsko-goranska', 45.3271, 14.4422],
        ['Osijek', '31000', 'Osječko-baranjska', 45.5550, 18.6955],
        ['Zadar', '23000', 'Zadarska', 44.1194, 15.2314],
        ['Pula', '52100', 'Istarska', 44.8666, 13.8496],
        ['Slavonski Brod', '35000', 'Brodsko-posavska', 45.1603, 18.0156],
        ['Karlovac', '47000', 'Karlovačka', 45.4870, 15.5478],
        ['Varaždin', '42000', 'Varaždinska', 46.3044, 16.3378],
        ['Šibenik', '22000', 'Šibensko-kninska', 43.7350, 15.8952],
        ['Sisak', '44000', 'Sisačko-moslavačka', 45.4658, 16.3779],
        ['Vinkovci', '32100', 'Vukovarsko-srijemska', 45.2881, 18.8048],
        ['Vukovar', '32000', 'Vukovarsko-srijemska', 45.3419, 18.9980],
        ['Dubrovnik', '20000', 'Dubrovačko-neretvanska', 42.6507, 18.0944],
        ['Bjelovar', '43000', 'Bjelovarsko-bilogorska', 45.8988, 16.8487],
        ['Koprivnica', '48000', 'Koprivničko-križevačka', 46.1628, 16.8277],
        ['Požega', '34000', 'Požeško-slavonska', 45.3400, 17.6855],
        ['Čakovec', '40000', 'Međimurska', 46.3844, 16.4339],
        ['Virovitica', '33000', 'Virovitičko-podravska', 45.8319, 17.3836],
        ['Samobor', '10430', 'Zagrebačka', 45.8009, 15.7118],
        ['Velika Gorica', '10410', 'Zagrebačka', 45.7125, 16.0756],
        ['Kutina', '44320', 'Sisačko-moslavačka', 45.4809, 16.7780],
        ['Đakovo', '31400', 'Osječko-baranjska', 45.3089, 18.4100],
        ['Gospić', '53000', 'Ličko-senjska', 44.5461, 15.3744],
        ['Krapina', '49000', 'Krapinsko-zagorska', 46.1608, 15.8703],
        ['Pazin', '52000', 'Istarska', 45.2397, 13.9375],
        ['Metković', '20350', 'Dubrovačko-neretvanska', 43.0542, 17.6485],
        ['Makarska', '21300', 'Splitsko-dalmatinska', 43.2969, 17.0178],
        ['Trogir', '21220', 'Splitsko-dalmatinska', 43.5125, 16.2517],
        ['Kaštela', '21212', 'Splitsko-dalmatinska', 43.5489, 16.3480],
        ['Solin', '21210', 'Splitsko-dalmatinska', 43.5397, 16.4914],
        ['Sinj', '21230', 'Splitsko-dalmatinska', 43.7033, 16.6392],
        ['Knin', '22300', 'Šibensko-kninska', 44.0411, 16.1992],
        ['Petrinja', '44250', 'Sisačko-moslavačka', 45.4414, 16.2819],
        ['Zaprešić', '10290', 'Zagrebačka', 45.8569, 15.8075],
        ['Rovinj', '52210', 'Istarska', 45.0811, 13.6387],
        ['Poreč', '52440', 'Istarska', 45.2269, 13.5944],
        ['Umag', '52470', 'Istarska', 45.4319, 13.5236],
        ['Opatija', '51410', 'Primorsko-goranska', 45.3378, 14.3053],
        ['Crikvenica', '51260', 'Primorsko-goranska', 45.1769, 14.6922],
        ['Nova Gradiška', '35400', 'Brodsko-posavska', 45.2561, 17.3831],
        ['Ivanić-Grad', '10310', 'Zagrebačka', 45.7089, 16.3978],
        ['Jastrebarsko', '10450', 'Zagrebačka', 45.6697, 15.6472],
        ['Dugo Selo', '10370', 'Zagrebačka', 45.8278, 16.2347],
        ['Daruvar', '43500', 'Bjelovarsko-bilogorska', 45.5897, 17.2256],
        ['Županja', '32270', 'Vukovarsko-srijemska', 45.0781, 18.6922],
        ['Ogulin', '47300', 'Karlovačka', 45.2669, 15.2267],
        ['Mali Lošinj', '51550', 'Primorsko-goranska', 44.5319, 14.4697],
        ['Krk', '51500', 'Primorsko-goranska', 45.0269, 14.5758],

        // Zadar county micro-towns (kept for the radius demo around Zadar).
        ['Biograd na Moru', '23210', 'Zadarska', 43.9389, 15.4439],
        ['Benkovac', '23420', 'Zadarska', 44.0372, 15.6117],
        ['Nin', '23232', 'Zadarska', 44.2406, 15.1783],
        ['Pag', '23250', 'Zadarska', 44.4433, 15.0586],
        ['Obrovac', '23450', 'Zadarska', 44.2017, 15.6836],
        ['Sukošan', '23206', 'Zadarska', 44.0536, 15.3200],
        ['Sveti Filip i Jakov', '23207', 'Zadarska', 43.9736, 15.4169],
        ['Privlaka', '23233', 'Zadarska', 44.2639, 15.1397],
        ['Posedarje', '23242', 'Zadarska', 44.2186, 15.4769],
        ['Starigrad', '23244', 'Zadarska', 44.2975, 15.4336],
        ['Pakoštane', '23211', 'Zadarska', 43.9075, 15.5097],
        ['Vir', '23234', 'Zadarska', 44.3042, 15.0514],
    ];

    public function run(): void
    {
        foreach ($this->cities as [$name, $postal, $county, $lat, $lng]) {
            City::query()->updateOrCreate(
                ['name' => $name],
                ['postal_code' => $postal, 'county' => $county, 'lat' => $lat, 'lng' => $lng],
            );
        }
    }
}
