<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds of pages and related blocks.
     *
     * @return void
     */
    public function run()
    {
        $schemas = [
            [
                'model' => [
                    'name' => 'Moklyak Yaroslav',
                    'avatar' => 'author-yaroslav.jpeg',
                    'facebook' => 'https://www.facebook.com/Gnida24/',
                    'instagram' => 'https://www.instagram.com/leonardo_de_moklya/',
                    'youtube' => 'https://www.youtube.com/channel/UC7ZYZeLyK_kKnylTkW1QR6g',
                    'email' => 'yarikmoklyak2010@gmail.com',
                    'steam' => 'https://steamcommunity.com/id/gnida24/',
                ],
                'translations' => [
                    'slug' => [
                        'en' => 'author-yaroslav',
                        'es' => 'author-yaroslav',
                    ],
                    'description' => [
                        'en' => 'Hello, I am Yaroslav – an owner of the HermitGamer website, games lover, and critic. I started playing computer games in 2005, and have a big experience in PC and PS games.

                        I am writing content about computer and console games for the last few years and have reviewed a big amount of games.

                        As for my leisure time, I prefer to spend it actively, for example: traveling, snowboarding, boxing. Moreover, I spend a lot of time playing video games.',
                        'es' => 'Hola, soy Yaroslav, propietario del sitio web HermitGamer, amante de los juegos y crítico. Empecé a jugar juegos de computadora en 2005 y tengo una gran experiencia en juegos de PC y PS.

                        Estoy escribiendo contenido sobre juegos de computadora y consola durante los últimos años y he revisado una gran cantidad de juegos.

                        En cuanto a mi tiempo libre, prefiero pasarlo activamente, por ejemplo: viajar, hacer snowboard, boxear. Además, paso mucho tiempo jugando videojuegos.',
                    ],
                ],
            ],
            [
                'model' => [
                    'name' => 'Mariia Grachova',
                    'avatar' => 'mariia-grachova.jpeg',
                    'facebook' => 'https://www.facebook.com/lady.grachovaa',
                    'instagram' => 'https://www.instagram.com/grachova_mary/',
                    'email' => 'lady.grachovaa@gmail.com',
                    'twitter' => 'https://twitter.com/President_Rikko',
                ],
                'translations' => [
                    'slug' => [
                        'en' => 'author-mariia',
                        'es' => 'author-mariia',
                    ],
                    'description' => [
                        'en' => 'Hi! My name is Mariia, and I am the content writer of the gaming news site – HermitGamer. I have been playing video games since I was 10 years old. For now, I am fond of playing on PC, but I also have a PS5.

                        Mostly I am writing gaming reviews on PC games I have played. Moreover, I like to write different guides, walkthroughs, and some interesting cheats on games.

                        I hope you enjoy reading my reviews 😄',
                        'es' => '¡Hola! Mi nombre es Mariia y soy la escritora de contenido del sitio de noticias de juegos: HermitGamer. He estado jugando videojuegos desde que tenía 10 años. Por ahora, soy aficionado a jugar en PC, pero también tengo una PS5.

                        Principalmente escribo reseñas de juegos en juegos de PC que he jugado. Además, me gusta escribir diferentes guías, tutoriales y algunos trucos interesantes en los juegos.

                        Espero que disfrutes leyendo mis reseñas 😄',
                    ],
                ],
            ],
        ];

        foreach ($schemas as $schema) {
            $model = Author::create($schema['model']);
            $model->saveTranslations($schema['translations']);
        }
    }
}
