<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $productSize = ['1x1','2x2','3x3','4x4','5x2','2x5'];
        $types = ['online','store'];
        $states = ['active','inactive'];

        foreach($productSize as $size){
            factory(App\Product::class, 1)->create([
                'type' => $types[array_rand($types)],
                'size' => $size,
                'handle' => Str::random(75),
                'published_state' => $states[array_rand($states)],
            ]);   
        }       
    }
}
