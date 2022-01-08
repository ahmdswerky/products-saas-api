<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $description = 'Necessitatibus rerum quia amet non quasi fuga.
        Amet quae quas voluptatem. Nihil et est asperiores eligendi reiciendis. Ut omnis voluptas quia eveniet.
        Harum quam ut asperiores quia nesciunt quidem. Consectetur dolore quisquam
        fugit optio vel tempore. Iste dolores laudantium commodi beatae.
        At quia fugit tempore qui sit explicabo quod. Sint et voluptatem voluptatem quis praesentium soluta temporibus.
        Vel aliquid libero eum qui.
        Sit tenetur tempora cum. Porro laboriosam ea consequatur quidem voluptas praesentium optio laboriosam.
        Laudantium et sapiente vitae placeat fugit.
        Fugit molestiae et molestiae harum fugit ab animi. Voluptas provident nihil officia ipsam.
        Tempore dolores perspiciatis voluptatem occaecati.
        Quos accusantium incidunt illo ut adipisci perferendis voluptas velit.
        Non tenetur similique qui illum voluptas explicabo quos consequatur.
        Ipsam nihil odit tempora nulla. Omnis eum hic esse illo impedit. Ab pariatur possimus sit.
        Odit omnis quo nihil alias at dicta corporis. Quas sit dolor optio consequatur vero sed.
        Ratione iste at molestiae explicabo.';

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'public_id' => Str::random(20),
            'title' => $this->faker->realText(20),
            'description' => $this->description,
            'category' => $this->faker->word,
            'price' => $this->faker->numberBetween(100, 3500),
            //'currency' => $this->faker->randomElement(['USD', 'EUR', 'EGP']),
            'currency' => $this->faker->randomElement(['USD']),
            'quantity' => $this->faker->numberBetween(1, 15),
        ];
    }
}
