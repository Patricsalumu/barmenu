<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_menu_shows_seeded_company_and_products(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Le Comptoir')
            ->assertSee('Bar · Restaurant')
            ->assertDontSee('Espace propriétaire')
            ->assertSee('Afficher en cartes')
            ->assertSee('product-grid is-list')
            ->assertSee('Bière blonde')
            ->assertSee('Poulet braisé')
            ->assertDontSee('wa.me/');
    }

    public function test_menu_can_be_filtered_by_subcategory_and_search(): void
    {
        $coldCategory = \App\Models\Category::where('name', 'Froid')->firstOrFail();

        $this->get('/?category='.$coldCategory->id)
            ->assertOk()
            ->assertSee('Salade fraîcheur')
            ->assertDontSee('Poulet braisé');

        $this->get('/?q=mojito')
            ->assertOk()
            ->assertSee('Mojito')
            ->assertDontSee('Bière blonde');
    }

    public function test_ajax_menu_filter_returns_only_matching_results_without_search_buttons(): void
    {
        $category = \App\Models\Category::where('name', 'Soft')->firstOrFail();

        $this->get('/?category='.$category->id.'&q=eau', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Eau minérale')
            ->assertDontSee('Bière blonde')
            ->assertDontSee('Rechercher</button>')
            ->assertDontSee('Effacer');

        $this->get('/', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('menu-results-content')
            ->assertSee('Mojito');
    }

    public function test_owner_dashboard_requires_the_access_code(): void
    {
        $this->get('/proprietaire')->assertRedirect(route('owner.login'));
        $this->followingRedirects()
            ->get('/proprietaire')
            ->assertOk()
            ->assertSee('Votre code d’accès');
        $this->post('/proprietaire/connexion', ['code' => 'incorrect'])
            ->assertSessionHasErrors('code');
        $this->post('/proprietaire/connexion', ['code' => '1234'])
            ->assertRedirect(route('owner.dashboard'));
        $this->get('/proprietaire')->assertOk()->assertSee('Votre établissement');
        $this->post('/proprietaire/deconnexion')->assertRedirect(route('menu'));
        $this->get('/proprietaire')->assertRedirect(route('owner.login'));
    }

    public function test_owner_can_add_a_product_to_the_public_menu(): void
    {
        $category = \App\Models\Category::where('name', 'Soft')->firstOrFail();
        $response = $this->withSession(['owner_authenticated' => true])
            ->post('/proprietaire/produits', [
                'category_id' => $category->id,
                'name' => 'Citronnade',
                'description' => 'Préparée maison',
                'price' => 2000,
            ]);

        $response->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Citronnade']);
        $this->get('/')->assertSee('Citronnade')->assertSee('2 000 F');
    }

    public function test_owner_can_customize_the_activity_type_shown_on_the_public_menu(): void
    {
        $this->withSession(['owner_authenticated' => true])
            ->put('/proprietaire/entreprise', [
                'name' => 'Le Comptoir',
                'activity_type' => 'Café culturel',
                'slogan' => 'Le goût des bons moments',
                'address' => 'Centre-ville',
                'whatsapp_number' => '+2250701234567',
                'theme_color' => '#c83b32',
                'currency' => 'F',
                'exchange_rate' => 600,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'activity_type' => 'Café culturel',
            'whatsapp_number' => '2250701234567',
        ]);
        $response = $this->get('/')
            ->assertOk()
            ->assertSee('Café culturel')
            ->assertDontSee('Bar · Restaurant')
            ->assertSee('https://wa.me/2250701234567')
            ->assertSee('Une question ou une réservation ? WhatsApp')
            ->assertSee('Une remarque, une réclamation ou une réservation ? Écrivez-nous sur WhatsApp');

        $this->assertSame(2, substr_count($response->getContent(), 'https://wa.me/2250701234567'));
        $this->assertStringContainsString(
            'Salut%20Le%20Comptoir%2C%20je%20suis%20un%20client%20de%20votre%20espace%20et%20je%20vous%20%C3%A9cris%20concernant%20%3A',
            $response->getContent(),
        );
    }
}
