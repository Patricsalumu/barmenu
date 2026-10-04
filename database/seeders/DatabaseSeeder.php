<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $company = Company::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Le Comptoir',
                'slogan' => 'Le goût des bons moments',
                'address' => 'Centre-ville',
                'theme_color' => '#c83b32',
                'currency' => 'F',
                'exchange_rate' => 600,
                'owner_code' => '1234',
            ],
        );

        $categories = [
            'Bières' => [],
            'Soft' => [],
            'Cocktails' => [],
            'Whisky' => [],
            'Food' => ['Entrées', 'Plats', 'Desserts', 'Froid'],
        ];

        foreach ($categories as $name => $children) {
            $category = $company->categories()->firstOrCreate(['name' => $name, 'parent_id' => null]);
            foreach ($children as $childName) {
                $company->categories()->firstOrCreate(['name' => $childName, 'parent_id' => $category->id]);
            }
        }

        $products = [
            ['Bières', 'Bière blonde', 'Bière fraîche et légère', 2500],
            ['Bières', 'Bière artisanale', 'Brassée localement', 3500],
            ['Soft', 'Eau minérale', 'Eau plate, 50 cl', 1000],
            ['Soft', 'Jus de gingembre', 'Préparation maison', 1500],
            ['Soft', 'Soda', 'Au choix selon disponibilité', 1500],
            ['Cocktails', 'Mojito', 'Menthe fraîche, citron vert', 5000],
            ['Cocktails', 'Sunset sans alcool', 'Fruits du jour et agrumes', 3500],
            ['Whisky', 'Whisky sélection', 'Servi pur ou avec glaçons', 6000],
            ['Entrées', 'Accras de poisson', 'Portion de six pièces', 3000],
            ['Plats', 'Poulet braisé', 'Accompagnement au choix', 7500],
            ['Desserts', 'Fondant au chocolat', 'Servi tiède', 3500],
            ['Froid', 'Salade fraîcheur', 'Légumes croquants et vinaigrette maison', 4500],
        ];

        foreach ($products as [$categoryName, $productName, $description, $price]) {
            $category = $company->categories()->where('name', $categoryName)->firstOrFail();
            $company->products()->firstOrCreate(
                ['name' => $productName],
                ['category_id' => $category->id, 'description' => $description, 'price' => $price],
            );
        }
    }
}
