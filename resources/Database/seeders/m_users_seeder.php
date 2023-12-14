<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class m_users_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(env('TABLE_PREFIX') . 'm_users')->insert([
            [
                'name' => 'Superadmin 1',
                'username' => 'superadmin1',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('s1234'),
                'email' => 'superadmin1@ihome.com',
                'role' => 'SUPERADMIN',
                'roles' => json_encode(['SUPERADMIN', 'ADMIN']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Superadmin 2',
                'username' => 'superadmin2',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('s1234'),
                'email' => 'superadmin2@ihome.com',
                'role' => 'SUPERADMIN',
                'roles' => json_encode(['SUPERADMIN']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Superadmin 3',
                'username' => 'superadmin3',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('s1234'),
                'email' => 'superadmin3@ihome.com',
                'role' => 'SUPERADMIN',
                'roles' => json_encode(['SUPERADMIN']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Admin Internal',
                'username' => 'admin_internal',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('a1234'),
                'email' => 'admin_internal@ihome.com',
                'role' => 'INTERNAL - ADMIN',
                'roles' => json_encode(['INTERNAL - ADMIN']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Admin',
                'username' => 'admin',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('a1234'),
                'email' => 'admin@ihome.com',
                'role' => 'ADMIN',
                'roles' => json_encode(['ADMIN']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Admin2',
                'username' => 'admin2',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Perempuan',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('a1234'),
                'email' => 'admin2@ihome.com',
                'role' => 'ADMIN',
                'roles' => json_encode(['ADMIN']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Security',
                'username' => 'security1',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('sc1234'),
                'email' => 'sc1@ihome.com',
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'satrya nugroho',
                'username' => 'security2',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '081212301064',
                'email' => 'dizzyknight@yahoo.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Hendri',
                'username' => 'security3',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '087873725114',
                'email' => 'Onghendri60@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'M Tohir',
                'username' => 'security4',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '081292927657.',
                'email' => 'iloytohir@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Dhimas philistine',
                'username' => 'security5',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '082111587890',
                'email' => 'philistinedhimas@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Eman',
                'username' => 'security6',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '08987529607',
                'email' => 'sepasieman441@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Girisona',
                'username' => 'security7',
                'gender' => 'Perempuan',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '0895348129237',
                'email' => 'girisona87@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Samad',
                'username' => 'security8',
                'gender' => 'Perempuan',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '083897395469',
                'email' => 'nursyafa526@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Nardi',
                'username' => 'security9',
                'gender' => 'Perempuan',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '087876563348',
                'email' => 'Icaleinstein@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'idhamcholid',
                'username' => 'security10',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '083870172211',
                'email' => 'idhamcholid433@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Supardi',
                'username' => 'security11',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '087877095616',
                'email' => 'psupardi865@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Bambang Sulistiawan',
                'username' => 'security12',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '087882374615',
                'email' => 'bambangsulistiawan69@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Suhendra',
                'username' => 'security13',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '081806434305',
                'email' => 'miovario55@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Budi Ari Wibowo',
                'username' => 'security14',
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '081381828859',
                'email' => 'wibowoselbi@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'Suratin',
                'username' => 'security15',
                'gender' => 'Perempuan',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'phone' => '081290942728',
                'email' => 'nitarus25@gmail.com',
                'avatar' => null,
                'password' => Hash::make('sc1234'),
                'role' => 'SECURITY',
                'roles' => json_encode(['SECURITY']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],

            [
                'name' => 'RT',
                'username' => 'rt_tester',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('rt1234'),
                'email' => 'rt_tester@ihome.com',
                'role' => 'RT',
                'roles' => json_encode(['RT']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'RW',
                'username' => 'rw_tester',
                "phone" => null,
                "avatar" => null,
                'gender' => 'Laki-laki',
                'date_birth' => Carbon::today()->subDays(rand(0, 365))->subYears(rand(20, 40)),
                'password' => Hash::make('rw1234'),
                'email' => 'rw_tester@ihome.com',
                'role' => 'RW',
                'roles' => json_encode(['RW']),
                'created_id' => 1,
                'created_at' => Carbon::now()
            ],
        ]);
    }
}
