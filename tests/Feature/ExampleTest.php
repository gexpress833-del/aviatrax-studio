<?php

namespace Tests\Feature;

use App\Models\Projet;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_shows_the_public_landing_page_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Aviatrax Studio')
            ->assertSee('Votre musique mérite')
            ->assertSee('Accueil');
    }

    public function test_the_landing_page_remains_the_entry_page_for_authenticated_users(): void
    {
        $this->actingAs(User::factory()->create(['telephone' => '0102030409']))
            ->get('/')
            ->assertOk()
            ->assertSee('Votre musique mérite')
            ->assertSee(route('dashboard'));
    }

    public function test_the_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_administrator_can_log_in_with_valid_credentials(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'administrateur'],
            ['description' => 'Administrateur']
        );

        $admin = User::factory()->create([
            'email' => 'admin-login@example.com',
            'telephone' => '0102030408',
            'password' => Hash::make('old-password'),
            'role_id' => $role->id,
            'status' => 'en_attente_validation',
            'statut' => 'en_attente_validation',
        ]);

        config([
            'admin.email' => $admin->email,
            'admin.password' => 'admin-secret',
        ]);
        app(AdminSeeder::class)->run();
        $admin->refresh();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'admin-secret',
        ]);

        $this->assertSame('valide', $admin->status);
        $this->assertSame('valide', $admin->statut);
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('home'), false)
            ->assertSee('bg-slate-900/90', false)
            ->assertDontSee('bg-white', false);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('bg-slate-900/90', false)
            ->assertDontSee('bg-white', false)
            ->assertDontSee('bg-gray-50 ', false)
            ->assertDontSee('bg-blue-50 ', false);
    }

    public function test_login_error_is_visible_after_invalid_credentials(): void
    {
        $this->from('/login')->post('/login', [
            'email' => 'unknown-user@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->get('/login')
            ->assertOk()
            ->assertSee('Les identifiants fournis ne correspondent pas à nos enregistrements.');
    }

    public function test_guest_can_access_registration_page(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
    }

    public function test_login_and_registration_pages_link_back_to_landing_page(): void
    {
        $landingPageUrl = url('/');

        $this->get('/login')
            ->assertOk()
            ->assertSee('href="' . $landingPageUrl . '"', false)
            ->assertSee('Retour à l’accueil');

        $this->get('/register')
            ->assertOk()
            ->assertSee('href="' . $landingPageUrl . '"', false)
            ->assertSee('Retour à l’accueil');
    }

    public function test_artist_subscription_uses_neutral_payment_copy_and_starts_hosted_mobile_request(): void
    {
        $role = Role::firstOrCreate(['name' => 'artiste'], ['description' => 'Artiste']);
        $artist = User::factory()->create([
            'telephone' => '243812345678',
            'role_id' => $role->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);

        config([
            'services.flexpay.base_url' => 'https://payments.example',
            'services.flexpay.token' => 'test-token',
            'services.flexpay.merchant' => 'AVIATRAX',
        ]);

        Http::fake([
            'https://payments.example/api/rest/v1/paymentService' => Http::response([
                'code' => '0',
                'message' => 'Request accepted',
                'orderNumber' => 'order-123',
            ]),
        ]);

        $this->actingAs($artist)->get(route('subscriptions.index'))
            ->assertOk()
            ->assertSee('12 $')
            ->assertSee('Paiement sécurisé par Mobile Money ou carte')
            ->assertDontSee('FlexPay');

        $this->actingAs($artist)->post(route('subscriptions.checkout'), [
            'phone' => '+243812345678',
            'payment_type' => '1',
        ])->assertRedirect(route('subscriptions.index'));

        $subscription = Subscription::where('user_id', $artist->id)->firstOrFail();
        $this->assertSame('12.00', $subscription->amount);
        $this->assertSame('pending', $subscription->status);
        $this->assertSame('order-123', $subscription->order_number);

        Http::assertSent(fn ($request) => $request->url() === 'https://payments.example/api/rest/v1/paymentService'
            && $request['merchant'] === 'AVIATRAX'
            && $request['type'] === '1'
            && $request['phone'] === '243812345678'
            && $request['currency'] === 'USD');
    }

    public function test_subscription_activates_only_after_server_side_payment_verification(): void
    {
        $role = Role::firstOrCreate(['name' => 'artiste'], ['description' => 'Artiste']);
        $artist = User::factory()->create([
            'telephone' => '243812345679',
            'role_id' => $role->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);
        $subscription = Subscription::create([
            'user_id' => $artist->id,
            'amount' => 55,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_reference' => 'sub-ref-test',
            'order_number' => 'order-verified',
        ]);

        config([
            'services.flexpay.base_url' => 'https://payments.example',
            'services.flexpay.token' => 'test-token',
            'services.flexpay.merchant' => 'AVIATRAX',
        ]);
        $this->travelTo(now()->startOfSecond());

        Http::fake([
            'https://payments.example/api/rest/v1/check/order-verified' => Http::response([
                'code' => '0',
                'transaction' => [
                    'orderNumber' => 'order-verified',
                    'reference' => 'sub-ref-test',
                    'amount' => '55.0',
                    'currency' => 'USD',
                    'status' => '0',
                ],
            ]),
        ]);

        $this->post(route('payments.webhook'), [
            'reference' => 'sub-ref-test',
            'orderNumber' => 'order-verified',
            'code' => '0',
        ])->assertOk();

        $subscription->refresh();
        $this->assertSame('active', $subscription->status);
        $this->assertTrue($subscription->starts_at->equalTo(now()));
        $this->assertTrue($subscription->ends_at->equalTo(now()->addDays(30)));
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/check/order-verified'));
    }

    public function test_only_admin_can_set_official_release_date(): void
    {
        $artistRole = Role::firstOrCreate(['name' => 'artiste'], ['description' => 'Artiste']);
        $adminRole = Role::firstOrCreate(['name' => 'administrateur'], ['description' => 'Administrateur']);
        $artist = User::factory()->create([
            'telephone' => '0102030490',
            'role_id' => $artistRole->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);
        $admin = User::factory()->create([
            'telephone' => '0102030491',
            'role_id' => $adminRole->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);
        $managerRole = Role::firstOrCreate(['name' => 'responsable_studio'], ['description' => 'Studio']);
        $manager = User::factory()->create([
            'telephone' => '0102030492',
            'role_id' => $managerRole->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);
        $project = Projet::create([
            'titre' => 'Release test',
            'description' => 'Projet de test',
            'artiste_id' => $artist->id,
            'responsable_id' => $manager->id,
            'date_debut' => now()->toDateString(),
            'statut' => 'actif',
        ]);

        $this->actingAs($artist)->put(route('admin.projects.release-date', $project), [
            'release_date' => '2027-01-15',
        ])->assertForbidden();

        $this->actingAs($admin)->put(route('admin.projects.release-date', $project), [
            'release_date' => '2027-01-15',
        ])->assertRedirect();

        $this->assertSame('2027-01-15', $project->fresh()->release_date->toDateString());
    }

    public function test_artist_without_active_subscription_cannot_submit_distribution_project(): void
    {
        $role = Role::firstOrCreate(['name' => 'artiste'], ['description' => 'Artiste']);
        $artist = User::factory()->create([
            'telephone' => '0102030493',
            'role_id' => $role->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);

        $this->actingAs($artist)->post(route('distribution.projects.store'), [])
            ->assertRedirect(route('subscriptions.index'))
            ->assertSessionHasErrors('subscription');
    }

    public function test_public_registration_creates_artist_account_by_default(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $response = $this->post('/register', [
            'name' => 'Nouveau Visiteur',
            'email' => 'nouveau-visiteur@example.com',
            'telephone' => '0102030407',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'nouveau-visiteur@example.com')->firstOrFail();

        $this->assertEquals($role->id, $user->role_id);
        $this->assertEquals('valide', $user->status);
        $this->assertEquals('valide', $user->statut);
        $this->assertTrue(auth()->check());
        $this->assertSame($user->id, auth()->id());
    }

    public function test_responsable_studio_user_can_access_dashboard(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'responsable_studio'],
            ['description' => 'Responsable studio']
        );

        $user = User::factory()->create([
            'name' => 'Responsable Studio Test',
            'email' => 'responsable-studio-test@example.com',
            'telephone' => '0101010101',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Dashboard Responsable Studio')
            ->assertSee('bg-slate-900', false)
            ->assertDontSee('bg-white', false)
            ->assertDontSee('bg-gray-50', false);
    }

    public function test_responsable_studio_user_can_manage_artist_accounts(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'responsable_studio'],
            ['description' => 'Responsable studio']
        );

        $user = User::factory()->create([
            'name' => 'Responsable Studio Test',
            'email' => 'responsable-manage@example.com',
            'telephone' => '0101010102',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)->get('/secretariat/artistes/create');

        $response->assertOk();
    }

    public function test_admin_can_assign_a_new_role_after_public_artist_registration(): void
    {
        $artistRole = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrateur'],
            ['description' => 'Administrateur']
        );

        $user = User::factory()->create([
            'name' => 'Artiste inscrit',
            'email' => 'artist-registered@example.com',
            'telephone' => '0102030408',
            'password' => Hash::make('password'),
            'statut' => 'en_attente_validation',
            'role_id' => $artistRole->id,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin principal',
            'email' => 'admin-principal@example.com',
            'telephone' => '0102030409',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $adminRole->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/users/' . $user->id . '/role', [
            'role_id' => $adminRole->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($adminRole->id, $user->fresh()->role_id);
    }

    public function test_artist_creation_duplicate_email_uses_french_and_clear_message(): void
    {
        $studioRole = Role::firstOrCreate(
            ['name' => 'responsable_studio'],
            ['description' => 'Responsable studio']
        );

        $user = User::factory()->create([
            'name' => 'Responsable Studio Test',
            'email' => 'responsable-duplicate@example.com',
            'telephone' => '0101010108',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $studioRole->id,
        ]);

        User::factory()->create([
            'name' => 'Artiste existant',
            'email' => 'duplicate@example.com',
            'telephone' => '0101010109',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => Role::firstOrCreate(['name' => 'artiste'], ['description' => 'Artiste'])->id,
        ]);

        $response = $this->actingAs($user)->from('/secretariat')->post('/secretariat/artistes', [
            'name' => 'Nouveau test',
            'email' => 'duplicate@example.com',
            'telephone' => '0606060606',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('déjà utilisée', $response->exception->validator->errors()->first('email'));
    }

    public function test_artist_cannot_manage_artist_accounts(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $user = User::factory()->create([
            'name' => 'Artiste Test',
            'email' => 'artiste-no-manage@example.com',
            'telephone' => '0101010103',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)->get('/secretariat/artistes/create');

        $response->assertForbidden();
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'responsable_studio'],
            ['description' => 'Responsable studio']
        );

        $user = User::factory()->create([
            'name' => 'Responsable Notifications',
            'email' => 'notifications@example.com',
            'telephone' => '0101010110',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);

        $user->notify(new \App\Notifications\ArtisteAffecteNotification(
            User::factory()->create([
                'name' => 'Artiste à notifier',
                'email' => 'artiste-notification@example.com',
                'telephone' => '0101010111',
                'password' => Hash::make('password'),
                'statut' => 'valide',
                'role_id' => Role::firstOrCreate(['name' => 'artiste'], ['description' => 'Artiste'])->id,
            ])
        ));

        $this->assertEquals(1, $user->fresh()->unreadNotifications()->count());

        $response = $this->actingAs($user)->post('/notifications/read-all');

        $response->assertRedirect();
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_notification_feed_returns_only_the_authenticated_users_notifications(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $user = User::factory()->create([
            'name' => 'Artiste Feed',
            'email' => 'notification-feed@example.com',
            'telephone' => '0101010116',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Autre artiste',
            'email' => 'other-notification-feed@example.com',
            'telephone' => '0101010117',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);

        $user->notify(new \App\Notifications\ArtisteAffecteNotification($otherUser));
        $otherUser->notify(new \App\Notifications\ArtisteAffecteNotification($user));

        $response = $this->actingAs($user)->getJson('/notifications/feed');

        $response->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonCount(1, 'notifications')
            ->assertJsonPath('notifications.0.title', 'Autre artiste');
    }

    public function test_artist_dashboard_keeps_original_entry_and_links_to_wallet_page(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $user = User::factory()->create([
            'name' => 'Artiste Dashboard',
            'email' => 'artist-dashboard@example.com',
            'telephone' => '0101010112',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);

        $user->wallet()->create(['points_balance' => 6000]);

        $this->actingAs($user);
        $dashboard = $this->get('/dashboard');

        $dashboard->assertOk();
        $dashboard->assertSee('Bienvenue sur votre espace artiste');
        $dashboard->assertSee('Mes projets');
        $dashboard->assertSee('Portefeuille');
        $dashboard->assertDontSee('Demander un retrait');

        $wallet = $this->get('/wallet');

        $wallet->assertOk();
        $wallet->assertSee('Portefeuille et retraits');
        $wallet->assertSee('Solde disponible');
        $wallet->assertSee('Demander un retrait');
        $wallet->assertDontSee('configurer par l’administrateur');
    }

    public function test_artist_profile_shows_account_status_and_music_summary(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $user = User::factory()->create([
            'name' => 'Daniel Muela',
            'stage_name' => 'D-Muela',
            'email' => 'profile-artist@example.com',
            'telephone' => '0850167641',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);
        $user->wallet()->create(['points_balance' => 1250]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
        $response->assertSee('D-Muela');
        $response->assertSee('Artiste');
        $response->assertSee('Compte actif');
        $response->assertSee('Mes projets');
        $response->assertSee('1 250');

        $editResponse = $this->post('/profile', [
            'name' => 'Daniel Muela',
            'stage_name' => 'Daniel M.',
            'email' => 'profile-artist@example.com',
            'telephone' => '0850167641',
        ]);

        $editResponse->assertRedirect('/profile');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'stage_name' => 'Daniel M.',
        ]);
    }

    public function test_user_profile_photo_uses_cloudinary_url_when_configured(): void
    {
        config()->set('filesystems.disks.cloudinary', [
            'driver' => 'cloudinary',
            'key' => 'demo-key',
            'secret' => 'demo-secret',
            'cloud' => 'demo-cloud',
            'url' => 'cloudinary://demo-key:demo-secret@demo-cloud',
            'secure' => true,
            'prefix' => 'aviatrax',
        ]);

        $user = new User([
            'photo_path' => 'profiles/avatar.jpg',
        ]);

        $this->assertNotNull($user->photo_url);
        $this->assertStringContainsString('demo-cloud', $user->photo_url);
        $this->assertStringContainsString('profiles', $user->photo_url);
        $this->assertStringContainsString('avatar.jpg', $user->photo_url);
        $this->assertStringNotContainsString('aviatrax/', $user->photo_url);
    }

    public function test_profile_photo_upload_is_rejected_when_cloudinary_is_not_configured(): void
    {
        config()->set('filesystems.disks.cloudinary.cloud', null);
        config()->set('filesystems.disks.cloudinary.key', null);
        config()->set('filesystems.disks.cloudinary.secret', null);

        $user = User::factory()->create([
            'name' => 'Compte sans Cloudinary',
            'email' => 'no-cloudinary@example.com',
            'telephone' => '0102030499',
            'statut' => 'valide',
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertSessionHasErrors('photo');
        $this->assertNull($user->fresh()->photo_path);
    }

    public function test_artist_can_submit_withdrawal_request(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $user = User::factory()->create([
            'name' => 'Artiste Retrait',
            'email' => 'artist-withdraw@example.com',
            'telephone' => '0101010113',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);

        $user->wallet()->create(['points_balance' => 6000]);

        $response = $this->actingAs($user)->post('/wallet/withdraw', [
            'points_amount' => 5000,
            'currency' => 'USD',
            'payment_method' => 'bank_transfer',
            'payment_account' => 'FR142004101005000...1234',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('withdrawal_requests', [
            'user_id' => $user->id,
            'points_amount' => 5000,
            'status' => 'en_attente',
        ]);
    }

    public function test_admin_can_define_currency_exchange_rate(): void
    {
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrateur'],
            ['description' => 'Administrateur']
        );

        $admin = User::factory()->create([
            'name' => 'Admin taux',
            'email' => 'admin-rate@example.com',
            'telephone' => '0101010114',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $adminRole->id,
        ]);

        $this->actingAs($admin);
        $settingsPage = $this->get('/admin/settings/currency');

        $settingsPage->assertOk();
        $settingsPage->assertSee('Francs congolais pour 1 USD');

        $response = $this->put('/admin/settings/currency', [
            'points_per_usd' => 100,
            'cdf_per_usd' => 2850.50,
            'minimum_withdrawal_points' => 5000,
        ]);

        $response->assertRedirect('/admin/settings/currency');
        $this->assertSame(2850.5, (float) Setting::where('key', 'currency_exchange_rate')->firstOrFail()->value['cdf_per_usd']);

        $this->seed(\Database\Seeders\PermissionRoleSeeder::class);

        $this->assertSame(2850.5, (float) Setting::where('key', 'currency_exchange_rate')->firstOrFail()->value['cdf_per_usd']);
    }

    public function test_artist_withdrawal_uses_admin_defined_cdf_rate(): void
    {
        Setting::updateOrCreate(
            ['key' => 'points_conversion_rate'],
            ['value' => ['points_per_dollar' => 100]]
        );
        Setting::updateOrCreate(
            ['key' => 'currency_exchange_rate'],
            ['value' => ['cdf_per_usd' => 2800]]
        );

        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $user = User::factory()->create([
            'name' => 'Artiste retrait CDF',
            'email' => 'artist-cdf@example.com',
            'telephone' => '0101010115',
            'password' => Hash::make('password'),
            'statut' => 'valide',
            'role_id' => $role->id,
        ]);
        $user->wallet()->create(['points_balance' => 6000]);

        $response = $this->actingAs($user)->post('/wallet/withdraw', [
            'points_amount' => 5000,
            'currency' => 'CDF',
            'payment_method' => 'mobile_money',
            'payment_account' => '+243000000000',
        ]);

        $response->assertRedirect('/wallet');
        $this->assertDatabaseHas('withdrawal_requests', [
            'user_id' => $user->id,
            'points_amount' => 5000,
            'cash_amount' => 140000,
            'currency' => 'CDF',
        ]);
    }

    public function test_project_stage_progression_follows_musical_workflow(): void
    {
        $this->assertTrue(Projet::canAdvanceTo('enregistrement', 'mixage'));
        $this->assertTrue(Projet::canAdvanceTo('mixage', 'mastering'));
        $this->assertTrue(Projet::canAdvanceTo('mastering', 'distribution_en_cours'));
        $this->assertFalse(Projet::canAdvanceTo('mixage', 'enregistrement'));
        $this->assertFalse(Projet::canAdvanceTo('enregistrement', 'distribution_terminee'));
    }

    public function test_active_artist_can_log_in_immediately(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'artiste'],
            ['description' => 'Artiste']
        );

        $user = User::factory()->create([
            'name' => 'Artiste actif',
            'email' => 'active-artist@example.com',
            'telephone' => '0102030405',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'status' => 'valide',
            'statut' => 'valide',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(auth()->check());

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }
}
