<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            ->assertSee('✦ Une carte préparée avec passion')
            ->assertDontSee('Espace propriétaire')
            ->assertSee('Afficher en cartes')
            ->assertSee('product-grid is-list')
            ->assertSee('Bière blonde')
            ->assertSee('Poulet braisé')
            ->assertDontSee('wa.me/');
    }

    public function test_menu_can_be_filtered_by_subcategory_and_search(): void
    {
        $coldCategory = Category::where('name', 'Froid')->firstOrFail();

        $this->get('/?category='.$coldCategory->id)
            ->assertOk()
            ->assertSee('Salade fraîcheur')
            ->assertDontSee('Poulet braisé');

        $this->get('/?q=mojito')
            ->assertOk()
            ->assertSee('Mojito')
            ->assertDontSee('Bière blonde');
    }

    public function test_category_grid_menu_opens_main_and_subcategories_before_products(): void
    {
        $company = Company::findOrFail(1);
        $company->update(['menu_layout' => 'category_grid']);
        $parent = Category::where('name', 'Food')->firstOrFail();
        $child = $parent->children()->where('name', 'Plats')->firstOrFail();

        $this->get('/')
            ->assertOk()
            ->assertSee('grid-menu-intro')
            ->assertSee('Nos catégories')
            ->assertSee('Food')
            ->assertDontSee('Contacter sur WhatsApp')
            ->assertDontSee('wa.me/')
            ->assertDontSee('Poulet braisé')
            ->assertDontSee('Afficher en cartes');

        $this->get('/?category='.$parent->id)
            ->assertOk()
            ->assertSee('grid-subcategory-container')
            ->assertSee('Entrées')
            ->assertSee('Plats')
            ->assertDontSee('Poulet braisé');

        $this->get('/?category='.$child->id)
            ->assertOk()
            ->assertSee('Poulet braisé')
            ->assertDontSee('Accras de poisson');
    }

    public function test_category_grid_search_can_find_products_within_selected_category(): void
    {
        $company = Company::findOrFail(1);
        $company->update(['menu_layout' => 'category_grid']);
        $food = Category::where('name', 'Food')->firstOrFail();

        $this->get('/?category='.$food->id.'&q=poulet')
            ->assertOk()
            ->assertSee('Poulet braisé')
            ->assertDontSee('Accras de poisson')
            ->assertSee('name="category" value="'.$food->id.'"', false);
    }

    public function test_owner_can_choose_the_category_grid_menu_layout(): void
    {
        $this->withSession(['owner_authenticated' => true, 'owner_company_id' => 1])
            ->put('/proprietaire/entreprise', [
                'name' => 'Le Comptoir',
                'activity_type' => 'Bar · Restaurant',
                'theme_color' => '#c83b32',
                'menu_layout' => 'category_grid',
                'currency' => 'F',
                'exchange_rate' => 600,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('companies', ['id' => 1, 'menu_layout' => 'category_grid']);
        $this->get('/')->assertOk()->assertSee('Nos catégories');
    }

    public function test_category_grid_shows_whatsapp_call_to_action_only_when_number_is_configured(): void
    {
        $company = Company::findOrFail(1);
        $company->update(['menu_layout' => 'category_grid', 'whatsapp_number' => '2250701234567']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Une question, une réclamation ou une réservation ?')
            ->assertSee('Contacter sur WhatsApp')
            ->assertSee('https://wa.me/2250701234567?text=');
    }

    public function test_owner_can_upload_images_for_categories_and_subcategories_display_them_in_grid(): void
    {
        Storage::fake('public');
        $parent = Category::where('name', 'Food')->firstOrFail();
        $child = $parent->children()->where('name', 'Plats')->firstOrFail();
        $ownerSession = ['owner_authenticated' => true, 'owner_company_id' => 1];

        $this->withSession($ownerSession)->put(route('owner.categories.update', $parent), [
            'name' => 'Food',
            'image' => UploadedFile::fake()->image('food.jpg'),
        ])->assertSessionHasNoErrors();

        $this->withSession($ownerSession)->put(route('owner.categories.update', $child), [
            'name' => 'Plats',
            'image' => UploadedFile::fake()->image('plats.jpg'),
        ])->assertSessionHasNoErrors();

        $parent->refresh();
        $child->refresh();
        Storage::disk('public')->assertExists($parent->image_path);
        Storage::disk('public')->assertExists($child->image_path);

        Company::findOrFail(1)->update(['menu_layout' => 'category_grid']);
        $this->get('/')
            ->assertOk()
            ->assertSee('storage/'.$parent->image_path);
        $this->get('/?category='.$parent->id)
            ->assertOk()
            ->assertSee('storage/'.$child->image_path);
    }

    public function test_subcategories_are_hidden_until_their_parent_is_selected(): void
    {
        $parent = Category::where('name', 'Food')->firstOrFail();
        $child = $parent->children()->where('name', 'Entrées')->firstOrFail();
        $groupSelector = '<div class="subcategory-filters" data-subcategory-group="'.$parent->id.'" aria-label="Sous-catégories de Food"';

        $defaultResponse = $this->get('/')->assertOk();
        $this->assertMatchesRegularExpression('/'.preg_quote($groupSelector, '/').'\s+hidden/', $defaultResponse->getContent());

        $parentResponse = $this->get('/?category='.$parent->id)->assertOk();
        $this->assertMatchesRegularExpression('/'.preg_quote($groupSelector, '/').'\s*>/', $parentResponse->getContent());
        $parentResponse->assertSee('Entrées');

        $childResponse = $this->get('/?category='.$child->id)->assertOk();
        $this->assertMatchesRegularExpression('/'.preg_quote($groupSelector, '/').'\s*>/', $childResponse->getContent());
        $childResponse->assertSee('Accras de poisson')->assertDontSee('Poulet braisé');
    }

    public function test_ajax_menu_filter_returns_only_matching_results_without_search_buttons(): void
    {
        $category = Category::where('name', 'Soft')->firstOrFail();

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
        $category = Category::where('name', 'Soft')->firstOrFail();
        $response = $this->withSession(['owner_authenticated' => true, 'owner_company_id' => 1])
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
        $this->withSession(['owner_authenticated' => true, 'owner_company_id' => 1])
            ->put('/proprietaire/entreprise', [
                'name' => 'Le Comptoir',
                'activity_type' => 'Café culturel',
                'slogan' => 'Le goût des bons moments',
                'menu_tagline' => '✦ Des saveurs à partager',
                'address' => 'Centre-ville',
                'whatsapp_number' => '+2250701234567',
                'theme_color' => '#c83b32',
                'menu_layout' => 'classic',
                'currency' => 'F',
                'exchange_rate' => 600,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'activity_type' => 'Café culturel',
            'whatsapp_number' => '2250701234567',
            'menu_tagline' => '✦ Des saveurs à partager',
            'menu_layout' => 'classic',
        ]);
        $response = $this->get('/')
            ->assertOk()
            ->assertSee('Café culturel')
            ->assertSee('✦ Des saveurs à partager')
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

    public function test_super_admin_can_create_a_company(): void
    {
        $this->get('/super-admin/connexion')
            ->assertOk()
            ->assertSee('Super admin');

        $this->post('/super-admin/connexion', ['password' => '123456'])
            ->assertRedirect(route('super-admin.dashboard'));

        $this->get('/super-admin')
            ->assertOk()
            ->assertSee('Gestion des établissements');

        $this->post('/super-admin/entreprises', [
            'name' => 'Casa Brisa',
            'subdomain' => 'casabrisa',
            'activity_type' => 'Restaurant',
            'theme_color' => '#123456',
            'currency' => 'F',
            'exchange_rate' => 600,
            'owner_code' => 'abcd',
        ])->assertRedirect(route('super-admin.dashboard'));

        $this->assertDatabaseHas('companies', ['name' => 'Casa Brisa', 'subdomain' => 'casabrisa']);
    }

    public function test_tenant_subdomain_resolves_the_correct_company(): void
    {
        $this->get('http://lecomptoir.barmenu.site/')
            ->assertOk()
            ->assertSee('Le Comptoir');
    }

    public function test_tenant_subdomain_resolves_locally_under_localhost(): void
    {
        $company = Company::create([
            'name' => 'Habibi',
            'subdomain' => 'habibi-local',
            'activity_type' => 'Restaurant',
            'theme_color' => '#123456',
            'currency' => 'F',
            'exchange_rate' => 600,
            'owner_code' => '4321',
        ]);

        $this->get('http://habibi-local.localhost:8083/')
            ->assertOk()
            ->assertSee($company->name)
            ->assertDontSee('Le Comptoir');
    }

    public function test_super_admin_shows_local_company_link_with_current_port(): void
    {
        $this->withSession(['super_admin_authenticated' => true])
            ->get('http://127.0.0.1:8083/super-admin')
            ->assertOk()
            ->assertSee('http://lecomptoir.localhost:8083/proprietaire/connexion');
    }

    public function test_unknown_company_subdomain_returns_404(): void
    {
        $this->get('http://inconnue.barmenu.site/')
            ->assertNotFound();
    }

    public function test_main_domain_is_not_treated_as_a_company(): void
    {
        $this->get('http://barmenu.site/')
            ->assertOk()
            ->assertSee('Choisissez votre établissement');
    }

    public function test_tenant_isolation_prevents_cross_company_access(): void
    {
        $secondCompany = Company::create([
            'name' => 'Habibi',
            'subdomain' => 'habibi',
            'activity_type' => 'Restaurant',
            'theme_color' => '#123456',
            'currency' => 'F',
            'exchange_rate' => 600,
            'owner_code' => '4321',
        ]);

        $secondCompany->categories()->create(['name' => 'Entrées']);
        $secondCompany->products()->create([
            'category_id' => $secondCompany->categories()->firstOrFail()->id,
            'name' => 'Mezze',
            'description' => 'Produit de l’entreprise Habibi',
            'price' => 4000,
            'is_available' => true,
        ]);

        $this->get('http://habibi.barmenu.site/')
            ->assertOk()
            ->assertSee('Habibi')
            ->assertSee('Mezze')
            ->assertDontSee('Bière blonde');
    }

    public function test_foreign_resource_cannot_be_modified_from_another_company(): void
    {
        $secondCompany = Company::create([
            'name' => 'Habibi',
            'subdomain' => 'habibi-foreign',
            'activity_type' => 'Restaurant',
            'theme_color' => '#123456',
            'currency' => 'F',
            'exchange_rate' => 600,
            'owner_code' => '4321',
        ]);

        $category = $secondCompany->categories()->create(['name' => 'Entrées']);
        $product = $secondCompany->products()->create([
            'category_id' => $category->id,
            'name' => 'Produit étranger',
            'description' => 'Ce produit ne doit pas être modifiable ici.',
            'price' => 3000,
            'is_available' => true,
        ]);

        $this->withServerVariables(['HTTP_HOST' => 'lecomptoir.barmenu.site'])
            ->withSession(['owner_authenticated' => true, 'owner_company_id' => 1])
            ->put('http://lecomptoir.barmenu.site/proprietaire/produits/'.$product->id, [
                'category_id' => $category->id,
                'name' => 'Tentative de modification',
                'description' => 'Ce produit est interdit.',
                'price' => 9999,
            ])
            ->assertNotFound();
    }
}
