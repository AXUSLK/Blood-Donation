<?php

namespace Database\Seeders;

use App\Models\User;
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
        $tescher1 =  User::create([
            'id' => 5,
            'first_name' => 'Sachin',
            'last_name' => 'Kavindu',
            'email' => 'bestkavindu@gmail.com',
            'phone' => '0771501502',
            'sales_code' => 'A45SGT484',
            'is_verified' => true,
            'profile_pic' => '/assets/images/tutors/1.jpg',
            'password' => Hash::make('asdasdasd'),
            'email_verified_at' => '2023-05-21 08:53:14',
        ]);


        $tescher2 =  User::create([
            'id' => 3,
            'first_name' => 'Kasun',
            'last_name' => 'Jayarathna',
            'email' => 'kasun@gmail.com',
            'phone' => '0788456587',
            'is_verified' => true,
            'profile_pic' => '/assets/images/tutors/2.jpg',
            'password' => Hash::make('asdasdasd'),
            'email_verified_at' => '2023-05-21 08:53:14',
        ]);

        $student1 =  User::create([
            'id' => 4,
            'first_name' => 'Sadew',
            'last_name' => 'Dullawa',
            'email' => 'sadew@gmail.com',
            'phone' => '0717598455',
            'is_verified' => false,
            'profile_pic' => '/assets/images/tutors/3.jpg',
            'email_verified_at' => '2023-05-21 08:53:14',
            'password' => Hash::make('asdasdasd'),
        ]);

        $student2 =  User::create([
            'id' => 2,
            'first_name' => 'Pasan',
            'last_name' => 'Vithanage',
            'email' => 'pasan@gmail.com',
            'phone' => '0775656988',
            'status' => false,
            'is_verified' => false,
            'profile_pic' => '/assets/images/tutors/3.jpg',
            'password' => Hash::make('asdasdasd'),
        ]);

        $tescher1->assignRole([2]);
        $tescher2->assignRole([2]);
        $student1->assignRole([3]);
        $student2->assignRole([3]);
    }
}
