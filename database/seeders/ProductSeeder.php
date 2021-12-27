<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected $photos = [
        'https://i.pinimg.com/564x/bd/d8/fe/bdd8fe6c259a1116f54f487ded088c65.jpg',
        'https://i.pinimg.com/564x/e8/2c/94/e82c94d23c457d13d2af3a00427b9435.jpg',
        'https://i.pinimg.com/564x/e8/96/43/e8964331dffaaa3b0a9c84df4c12e3a9.jpg',
        'https://i.pinimg.com/564x/06/1e/c2/061ec28b0a0407115aaedbd9e31bae3f.jpg',
        'https://i.pinimg.com/564x/70/77/86/70778628554d53b8925399ba16f448f1.jpg',
        'https://i.pinimg.com/564x/7e/b6/cb/7eb6cb2b8d92f2e9f00a67e9e2dde94e.jpg',
        'https://i.pinimg.com/564x/d1/a8/3c/d1a83c7ab3fe50211ac7885bef53dc7f.jpg',
        'https://i.pinimg.com/564x/cf/d9/f0/cfd9f05724811585fa9aa69dbb793d5e.jpg',
        'https://i.pinimg.com/564x/7a/e1/50/7ae150351299462caf55ce0178894b43.jpg',
        'https://i.pinimg.com/564x/72/2e/cd/722ecd7bbfbfb11c569ddf5f97ff307d.jpg',
        'https://i.pinimg.com/564x/c2/b6/c3/c2b6c3d16d64f4fa32b40d7948473f50.jpg',
        'https://i.pinimg.com/750x/9e/6f/56/9e6f5603c16f5679b3bd621d959c74b3.jpg',
        'https://i.pinimg.com/564x/2f/bd/29/2fbd2944d277315b9b243f53e056a41c.jpg',
        'https://i.pinimg.com/564x/d7/c8/2e/d7c82ee0c9cf1fe81ba6cd03ba9be7ee.jpg',
        'https://i.pinimg.com/564x/8e/88/cf/8e88cffd3526e03447d6f12841324953.jpg',
        'https://i.pinimg.com/564x/47/10/e4/4710e4577d88087288d73baac08df3ef.jpg',
        'https://i.pinimg.com/564x/6a/43/c1/6a43c14e576f3d5aae68a561167c6909.jpg',
        'https://i.pinimg.com/564x/2e/b0/d5/2eb0d5100cea8143ac35f988497a311c.jpg',
        'https://i.pinimg.com/564x/23/b6/0b/23b60bf84677ed31375543de6acf9817.jpg',
        'https://i.pinimg.com/564x/98/2b/59/982b5929ce94a902e794158bdbaf4ce7.jpg',
        'https://i.pinimg.com/564x/36/5a/de/365ade729c71b8b17547203f101bc967.jpg',
        'https://i.pinimg.com/564x/df/9f/ed/df9fed927439b72af354b5379faa987f.jpg',
        'https://i.pinimg.com/564x/dd/ff/80/ddff80f06bae7b4c4df0969b92b20645.jpg',
        'https://i.pinimg.com/564x/8b/fa/2e/8bfa2ea2b7c965af8854bf3e2c1ce530.jpg',
        'https://i.pinimg.com/564x/6d/36/1c/6d361c1f45f071e4f8a6c9aa69824a10.jpg',
        'https://i.pinimg.com/564x/d4/cd/fb/d4cdfb2a9fa2551a1492e8de7f0b1611.jpg',
        'https://i.pinimg.com/564x/38/19/ab/3819ab79f5fb2ae4d370229448155ee0.jpg',
        'https://i.pinimg.com/564x/af/3c/36/af3c36041fd21a8e385705d8cd73c23f.jpg',
        'https://i.pinimg.com/564x/9a/2c/73/9a2c73f61f0ecdd357e6423e7edb24c9.jpg',


        //'https://cdn-icons-png.flaticon.com/512/5257/5257674.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257687.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257695.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257717.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257724.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257739.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257751.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257760.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257768.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257782.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257861.png',
        //'https://cdn-icons-png.flaticon.com/512/5257/5257873.png',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $index = 0;
        $merchant = Merchant::first();

        $products = Product::factory()->count(count($this->photos))->make([
            'category' => 'Skincare'
        ]);

        collect($products)->map(function ($product) use ($merchant, &$index) {
            $product = $merchant->products()->create($product->toArray());

            //echo $this->photos[$index];
            //echo "\n";
            $product->addMedia($this->photos[$index], 'photo', true, [
                'name' => 'photo',
            ], true);

            $index++;
        });
    }
}
