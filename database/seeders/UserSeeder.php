<?php

namespace Database\Seeders;

use App\Models\TblMahasiswa;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        for ($i = 0; $i < 50; $i++) {
            $name    = $faker->name;
            $email   = $faker->email;
            $nik     = $faker->nik;
            $address = $faker->address;
            $date    = $faker->date;
            $gender  = $faker->randomElement(['male', 'female', 'unknown']);
            $nim     = $faker->unique()->numberBetween();
            $word    = $faker->word();
            $phone   = $faker->phoneNumber();

            // * Create user
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make('secret'),
            ]);

            // * Create mahasiswa
            TblMahasiswa::create([
                'user_id'        => $user->id,
                'nik'            => $nik,
                'nim'            => $nim,
                'date_of_birth'  => $date,
                'place_of_birth' => $word,
                'gender'         => $gender,
                'address'        => $address,
                'phone_number'   => $phone,
            ]);
        }
    }
}
