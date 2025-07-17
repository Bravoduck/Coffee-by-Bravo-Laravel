<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\OptionGroup;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        // Data Grup Opsi dengan nama yang bersih dan profesional
        $groups = [
            ['id' => 1, 'name' => 'Ukuran Cup', 'type' => 'radio', 'is_required' => true],
            ['id' => 2, 'name' => 'Sweetness', 'type' => 'radio', 'is_required' => true],
            ['id' => 3, 'name' => 'Ice Cube', 'type' => 'radio', 'is_required' => true],
            ['id' => 4, 'name' => 'Espresso', 'type' => 'radio', 'is_required' => true],
            ['id' => 5, 'name' => 'Dairy', 'type' => 'radio', 'is_required' => true],
            ['id' => 6, 'name' => 'Syrup', 'type' => 'checkbox', 'is_required' => false],
            ['id' => 7, 'name' => 'Topping', 'type' => 'checkbox', 'is_required' => false],
            ['id' => 8, 'name' => 'Temperatur', 'type' => 'radio', 'is_required' => true],
        ];

        // Data Opsi Tambahan, sekarang Ukuran Cup punya semua kemungkinan
        $options = [
            1 => [['name' => 'Regular Ice', 'price' => 0], ['name' => 'Large Ice', 'price' => 4500],['name' => 'Regular Hot', 'price' => 0],['name' => 'Large Hot', 'price' => 4500]],
            2 => [['name' => 'Normal Sweet', 'price' => 0], ['name' => 'Less Sweet', 'price' => 0]],
            3 => [['name' => 'Normal Ice', 'price' => 0], ['name' => 'Less Ice', 'price' => 0], ['name' => 'More Ice', 'price' => 0]],
            4 => [['name' => 'Normal Shot', 'price' => 0], ['name' => '+1 Shot', 'price' => 4500], ['name' => '+2 Shot', 'price' => 9000]],
            5 => [['name' => 'Milk', 'price' => 0], ['name' => 'Oat Milk', 'price' => 10000], ['name' => 'Almond Milk', 'price' => 10000]],
            6 => [['name' => 'Aren', 'price' => 4500], ['name' => 'Hazelnut', 'price' => 4500], ['name' => 'Pandan', 'price' => 4500], ['name' => 'Manuka', 'price' => 4500], ['name' => 'Vanilla', 'price' => 4500], ['name' => 'Salted Caramel', 'price' => 4500]],
            7 => [['name' => 'Caramel Sauce', 'price' => 4500], ['name' => 'Crumble', 'price' => 4500], ['name' => 'Milo Powder', 'price' => 4500], ['name' => 'Oreo Crumbs', 'price' => 4500]],
            8 => [['name' => 'Hot', 'price' => 0], ['name' => 'Extra Hot', 'price' => 0]],
        ];

        foreach ($groups as $groupData) {
            $group = OptionGroup::updateOrCreate(['id' => $groupData['id']], ['name' => $groupData['name'], 'type' => $groupData['type'], 'is_required' => $groupData['is_required']]);
            if (isset($options[$group->id])) {
                foreach ($options[$group->id] as $optionData) {
                    Option::updateOrCreate(
                        ['option_group_id' => $group->id, 'name' => $optionData['name']],
                        ['price' => $optionData['price']]
                    );
                }
            }
        }
    }
}