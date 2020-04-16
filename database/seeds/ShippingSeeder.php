<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipping_infos')->insert([
            'location' => 'Outside Lagos',
            'cost' => 2000,
            'duration_days' => 5,
        ]);

        DB::table('shipping_infos')->insert([
            'location' => 'Lagos - Island (VI, Lekki, ...)',
            'cost' => 1000,
            'duration_days' => 5,
        ]);

        DB::table('shipping_infos')->insert([
            'location' => 'Lagos - Mainland (Ikeja, Maryland, Surulere and environs)',
            'cost' => 1000,
            'duration_days' => 5,
        ]);


        DB::table('shipping_infos')->insert([
            'location' => 'Lagos - Mainland (Apapa, Berger, Ikorodu, Iyana Ipaja, Oshodi and environs)',
            'cost' => 1500,
            'duration_days' => 5,
        ]);
    }
}
