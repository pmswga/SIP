<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListSubSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('list_sub_system')->insert([
            [
                'idSystemSection' => 1,
                'idSubSystem' => \App\Core\Constants\ListSubSystemConstants::IPS,
                'caption' => 'Индивидуальные планы',
                'route' => 'ips.index'
            ],
            [
                'idSystemSection' => 1,
                'idSubSystem' => \App\Core\Constants\ListSubSystemConstants::Orders,
                'caption' => 'Приказы',
                'route' => 'orders.index',
            ],
            [
                'idSystemSection' => 1,
                'idSubSystem' => \App\Core\Constants\ListSubSystemConstants::Protocols,
                'caption' => 'Протоколы',
                'route' => 'protocols.index'
            ],
            [
                'idSystemSection' => 6,
                'idSubSystem' => \App\Core\Constants\ListSubSystemConstants::Storage,
                'caption' => 'Хранение материалов',
                'route' => 'files.index'
            ],
            [
                'idSystemSection' => 6,
                'idSubSystem' => \App\Core\Constants\ListSubSystemConstants::Tickets,
                'caption' => 'Поручения',
                'route' => 'tickets.index'
            ]
        ]);
    }
}
