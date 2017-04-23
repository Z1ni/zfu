<?php

use App\User;
use Illuminate\Database\Seeder;

class FilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $f = App\File::create([
                "code" => substr($faker->uuid(), 0, 5),
                "mimetype" => "image/png", //$faker->mimeType()
                "visible" => true,
                "size" => rand(2048, 10485760),
                "size_optimized" => rand(1048, 5242880),
                "views" => rand(1, 300),
                "crc" => hash("crc32b", $faker->firstName()),
                "user_id" => User::first()->id
            ]);
            $info = App\ImageInfo::create([
                "width" => rand(50, 1080),
                "height" => rand(50, 1920)
            ]);
            $info->file()->associate($f);
            $info->save();
        }
    }
}
