<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::create([
            'id' => 5,
            'title' => 1,
            'first_name' => 'Sachin',
            'last_name' => 'Kavindu',
            'email' => 'bestkavindu@gmail.com',
            'phone' => '0771501502',
            'password' => bcrypt('asdasdasd'),
            'blood_group' => 8,
            'gender' => 1,
            'dob' => '1995-04-11 00:00:00',
        ]);

        $user2 = User::create([
            'id' => 3,
            'title' => 1,
            'first_name' => 'Kasun',
            'last_name' => 'Jayarathna',
            'email' => 'kasun@gmail.com',
            'phone' => '0788456587',
            'password' => bcrypt('asdasdasd'),
            'blood_group' => 11,
            'gender' => 1,
            'dob' => '1994-07-22 00:00:00',
        ]);

        $user3 = User::create([
            'id' => 4,
            'title' => 1,
            'first_name' => 'Sadew',
            'last_name' => 'Dullawa',
            'email' => 'sadew@gmail.com',
            'phone' => '0717598455',
            'password' => bcrypt('asdasdasd'),
            'blood_group' => 6,
            'gender' => 1,
            'dob' => '1993-09-10 00:00:00',
        ]);

        $user4 = User::create([
            'id' => 2,
            'title' => 1,
            'first_name' => 'Pasan',
            'last_name' => 'Vithanage',
            'email' => 'pasan@gmail.com',
            'phone' => '0775656988',
            'password' => bcrypt('asdasdasd'),
            'blood_group' => 10,
            'gender' => 1,
            'dob' => '1992-06-18 00:00:00',
        ]);

        // Extra sample users
        $user5 = User::create([
            'id' => 6,
            'title' => 2,
            'first_name' => 'Nadeesha',
            'last_name' => 'Madushani',
            'email' => 'nadeesha@gmail.com',
            'phone' => '0772223344',
            'password' => bcrypt('asdasdasd'),
            'blood_group' => 9,
            'gender' => 2,
            'dob' => '1996-12-05 00:00:00',
        ]);

        $user6 = User::create([
            'id' => 7,
            'title' => 3,
            'first_name' => 'Tharindu',
            'last_name' => 'Perera',
            'email' => 'tharindu@gmail.com',
            'phone' => '0714432211',
            'password' => bcrypt('asdasdasd'),
            'blood_group' => 12,
            'gender' => 1,
            'dob' => '1991-02-27 00:00:00',
        ]);

        $user1->assignRole([2]);
        $user2->assignRole([3]);
        $user3->assignRole([3]);
        $user4->assignRole([4]);
        $user5->assignRole([2]);
        $user6->assignRole([4]);
    }
}
