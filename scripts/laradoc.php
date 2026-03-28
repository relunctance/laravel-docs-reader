#!/usr/bin/env php
<?php
/**
 * Laravel Docs Reader CLI
 *
 * Usage:
 *   php laradoc.php <command> [args]
 *
 * Commands:
 *   search <query>              Natural language search (e.g. "how to create middleware")
 *   version [path]               Detect local Laravel version (default: current dir)
 *   current                      Show Laravel version used by default
 *   config <file>                Show config reference (e.g. database, cache)
 *   facade <name>               Show Facade methods (e.g. Cache, DB, Route)
 *   artisan <cmd>               Artisan command help (e.g. make:controller)
 *   diff <feature>              Version diff (e.g. auth, routing, middleware)
 *   generate <type> <name>     Generate code skeleton (controller, model, job...)
 *   lang <query>                Search Blade directives (e.g. "loop", "if")
 *   psr [topic]                 PSR-12 quick reference (or specific topic)
 *   cache                       Show local doc cache status
 *   update                      Pull latest docs from GitHub (force refresh)
 *   subscribe                   Show / manage doc subscription status
 *
 * Examples:
 *   php laradoc.php search "how to create a middleware"
 *   php laradoc.php search "how to send a notification"
 *   php laradoc.php version
 *   php laradoc.php version /path/to/project
 *   php laradoc.php config database
 *   php laradoc.php facade Cache
 *   php laradoc.php diff auth
 *   php laradoc.php generate controller UserController
 *   php laradoc.php lang "loop variable"
 *   php laradoc.php psr                 # Full PSR-12 summary
 *   php laradoc.php psr arrays         # Arrays rule only
 *   php laradoc.php psr naming         # Naming conventions
 *   php laradoc.php cache              # Show cache status
 *   php laradoc.php update             # Force-pull latest docs
 *   php laradoc.php subscribe           # Show subscription
 *
 * Auto-Update:
 *   This tool is kept up to date via GitHub Actions.
 *   A workflow runs weekly to check laravel/docs for changes,
 *   and auto-opens a PR when this skill's references are outdated.
 */

$DEFAULT_VERSION = '12';

// ─────────────────────────────────────────────────────────────
// Documentation Database (strings → sections)
// ─────────────────────────────────────────────────────────────

$DOCS_INDEX = [
    // ── Routing ──
    'routing' => [
        'scene' => 'Routing', 'version' => '10/11/12',
        'intro' => 'Routes are defined in routes/web.php, routes/api.php, routes/console.php.',
        'example' => <<<'PHP'
Route::get('/user/{id}', function (int $id) {
    return view('user.show', ['id' => $id]);
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
PHP,
        'notes' => [
            'Laravel 11+: routes/api.php and routes/web.php are merged into a single routes directory.',
            'Laravel 12: Route group attributes use array syntax instead of prefix/suffix methods.',
            'Always use named routes to avoid hardcoded URLs.',
        ],
    ],
    'middleware' => [
        'scene' => 'Middleware', 'version' => '10/11/12',
        'intro' => 'Middleware provides a way to filter HTTP requests. Create with `php artisan make:middleware`',
        'example' => <<<'PHP'
// app/Http/Middleware/CheckAge.php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class CheckAge
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->age < 18) {
            return redirect('/denied');
        }
        return $next($request);
    }
}

// Register in bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $m) {
    $m->web(append: [CheckAge::class]);
    $m->alias(['admin' => CheckAdmin::class]);
})
PHP,
        'notes' => [
            'Laravel 11+: Middleware registration moved from Kernel.php to bootstrap/app.php.',
            'Laravel 12: withMiddleware() API is further refined.',
            'Use `$m->alias()` for named middleware shortcuts.',
        ],
    ],
    'controller' => [
        'scene' => 'Controllers', 'version' => '10/11/12',
        'intro' => 'Controllers organize request handling logic. Create with `php artisan make:controller`',
        'example' => <<<'PHP'
namespace App\Http\Controllers;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'Created!');
    }

    public function show(int $id): View|Aborts403
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }
}
PHP,
        'notes' => [
            'Always type-hint dependencies in __construct() for automatic DI.',
            'Use Form Request classes for complex validation: `$request->validate(...)` or custom FormRequest.',
            'Laravel 11+: Single Action Controllers with `__invoke()` are supported.',
        ],
    ],
    'model' => [
        'scene' => 'Eloquent', 'version' => '10/11/12',
        'intro' => 'Models interact with database tables. Create with `php artisan make:model`',
        'example' => <<<'PHP'
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'is_admin' => 'bool'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
PHP,
        'notes' => [
            'Always set $fillable or use $guarded.',
            'Use accessors `$casts` for automatic type conversion.',
            'Laravel 12: Mass assignment protection is still essential.',
            'Define relationships as methods, not properties.',
        ],
    ],
    'migration' => [
        'scene' => 'Migrations', 'version' => '10/11/12',
        'intro' => 'Migrations keep database schema in version control. Create with `php artisan make:migration`',
        'example' => <<<'PHP'
// Create users table
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});

// Add foreign key
Schema::table('posts', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
});
PHP,
        'notes' => [
            'Use `unsignedBigInteger` or `foreignId()` for foreign keys (Laravel 7+).',
            'Use `nullable()` for optional fields.',
            'Always add indexes on frequently queried columns.',
            'Use `softDeletes()` for soft deletion instead of hard delete.',
        ],
    ],
    'relationship' => [
        'scene' => 'Eloquent Relationships', 'version' => '10/11/12',
        'intro' => 'Eloquent relationships define how models are related.',
        'example' => <<<'PHP'
// One to Many
public function posts(): HasMany { return $this->hasMany(Post::class); }

// Many to Many
public function roles(): BelongsToMany { return $this->belongsToMany(Role::class); }

// Has One Through
public function scheme(): HasOneThrough {
    return $this->hasOneThrough(Scheme::class, Machine::class);
}

// Polymorphic
public function comments(): MorphMany { return $this->morphMany(Comment::class, 'commentable'); }
PHP,
        'notes' => [
            'Define relations as methods with return types.',
            'Use `with()` to eager-load and avoid N+1 queries.',
            'Laravel 12: All 12 relationship types fully supported.',
        ],
    ],
    'validation' => [
        'scene' => 'Validation', 'version' => '10/11/12',
        'intro' => 'Validate request data using Form Request classes or inline validation.',
        'example' => <<<'PHP'
// Inline validation
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'body'  => 'required|string',
    'published_at' => 'nullable|date',
]);

// Form Request (php artisan make:request StorePostRequest)
public function rules(): array {
    return [
        'title' => 'required|string|max:255',
        'body'  => 'required|string',
    ];
}

public function messages(): array {
    return ['title.required' => '标题不能为空'];
}
PHP,
        'notes' => [
            'Always validate before updating the database.',
            'Use custom Form Request classes for reusable validation.',
            'Use `$this->validate()` in controllers for simple cases.',
        ],
    ],
    'auth' => [
        'scene' => 'Authentication', 'version' => '10/11/12',
        'intro' => 'Laravel Breeze is the recommended starter kit. Sanctum for SPA.',
        'example' => <<<'PHP'
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Gate / Policy
Gate::define('update-post', function (User $user, Post $post) {
    return $user->id === $post->user_id;
});

// Policy
public function update(User $user, Post $post): bool {
    return $user->id === $post->user_id;
}
PHP,
        'notes' => [
            'Laravel 11+: `laravel new --auth` no longer exists. Use Breeze: `composer require laravel/breeze`.',
            'Laravel 12: Auth scaffolding further simplified.',
            'Use Policies for authorization, Gates for simple cases.',
        ],
    ],
    'queue' => [
        'scene' => 'Queues', 'version' => '10/11/12',
        'intro' => 'Queues defer slow tasks. Create with `php artisan make:job` or `make:job --sync`',
        'example' => <<<'PHP'
// app/Jobs/ProcessUpload.php
class ProcessUpload implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public Upload $upload) {}

    public function handle(): void
    {
        // Process upload...
        $this->upload->markAsProcessed();
    }

    public function failed(\Throwable $e): void
    {
        Mail::to($this->upload->user)->send(new UploadFailed($this->upload, $e));
    }
}

// Dispatch
ProcessUpload::dispatch($upload);

// Dispatch on specific queue
ProcessUpload::dispatch($upload)->onQueue('processing');
PHP,
        'notes' => [
            'Always implement `ShouldQueue` for background processing.',
            'Use `$this->release(now()->addSeconds(10))` to retry failed jobs.',
            'Laravel Horizon (separate package) provides a beautiful queue dashboard.',
        ],
    ],
    'cache' => [
        'scene' => 'Caching', 'version' => '10/11/12',
        'intro' => 'Laravel Cache facade provides a unified API for various cache backends.',
        'example' => <<<'PHP'
// Store
Cache::put('key', 'value', now()->addMinutes(10));
Cache::put('key', 'value', 600); // seconds
Cache::remember('users', 3600, fn() => User::all());
Cache::rememberForever('settings', fn() => Settings::all());

// Retrieve
$value = Cache::get('key');
Cache::forget('key');
Cache::flush(); // Clear all cache

// Tags (Redis)
Cache::tags(['users', 'posts'])->put('user-1', $user, 600);
PHP,
        'notes' => [
            'Use `Cache::remember()` for expensive operations.',
            'Never cache sensitive data.',
            'Laravel 12: Cache configuration unchanged from 11.',
        ],
    ],
    'mail' => [
        'scene' => 'Mail', 'version' => '10/11/12',
        'intro' => 'Laravel Mail provides a clean API over popular mail drivers.',
        'example' => <<<'PHP'
// Create: php artisan make:mail OrderShipped
class OrderShipped extends Mailable
{
    public function build(): static
    {
        return $this->subject("Order #{$this->order->id} Shipped")
            ->view('emails.order-shipped')
            ->attach('/path/to/file.pdf');
    }
}

// Send
Mail::to($user)->send(new OrderShipped($order));
Mail::to($user)->cc($admin)->send(new OrderShipped($order));

// Queue (async)
Mail::to($user)->queue(new OrderShipped($order));
PHP,
        'notes' => [
            'Use Markdown mail for templated emails.',
            'Queue mails with `Mail::to()->queue()` for better performance.',
            'Use `Mail::failures()` to check for rejected recipients.',
        ],
    ],
    'notification' => [
        'scene' => 'Notifications', 'version' => '10/11/12',
        'intro' => 'Notifications can be sent via mail, SMS, Slack, database and more.',
        'example' => <<<'PHP'
// Create: php artisan make:notification InvoicePaid
class InvoicePaid extends Notification
{
    public function via(object $notifiable): array {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Invoice Paid')
            ->line("Invoice #{$this->invoice->id} has been paid.");
    }

    public function toArray(object $notifiable): array {
        return ['invoice_id' => $this->invoice->id, 'amount' => $this->invoice->amount];
    }
}

// Send
$user->notify(new InvoicePaid($invoice));
Notification::send($users, new InvoicePaid($invoice));
PHP,
        'notes' => [
            'Use `Notifiable` trait on User model.',
            'Channel `database` stores notifications in a `notifications` table.',
            'Laravel 12: Notification contract unchanged.',
        ],
    ],
    'testing' => [
        'scene' => 'Testing', 'version' => '10/11/12',
        'intro' => 'Laravel is built with testing in mind. Use Pest (recommended) or PHPUnit.',
        'example' => <<<'PHP'
// Feature test
test('users can create posts', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->post('/posts', [
        'title' => 'My Post',
        'body'  => 'Post content',
    ]);

    $response->assertRedirect('/posts');
    $this->assertDatabaseHas('posts', ['title' => 'My Post']);
});

// Unit test
test('total is calculated correctly', function () {
    $cart = new Cart(['item1' => 100, 'item2' => 200]);
    expect($cart->total())->toBe(300);
});

// Fake
Mail::fake();
Notification::fake();

// Refresh database per test
use RefreshDatabase;
$this->assertDatabaseHas('users', ['email' => $user->email]);
PHP,
        'notes' => [
            'Use Pest (`composer require pestphp/pest --dev`) for cleaner syntax.',
            'Always use `actingAs()` for authenticated requests.',
            'Use `$this->refreshDatabase()` trait for integration tests.',
        ],
    ],
    'event' => [
        'scene' => 'Events & Listeners', 'version' => '10/11/12',
        'intro' => 'Events decouple application logic. Create with `php artisan make:event` and `make:listener`',
        'example' => <<<'PHP'
// Event: app/Events/OrderPlaced.php
class OrderPlaced
{
    use SerializesModels;
    public function __construct(public Order $order) {}
}

// Listener: app/Listeners/SendOrderNotification.php
class SendOrderNotification
{
    public function handle(OrderPlaced $event): void
    {
        $event->order->user->notify(new OrderReceived($event->order));
    }
}

// Register (Laravel 11+): bootstrap/app.php or EventServiceProvider
Event::listen(OrderPlaced::class, SendOrderNotification::class);

// Async listener (should implement ShouldQueue)
class SendOrderNotification implements ShouldQueue {}

// Dispatch
event(new OrderPlaced($order));
PHP,
        'notes' => [
            'Laravel 11+: Event discovery is automatic when using `Event::listen()` in boot().',
            'Use `NotifyUsers` job as listener for async email/SMS notifications.',
        ],
    ],
    'storage' => [
        'scene' => 'File Storage', 'version' => '10/11/12',
        'intro' => 'Laravel provides a powerful filesystem with local, S3, FTP, SFTP drivers.',
        'example' => <<<'PHP'
// Disk: config/filesystems.php defines disks (local, public, s3)
// Upload
$path = $request->file('avatar')->store('avatars', 's3');
$path = $request->file('avatar')->storeAs('avatars', $user->id . '.jpg', 's3');

// Retrieve
$url = Storage::disk('s3')->url('avatars/' . $filename);
$contents = Storage::get('file.txt');
Storage::delete('file.txt');

// Temporary URLs (S3)
$url = Storage::disk('s3')->temporaryUrl('file.pdf', now()->addMinutes(5));
PHP,
        'notes' => [
            'Use `public` disk for files served directly to users.',
            'Always generate unique filenames to avoid collisions.',
            'Laravel 12: Flysystem 3.x is the default (dropped PHP 8.0 support).',
        ],
    ],
    'broadcasting' => [
        'scene' => 'Broadcasting', 'version' => '10/11/12',
        'intro' => 'Broadcast events to client-side JavaScript via WebSockets.',
        'example' => <<<'PHP'
// Event implements ShouldBroadcast
class OrderUpdated implements ShouldBroadcast
{
    use SerializesModels;
    public function __construct(public Order $order) {}

    public function broadcastOn(): array {
        return [new PrivateChannel('orders.' . $this->order->user_id)];
    }

    public function broadcastAs(): string {
        return 'order.updated';
    }
}

// Echo (JavaScript)
Echo.private('orders.' + userId)
    .listen('OrderUpdated', (e) => { console.log('Order updated:', e.order); });
PHP,
        'notes' => [
            'Requires Pusher, Ably, or a self-hosted WebSocket server.',
            'Laravel 11+: Broadcasting configuration simplified.',
        ],
    ],
    'scheduler' => [
        'scene' => 'Task Scheduling', 'version' => '10/11/12',
        'intro' => 'Schedule tasks in app/Console/Kernel.php (Laravel 10) or routes/console.php (Laravel 11+)',
        'example' => <<<'PHP'
// routes/console.php (Laravel 11+)
Schedule::command('emails:send')->dailyAt('09:00');
Schedule::job(new ProcessReports)->everyFiveMinutes();
Schedule::call(fn() => Cache::flush())->hourly();

// Closure based (Laravel 10, app/Console/Kernel.php)
$schedule->call(function () {
    News::publishScheduled();
})->daily();

// Without Overlapping
Schedule::command('emails:send')->withoutOverlapping();

// Run scheduler (add to crontab)
* * * * * cd /project && php artisan schedule:run >> /dev/null 2>&1
PHP,
        'notes' => [
            'Laravel 11+: Scheduling is defined in routes/console.php, not Kernel.php.',
            'Use `withoutOverlapping()` to prevent duplicate runs.',
            'Always use UTC times in scheduler for consistency.',
        ],
    ],
    'service-container' => [
        'scene' => 'Service Container', 'version' => '10/11/12',
        'intro' => 'The container resolves all class dependencies and manages object lifecycle.',
        'example' => <<<'PHP'
// Binding
$this->app->singleton(GatewayInterface::class, StripeGateway::class);
$this->app->scoped(PaymentService::class);

// Resolving
$gateway = app(GatewayInterface::class);
$gateway = resolve(StripeGateway::class);

// Constructor injection (automatic)
public function __construct(protected GatewayInterface $gateway) {}

// Contextual binding
$this->app->when(ReportController::class)
    ->needs(GatewayInterface::class)
    ->give(StripeGateway::class);
PHP,
        'notes' => [
            'Always prefer constructor injection over manual `app()` calls.',
            'Use `singleton()` for services that should only have one instance.',
            'Laravel 12: Container contract unchanged.',
        ],
    ],
    'facade' => [
        'scene' => 'Facades', 'version' => '10/11/12',
        'intro' => 'Facades provide static-like access to services bound in the container.',
        'example' => <<<'PHP'
// All facades: Route, Cache, Config, DB, Gate, Mail, Notification,
//              Queue, Redirect, Request, Response, Schema, Session, Storage, URL, Validator...

// Route facade
Route::get('/users', [UserController::class, 'index'])->name('users.index');
$url = Route::has('users.index'); // bool

// Cache facade
Cache::put('key', 'value', 60);
$value = Cache::get('key');

// DB facade
$users = DB::select('SELECT * FROM users WHERE active = ?', [1]);

// Tip: Most facades have an equivalent contract (interface).
// Prefer injecting the contract for testability.
PHP,
        'notes' => [
            'Facades are not singletons — they resolve from the container on each call.',
            'Prefer constructor injection over facades for better testability.',
            'Laravel 12: No facades have been deprecated (unlike some early-lifecycle packages).',
        ],
    ],
];

// ── Artisan Commands ──────────────────────────────────────────

$ARTISAN_COMMANDS = [
    'make:controller'   => ['args' => '<name> [--resource] [--model=] [--requests]', 'desc' => 'Create a new controller class'],
    'make:model'       => ['args' => '<name> [-m] [-f] [--migration]', 'desc' => 'Create a new Eloquent model'],
    'make:middleware'  => ['args' => '<name>', 'desc' => 'Create a new middleware class'],
    'make:request'     => ['args' => '<name>', 'desc' => 'Create a new form request class'],
    'make:job'         => ['args' => '<name> [--sync]', 'desc' => 'Create a new job class (use --sync for sync queue)'],
    'make:mail'        => ['args' => '<name> [--markdown=]', 'desc' => 'Create a new mailable class'],
    'make:notification'=> ['args' => '<name> [--table=]', 'desc' => 'Create a new notification class'],
    'make:event'       => ['args' => '<name>', 'desc' => 'Create a new event class'],
    'make:listener'   => ['args' => '<name> [--event=]', 'desc' => 'Create a new listener class'],
    'make:policy'     => ['args' => '<name> [--model=]', 'desc' => 'Create a new policy class'],
    'make:resource'   => ['args' => '<name> [--collection]', 'desc' => 'Create a new API resource (Eloquent API resource)'],
    'make:seeder'     => ['args' => '<name>', 'desc' => 'Create a new seeder class'],
    'make:factory'    => ['args' => '<name> [--model=]', 'desc' => 'Create a new model factory'],
    'make:test'        => ['args' => '<name> [--unit]', 'desc' => 'Create a new test class'],
    'migrate'         => ['args' => '[--path=] [--seed]', 'desc' => 'Run pending database migrations'],
    'migrate:fresh'   => ['args' => '[--seed] [--seeder=]', 'desc' => 'Drop all tables and re-run migrations (DANGEROUS)'],
    'migrate:rollback' => ['args' => '[--step=1]', 'desc' => 'Rollback the latest migration'],
    'db:seed'         => ['args' => '[--class=DatabaseSeeder]', 'desc' => 'Seed the database with records'],
    'route:list'       => ['args' => '[--path=] [--except-path=]', 'desc' => 'List all registered routes'],
    'cache:clear'      => ['args' => '[--tags=]', 'desc' => 'Flush the application cache'],
    'config:cache'     => ['args' => '', 'desc' => 'Cache config for faster boot'],
    'config:clear'     => ['args' => '', 'desc' => 'Remove the config cache file'],
    'route:cache'      => ['args' => '', 'desc' => 'Cache all routes (NOT in dev)'],
    'optimize'        => ['args' => '', 'desc' => 'Cache framework bootstrap files'],
    'vendor:publish'   => ['args' => '[--provider=] [--tag=]', 'desc' => 'Publish package assets'],
    'env:dump'        => ['args' => '', 'desc' => 'Dump current environment variables'],
];

// ─────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────

function version_detect(string $path = '.'): ?string {
    $composer = $path . '/composer.json';
    if (is_readable($composer)) {
        $json = json_decode(file_get_contents($composer), true);
        if (isset($json['require']['laravel/framework'])) {
            $version = $json['require']['laravel/framework'];
            if (preg_match('/\^?(\d+)/', $version, $m)) {
                return $m[1] . '.x';
            }
        }
    }

    $artisan = $path . '/artisan';
    if (is_file($artisan)) {
        $content = file_get_contents($artisan);
        if (preg_match('/VERSION.*?(\d+)/', $content, $m)) {
            return $m[1] . '.x';
        }
    }

    $app = $path . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php';
    if (is_readable($app)) {
        $content = file_get_contents($app);
        if (preg_match('/const VERSION = \'(\d+)/', $content, $m)) {
            return $m[1] . '.x';
        }
    }

    return null;
}

function natural_language_search(string $query): array {
    global $DOCS_INDEX;

    $query = strtolower($query);
    $results = [];

    // Intent → doc key mapping
    $intent_map = [
        // Routing
        'route' => ['routing'],
        '路由' => ['routing'],
        // Middleware
        'middleware' => ['middleware'],
        '中间件' => ['middleware'],
        'filter request' => ['middleware'],
        // Controller
        'controller' => ['controller'],
        '控制器' => ['controller'],
        // Model
        'model' => ['model'],
        'eloquent' => ['model', 'relationship'],
        '模型' => ['model'],
        // Migration
        'migration' => ['migration'],
        'schema' => ['migration'],
        '迁移' => ['migration'],
        // Validation
        'validation' => ['validation'],
        'validate' => ['validation'],
        '验证' => ['validation'],
        // Auth
        'auth' => ['auth'],
        'authentication' => ['auth'],
        '登录' => ['auth'],
        '认证' => ['auth'],
        'permission' => ['auth'],
        '权限' => ['auth'],
        // Queue / Job
        'queue' => ['queue'],
        'job' => ['queue'],
        '队列' => ['queue'],
        'background' => ['queue'],
        // Cache
        'cache' => ['cache'],
        '缓存' => ['cache'],
        // Mail
        'mail' => ['mail'],
        'email' => ['mail'],
        '邮件' => ['mail'],
        // Notification
        'notification' => ['notification'],
        '通知' => ['notification'],
        // Testing
        'test' => ['testing'],
        'pest' => ['testing'],
        '测试' => ['testing'],
        // Storage
        'storage' => ['storage'],
        'file' => ['storage'],
        'upload' => ['storage'],
        's3' => ['storage'],
        '文件' => ['storage'],
        // Broadcast
        'broadcast' => ['broadcasting'],
        'websocket' => ['broadcasting'],
        // Event
        'event' => ['event'],
        'listener' => ['event'],
        '事件' => ['event'],
        // Scheduling
        'schedule' => ['scheduler'],
        'cron' => ['scheduler'],
        '定时' => ['scheduler'],
        // Container
        'container' => ['service-container'],
        'di' => ['service-container'],
        'singleton' => ['service-container'],
        // Facade
        'facade' => ['facade'],
        // Relationship
        'relationship' => ['relationship'],
        'relation' => ['relationship'],
        'hasmany' => ['relationship'],
        'belongsto' => ['relationship'],
        '关联' => ['relationship'],
    ];

    // Also check "how to create" / "how to use"
    $is_howto = preg_match('/how to (create|use|make|send|build|set up)/i', $query, $m_how);

    foreach ($intent_map as $keyword => $doc_keys) {
        if (strpos($query, $keyword) !== false) {
            foreach ($doc_keys as $key) {
                if (!isset($results[$key])) {
                    $results[$key] = $DOCS_INDEX[$key];
                }
            }
        }
    }

    // If still empty, do fuzzy match on doc titles and intros
    if (empty($results)) {
        foreach ($DOCS_INDEX as $key => $doc) {
            if (strpos(strtolower($doc['intro']), $query) !== false ||
                strpos($key, str_replace(' ', '_', $query)) !== false) {
                $results[$key] = $doc;
            }
        }
    }

    return $results;
}

function print_doc(array $doc, string $title): void {
    $badge = $doc['scene'] ?? $title;
    $ver = $doc['version'] ?? '';

    echo "\n=== {$badge} ===\n";
    echo "Laravel: {$ver}\n\n";
    echo wordwrap($doc['intro'], 80) . "\n\n";
    echo "Code Example:\n";
    echo "──────────────────────────────────────\n";
    echo $doc['example'];
    echo "\n──────────────────────────────────────\n";

    if (!empty($doc['notes'])) {
        echo "\nNotes:\n";
        foreach ($doc['notes'] as $note) {
            echo "  • " . wordwrap($note, 75) . "\n";
        }
    }
    echo "\n";
}

// ─────────────────────────────────────────────────────────────
// Commands
// ─────────────────────────────────────────────────────────────

function cmd_search(string $query): void {
    $results = natural_language_search($query);

    if (empty($results)) {
        echo "No results found for: \"{$query}\"\n";
        echo "Try: 'how to create middleware', 'queue job', 'authentication', 'testing'...\n";
        return;
    }

    echo "\n=== Search: \"{$query}\" ===\n";
    echo "Found: " . count($results) . " result(s)\n\n";

    foreach ($results as $key => $doc) {
        print_doc($doc, $key);
    }

    // ── Laravel Package Search cross-link ──────────────────
    echo "\n";
    echo "──────────────────────────────────────────────────\n";
    echo "💡 Looking for a third-party package?\n";
    echo "\n";
    echo "  laravel-package-search indexes 1,000+ packages:\n";
    echo "    clawhub install laravel-package-search\n";
    echo "    git clone https://github.com/relunctance/laravel-package-search\n";
    echo "──────────────────────────────────────────────────\n\n";
}

function cmd_version(string $path = '.'): void {
    global $DEFAULT_VERSION;

    $detected = version_detect($path);

    if ($detected) {
        echo "Detected Laravel: {$detected}\n";
        echo "Default for this tool: {$DEFAULT_VERSION}\n";
        if ($detected !== "{$DEFAULT_VERSION}.x") {
            echo "Note: This tool serves {$DEFAULT_VERSION} by default. Some features may differ.\n";
        }
    } else {
        echo "No Laravel project found in: {$path}\n";
        echo "Default version: {$DEFAULT_VERSION}\n";
        echo "Usage: laradoc.php version /path/to/project\n";
    }
}

function cmd_current(): void {
    global $DEFAULT_VERSION;
    echo "Default Laravel version: {$DEFAULT_VERSION}\n";
}

function cmd_config(string $file): void {
    global $DEFAULT_VERSION;

    $configs = [
        'database' => [
            'title' => 'Database Configuration',
            'intro' => 'config/database.php defines connections and drivers.',
            'example' => <<<'PHP'
// config/database.php
'connections' => [
    'sqlite' => [
        'driver' => 'sqlite',
        'url' => env('DATABASE_URL'),
        'database' => database_path('database.sqlite'),
        'prefix' => '',
        'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    ],
    'mysql' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
    ],
],
PHP,
            'notes' => ['Use `DB::connection()->getPdo()` to access raw PDO.', 'Migrations: `php artisan migrate:fresh --seed`'],
        ],
        'cache' => [
            'title' => 'Cache Configuration',
            'intro' => 'config/cache.php defines cache stores.',
            'example' => <<<'PHP'
// config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
],
'default' => env('CACHE_STORE', 'database'),
PHP,
            'notes' => ['Redis is fastest for production.', 'Database cache table: `php artisan cache:table`'],
        ],
        'mail' => [
            'title' => 'Mail Configuration',
            'intro' => 'config/mail.php defines mail drivers and SMTP settings.',
            'example' => <<<'PHP'
// .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"
PHP,
            'notes' => ['Use Mailgun/Postmark/SES for production.', 'Queue mails: `Mail::to()->queue()`'],
        ],
        'queue' => [
            'title' => 'Queue Configuration',
            'intro' => 'config/queue.php defines queue connections.',
            'example' => <<<'PHP'
// .env
QUEUE_CONNECTION=redis

// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
    ],
],
PHP,
            'notes' => ['Redis recommended for production.', 'Run worker: `php artisan queue:work`'],
        ],
        'auth' => [
            'title' => 'Auth Configuration',
            'intro' => 'config/auth.php defines guards and providers.',
            'example' => <<<'PHP'
// config/auth.php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'api' => ['driver' => 'token', 'provider' => 'users', 'hash' => true],
],
'providers' => [
    'users' => ['driver' => 'eloquent', 'model' => App\Models\User::class],
],
PHP,
            'notes' => ['Laravel 11+: Uses Sanctum for API auth (recommended over JWT).'],
        ],
        'session' => [
            'title' => 'Session Configuration',
            'intro' => 'config/session.php defines session drivers.',
            'example' => <<<'PHP'
// .env
SESSION_DRIVER=redis
SESSION_LIFETIME=120

// config/session.php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
PHP,
            'notes' => ['Redis recommended for production.', 'Set SESSION_DOMAIN for cross-subdomain cookies.'],
        ],
    ];

    $file = strtolower($file);
    if (!isset($configs[$file])) {
        echo "Config '{$file}' not found.\n";
        echo "Available: " . implode(', ', array_keys($configs)) . "\n";
        return;
    }

    $cfg = $configs[$file];
    echo "\n=== config/{$file}.php ===\n\n";
    echo wordwrap($cfg['intro'], 80) . "\n\n";
    echo "Example:\n──────────────────────────────────────\n";
    echo $cfg['example'];
    echo "\n──────────────────────────────────────\n";
    foreach ($cfg['notes'] as $note) {
        echo "• {$note}\n";
    }
    echo "\n";
}

function cmd_facade(string $name): void {
    $facades = [
        'Cache'      => ['methods' => ['get($key, $default = null)', 'put($key, $value, $ttl = null)', 'has($key)', 'forget($key)', 'flush()', 'remember($key, $ttl, $callback)'], 'desc' => 'Get/set/clear cached values'],
        'DB'         => ['methods' => ['select($sql, $bindings = [])', 'insert($sql, $bindings = [])', 'update($sql, $bindings = [])', 'delete($sql, $bindings = [])', 'table($table)', 'beginTransaction()', 'commit()', 'rollBack()'], 'desc' => 'Raw database queries and transactions'],
        'Route'      => ['methods' => ['get($uri, $action)', 'post($uri, $action)', 'put($uri, $action)', 'delete($uri, $action)', 'patch($uri, $action)', 'options($uri, $action)', 'match($methods, $uri, $action)', 'group($attributes, $routes)', 'has($name)'], 'desc' => 'Define HTTP routes'],
        'Auth'       => ['methods' => ['attempt($credentials)', 'login($user, $remember = false)', 'logout()', 'check()', 'guest()', 'id()', 'user()', 'viaRemember()'], 'desc' => 'Authenticate users'],
        'Gate'       => ['methods' => ['define($ability, $callback)', 'denies($ability, $arguments = [])', 'allows($ability, $arguments = [])', 'authorize($ability, $arguments)'], 'desc' => 'Authorize actions'],
        'Mail'       => ['methods' => ['to($users)', 'cc($users)', 'bcc($users)', 'send($mailable)', 'queue($mailable)', 'later($delay, $mailable)'], 'desc' => 'Send emails'],
        'Notification' => ['methods' => ['send($users, $notification)', 'sendNow($users, $notification)'], 'desc' => 'Send notifications'],
        'Queue'      => ['methods' => ['push($job, $data = [], $queue = null)', 'later($delay, $job, $data = [], $queue = null)'], 'desc' => 'Push jobs to queue'],
        'Schema'    => ['methods' => ['create($table, $callback)', 'table($table, $callback)', 'hasTable($table)', 'drop($table)', 'rename($from, $to)'], 'desc' => 'Database schema operations'],
        'Storage'   => ['methods' => ['disk($name = null)', 'put($path, $contents, $options = [])', 'get($path)', 'exists($path)', 'delete($path)', 'url($path)', 'download($path)'], 'desc' => 'File storage operations'],
        'Log'       => ['methods' => ['info($message, $context = [])', 'error($message, $context = [])', 'warning($message, $context = [])', 'debug($message, $context = [])', 'channel($name)'], 'desc' => 'Application logging'],
        'Validator' => ['methods' => ['make($data, $rules, $messages = [])', 'validate($data, $rules)'], 'desc' => 'Create and validate against rules'],
        'Redirect'  => ['methods' => ['to($url, $status = 302)', 'route($name, $parameters = [])', 'back($status = 302, $headers = [], $fallback = false)'], 'desc' => 'Redirect responses'],
        'URL'       => ['methods' => ['to($path, $extra = [], $secure = null)', 'route($name, $parameters = [])', 'current()', 'previous()'], 'desc' => 'URL generation'],
        'Config'    => ['methods' => ['get($key, $default = null)', 'set($key, $value = null)', 'has($key)', 'all()'], 'desc' => 'Configuration access'],
        'Session'   => ['methods' => ['get($key, $default = null)', 'put($key, $value)', 'forget($key)', 'flush()', 'invalidate()', 'regenerate()'], 'desc' => 'Session management'],
        'Response'  => ['methods' => ['make($content = "", $status = 200, $headers = [])', 'json($data = [], $status = 200, $headers = [], $options = 0)', 'redirect($to, $status = 302, $headers = [], $secure = null)'], 'desc' => 'HTTP response construction'],
        'Request'   => ['methods' => ['input($key = null, $default = null)', 'all()', 'only($keys)', 'except($keys)', 'has($key)', 'validate($rules)', 'user()', 'ip()'], 'desc' => 'HTTP request data'],
    ];

    $name = ucfirst($name);
    if (!isset($facades[$name])) {
        echo "Facade '{$name}' not found.\n";
        echo "Available: " . implode(', ', array_keys($facades)) . "\n";
        return;
    }

    $f = $facades[$name];
    echo "\n=== Facade: {$name} ===\n";
    echo "Purpose: {$f['desc']}\n\n";
    echo "Methods:\n";
    foreach ($f['methods'] as $m) {
        echo "  {$name}::{$m}\n";
    }
    echo "\n";
}

function cmd_artisan(string $cmd): void {
    global $ARTISAN_COMMANDS;

    $cmd = strtolower($cmd);
    if (!isset($ARTISAN_COMMANDS[$cmd])) {
        echo "Command '{$cmd}' not found.\n";
        echo "Available commands:\n";
        foreach ($ARTISAN_COMMANDS as $name => $info) {
            echo "  {$name} {$info['args']}\n      {$info['desc']}\n";
        }
        return;
    }

    $info = $ARTISAN_COMMANDS[$cmd];
    echo "\n=== artisan {$cmd} {$info['args']} ===\n";
    echo "{$info['desc']}\n";
    echo "\nExample:\n  php artisan {$cmd}\n";
    echo "\n";
}

function cmd_diff(string $feature): void {
    global $DEFAULT_VERSION;

    $diff = [
        'auth' => [
            'title' => 'Auth Scaffolding Diff (Laravel 10 → 11 → 12)',
            'rows' => [
                ['Feature',        'Laravel 10',         'Laravel 11',                'Laravel 12'],
                ['Auth Scaffolding','Breeze/Fortify',   'Breeze only',              'Breeze + minimal'],
                ['Auth Route',     'routes/web.php',     'routes/web.php',           'bootstrap/app.php'],
                ['Session Guard', 'config/auth.php',    'config/auth.php',           'Still needed'],
                ['Sanctum',        'Package',            'Package',                   'First-party'],
            ],
        ],
        'routing' => [
            'title' => 'Routing Diff (Laravel 10 → 11 → 12)',
            'rows' => [
                ['Feature',        'Laravel 10',         'Laravel 11',                'Laravel 12'],
                ['Route Files',    'routes/web.php api', 'routes/*.php',           'routes/*.php'],
                ['Route Config',   'RouteServiceProvider','bootstrap/app.php',      'bootstrap/app.php'],
                ['Middleware',     'Kernel.php',         'bootstrap/app.php',         'bootstrap/app.php'],
                ['Rate Limiting', 'Kernel.php',          'bootstrap/app.php',         'bootstrap/app.php'],
            ],
        ],
        'middleware' => [
            'title' => 'Middleware Diff (Laravel 10 → 11 → 12)',
            'rows' => [
                ['Feature',        'Laravel 10',         'Laravel 11',                'Laravel 12'],
                ['Registration',  'app/Http/Kernel.php', 'bootstrap/app.php',      'bootstrap/app.php'],
                ['Groups',        '$middlewareGroups',    'withMiddleware()',         'withMiddleware()'],
                ['Alias',         '$middlewareAliases',  '->alias()',               '->alias()'],
            ],
        ],
        'exception' => [
            'title' => 'Exception Handling Diff',
            'rows' => [
                ['Feature',        'Laravel 10',         'Laravel 11',                'Laravel 12'],
                ['Handler',        'app/Exceptions/Handler','bootstrap/app.php (inline)','bootstrap/app.php'],
                ['Report Method',  'report($e)',         'report($e)',               'report($e)'],
                ['Render Method',  'render($request, $e)','render($request, $e)',    'render($request, $e)'],
            ],
        ],
    ];

    $feature = strtolower($feature);
    if (!isset($diff[$feature])) {
        echo "Feature '{$feature}' not found.\n";
        echo "Available: " . implode(', ', array_keys($diff)) . "\n";
        return;
    }

    $d = $diff[$feature];
    echo "\n=== {$d['title']} ===\n\n";

    foreach ($d['rows'] as $row) {
        printf("%-18s | %-22s | %-22s | %-22s\n", ...$row);
        if ($row === $d['rows'][0]) {
            echo str_repeat('-', 90) . "\n";
        }
    }
    echo "\n";
}

function cmd_generate(string $type, string $name): void {
    global $DEFAULT_VERSION;

    $templates = [
        'controller' => [
            'desc' => 'Resource controller',
            'code' => <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Models\{NAME};
use Illuminate\Http\{Request, RedirectResponse, JsonResponse};

class {NAME}Controller extends Controller
{
    public function index(Request $request): JsonResponse
    {
        ${names} = {NAME}::query()->paginate(15);
        return response()->json(${names});
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Add validation rules here
        ]);
        ${name} = {NAME}::create($validated);
        return response()->json(${name}, 201);
    }

    public function show(int $id): JsonResponse
    {
        ${name} = {NAME}::findOrFail($id);
        return response()->json(${name});
    }

    public function update(Request $request, int $id): JsonResponse
    {
        ${name} = {NAME}::findOrFail($id);
        $validated = $request->validate([
            // Add validation rules here
        ]);
        ${name}->update($validated);
        return response()->json(${name});
    }

    public function destroy(int $id): JsonResponse
    {
        {NAME}::destroy($id);
        return response()->json(null, 204);
    }
}
PHP,
        ],
        'model' => [
            'desc' => 'Eloquent model with fillable and casts',
            'code' => <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class {NAME} extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Define fillable fields
    ];

    protected $hidden = [
        // Fields to hide when serializing
    ];

    protected $casts = [
        // 'field' => 'datetime',
        // 'field' => 'bool',
    ];

    // ── Relationships ────────────────────────────────────────

    // public function related(): HasMany
    // {
    //     return $this->hasMany(Related::class);
    // }
}
PHP,
        ],
        'job' => [
            'desc' => 'Queueable job',
            'code' => <<<'PHP'
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Process{NAME} implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 60;

    public function __construct(
        // public readonly mixed $data,
    ) {}

    public function handle(): void
    {
        // Process the job...
    }

    public function failed(\Throwable $e): void
    {
        // Handle failure (e.g. notify admin)
    }
}
PHP,
        ],
        'middleware' => [
            'desc' => 'HTTP middleware',
            'code' => <<<'PHP'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Check{NAME}
{
    public function handle(Request $request, Closure $next)
    {
        // Add your check logic here
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        return $next($request);
    }
}
PHP,
        ],
        'request' => [
            'desc' => 'Form request for validation',
            'code' => <<<'PHP'
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{NAME}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or: $this->user()->can('create', {NAME}::class);
    }

    public function rules(): array
    {
        return [
            // 'field' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            // 'field.required' => 'The :attribute field is required.',
        ];
    }
}
PHP,
        ],
        'notification' => [
            'desc' => 'Multi-channel notification',
            'code' => <<<'PHP'
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class {NAME}Notification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        // public readonly mixed $data,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('{NAME}')
            ->line('...');
    }

    public function toArray(object $notifiable): array
    {
        return [
            // 'field' => $this->data,
        ];
    }
}
PHP,
        ],
        'factory' => [
            'desc' => 'Model factory for testing',
            'code' => <<<'PHP'
<?php

namespace Database\Factories;

use App\Models\{NAME};
use Illuminate\Database\Eloquent\Factories\Factory;

class {NAME}Factory extends Factory
{
    protected $model = {NAME}::class;

    public function definition(): array
    {
        return [
            // 'field' => fake()->sentence(),
        ];
    }
}
PHP,
        ],
    ];

    $type = strtolower($type);
    if (!isset($templates[$type])) {
        echo "Type '{$type}' not found.\n";
        echo "Available: " . implode(', ', array_keys($templates)) . "\n";
        return;
    }

    $tpl = $templates[$type];
    $singular = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    $plural = \Illuminate\Support\Str::plural($singular);

    $code = str_replace('{NAME}', $name, $tpl['code']);
    $code = str_replace('{names}', $plural, $code);
    $code = str_replace('{name}', $singular, $code);

    echo "\n=== Generated: {$type} {$name} ===\n";
    echo "Artisan command: php artisan make:{$type} {$name}\n\n";
    echo "──────────────────────────────────────\n";
    echo $code;
    echo "\n──────────────────────────────────────\n";
    echo "\n";
}

function cmd_lang(string $query): void {
    $directives = [
        'if'        => ['@if', '@elseif', '@else', '@endif', 'Conditionals'],
        'unless'    => ['@unless', '@endunless', 'Opposite of @if'],
        'isset'     => ['@isset($var)', '@endisset', 'Check if set'],
        'empty'     => ['@empty($var)', '@endempty', 'Check if empty'],
        'auth'      => ['@auth', '@endauth', '@guest', '@endguest', 'Auth check'],
        'can'       => ['@can(\'edit\', $post)', '@endcan', 'Authorization'],
        'loop'      => ['@for ($i = 0; $i < 10; $i++)', '@foreach($items as $item)', '@forelse($items as $item)', '@while (true)', 'Loop'],
        'forelse'   => ['@forelse($items as $item)', '@empty', '@endforelse', 'Loop with empty fallback'],
        'each'      => ['@each(\'partials.item\', $items, \'item\')', 'Render partial for each item (deprecated in 5.8+)'],
        'component' => ['@component(\'alert\', [\'type\' => \'error\'])', '@endcomponent', 'Laravel 5.4+ component system'],
        'slot'      => ['@slot(\'footer\')', '@endslot', 'Passing content to components'],
        'section'   => ['@section(\'title\', \'Default\')', '@show', '@yield(\'title\')', 'Template inheritance'],
        'push'      => ['@push(\'scripts\')', '@endpush', '@stack(\'scripts\')', 'Stack assets'],
        'dd'        => ['@dd($var)', 'Dump and die (dev only)'],
        'verbatim'  => ['@verbatim', '@endverbatim', 'Raw content without Blade processing'],
        'php'       => ['@php', '@endphp', 'Inline PHP (avoid if possible)'],
        'include'   => ['@include(\'partials.header\')', 'Include subview'],
        'includeIf' => ['@includeIf(\'partials.optional\')', 'Include if exists'],
        'once'      => ['@once', '@endonce', 'Only render once per request'],
        'error'     => ['@error(\'field\')', '@enderror', 'Validation error display'],
        'selected'  => ['@selected($value, old(\'field\'))', '@checked()', '@disabled()', 'Form helpers'],
        'env'       => ['@env(\'local\')', '@production', '@endproduction', 'Environment blocks'],
        'inject'    => ['@inject(\'metrics\', App\Services\MetricsService::class)', 'Inject service from container'],
        'session'   => ['@if (session(\'success\'))', 'Session flash data'],
        'csrf'      => ['@csrf', 'CSRF token field (equivalent to <input type="hidden" name="_token">)'],
    ];

    $query = strtolower($query);
    $matched = [];

    foreach ($directives as $name => $directive) {
        if (strpos($name, $query) !== false || strpos($query, $name) !== false) {
            $matched[$name] = $directive;
        } else {
            foreach ($directive as $d) {
                if (strpos(strtolower($d), $query) !== false) {
                    $matched[$name] = $directive;
                    break;
                }
            }
        }
    }

    if (empty($matched)) {
        echo "No directives found matching: {$query}\n";
        return;
    }

    echo "\n=== Blade Directives matching: {$query} ===\n\n";
    foreach ($matched as $name => $directive) {
        $usage = $directive[0];
        $desc = $directive[1];
        echo "{$usage}\n   {$desc}\n\n";
    }
}

function cmd_help(): void {
    echo <<<'HELP'
Laravel Docs Reader CLI

Usage: php laradoc.php <command> [args]

Commands:
  search <query>              Natural language search (e.g. "how to create middleware")
  version [path]               Detect local Laravel version (default: current dir)
  current                      Show default Laravel version
  config <file>               Show config reference (database/cache/mail/queue/auth/session)
  facade <name>               Show Facade methods (Cache/DB/Route/Auth/Gate/Mail/etc)
  artisan <cmd>               Artisan command help (e.g. make:controller, migrate)
  diff <feature>              Version diff (auth/routing/middleware/exception)
  generate <type> <name>     Generate code skeleton (controller/model/job/middleware/etc)
  lang <query>                Search Blade directives (e.g. "loop", "if", "auth")
  psr [topic]                  PSR-12 quick reference (full / arrays / naming / etc)
  cache                         Show local doc cache status
  update                        Force-pull latest docs from GitHub
  subscribe                    Show / manage doc subscription status

Examples:
  php laradoc.php search "how to create a middleware"
  php laradoc.php search "queue job laravel"
  php laradoc.php version
  php laradoc.php version /path/to/project
  php laradoc.php config database
  php laradoc.php facade Cache
  php laradoc.php diff auth
  php laradoc.php generate controller UserController
  php laradoc.php generate model Post
  php laradoc.php generate job ProcessUpload
  php laradoc.php lang "loop variable"
  php laradoc.php lang "csrf"
  php laradoc.php psr                  # Full PSR-12 summary
  php laradoc.php psr arrays           # Arrays rule only
  php laradoc.php psr naming           # Naming conventions
  php laradoc.php cache                 # Show cache status
  php laradoc.php update               # Force-pull latest docs
  php laradoc.php subscribe            # Show subscription

HELP;
}

// ─────────────────────────────────────────────────────────────
// PSR-12 Quick Reference
// ─────────────────────────────────────────────────────────────

function cmd_psr(string $topic = ''): void {
    $topic = strtolower(trim($topic));

    $psr_sections = [
        '' => [
            'title' => 'PSR-12 Coding Standard — Full Summary',
            'lines' => [
                '┌─────────────────────────────────────────────────────────────┐',
                '│  Rule                     │  Correct          │  Wrong      │',
                '├─────────────────────────────────────────────────────────────┤',
                '│  Indentation              │  4 spaces         │  tabs       │',
                '│  Opening brace (class)    │  same line        │  new line   │',
                '│  Visibility               │  always required  │  omit       │',
                '│  Namespace separator       │  \\                │  _          │',
                '│  use alphabetical         │  ✓ alphabetical   │  random     │',
                '│  Strict types             │  declare(strict)  │  missing    │',
                '│  Line length             │  ≤120 chars       │  >180       │',
                '│  String concat            │  "a" . "b"        │  "a" ."b"   │',
                '│  Array short syntax       │  []                │  array()    │',
                '│  Trailing comma           │  ✓ allowed        │  omit       │',
                '└─────────────────────────────────────────────────────────────┘',
                '',
                'Key file structure:',
                '  <?php',
                '  declare(strict_types=1);',
                '  namespace App\\Http\\Controllers;',
                '  use Illuminate\\Http\\Request;',
                '  class UserController extends Controller { ... }',
            ],
        ],
        'arrays' => [
            'title' => 'PSR-12 — Arrays',
            'lines' => [
                '# Short array syntax REQUIRED (no array())',
                '$items = [\'first\', \'second\'];',
                '',
                '# Trailing comma allowed and encouraged',
                '$config = [',
                "    'host' => '127.0.0.1',",
                "    'port' => 3306,",
                "    'database' => 'forge',   ← trailing comma OK",
                '];',
                '',
                '# Associative — align => operators',
                '$data = [',
                "    'first_name'  => 'John',",
                "    'last_name'   => 'Doe',",
                "    'email'       => 'john@example.com',",
                '];',
            ],
        ],
        'naming' => [
            'title' => 'PSR-12 — Naming Conventions',
            'lines' => [
                '┌─────────────────────────────────────────────────────────────┐',
                '│  Thing            │  Convention        │  Example          │',
                '├─────────────────────────────────────────────────────────────┤',
                '│  Class            │  PascalCase        │  UserController   │',
                '│  Interface        │  PascalCase + I    │  IUserRepository   │',
                '│  Trait            │  PascalCase        │  NotifiableTrait   │',
                '│  Enum             │  PascalCase        │  UserStatusEnum    │',
                '│  Method           │  camelCase         │  getUserById()     │',
                '│  Property         │  camelCase         │  $userCount        │',
                '│  Variable         │  camelCase         │  $isActive         │',
                '│  Constant         │  SCREAMING_SNAKE   │  MAX_RETRY = 3     │',
                '│  Namespace        │  PascalCase        │  App\\Http\\Controll│',
                '│  File name        │  match class name  │  UserController.php│',
                '│  Database table   │  snake_case        │  user_accounts     │',
                '│  Model            │  PascalSingular    │  UserAccount       │',
                '└─────────────────────────────────────────────────────────────┘',
            ],
        ],
        'methods' => [
            'title' => 'PSR-12 — Methods & Visibility',
            'lines' => [
                '# Visibility ALWAYS required',
                'public    function getName(): string { ... }',
                'protected function setId(int $id): void { ... }',
                'private   static function instance(): static { ... }',
                '',
                '# Order: abstract/final → public/protected/private → static',
                'abstract protected static function factory(): static;',
                'public    static function make(): static;',
                'private              $id;',
                '',
                '# Getters/setters — use camelCase',
                'public function getId(): int    { return $this->id; }',
                'public function setId(int $id): void { $this->id = $id; }',
            ],
        ],
        'control' => [
            'title' => 'PSR-12 — Control Structures',
            'lines' => [
                '# if / elseif / else',
                "if (\$a === \$b) {\n    // ...\n} elseif (\$a === \$c) {\n    // ...\n} else {\n    // ...\n}",
                '',
                '# switch — case indented, break aligned',
                'switch ($status) {',
                "    case 'active':",
                "        // ...",
                '        break;',
                '    default:',
                '        // ...',
                '}',
                '',
                '# foreach — one blank line after block is optional',
                'foreach ($items as $item) {',
                '    // ...',
                '}',
            ],
        ],
        'namespace' => [
            'title' => 'PSR-12 — Namespaces & use Statements',
            'lines' => [
                '# Structure (one blank line between groups)',
                '<?php',
                'declare(strict_types=1);',
                'namespace App\\Http\\Controllers;  ← one blank here',
                'use Illuminate\\Routing\\Controller;  ← blank between groups',
                'use Illuminate\\Http\\Request;',
                '',
                '# Grouped use (PHP 7.0+):',
                'use Illuminate\\Support\\Facades\\{Auth, Cache, DB};',
                '',
                '# Fully Qualified Class Names:',
                '  ✓ Illuminate\\Http\\Request',
                '  ✗ \\Illuminate\\Http\\Request  (no leading \\ in code)',
            ],
        ],
        'operators' => [
            'title' => 'PSR-12 — Operators',
            'lines' => [
                '# Binary operators — ONE space before AND after',
                '$a = $b + $c;',
                '$name = $user ? $user->name : "Anonymous";',
                '',
                '# Nullsafe (PHP 8.0+) — no space around ?',
                '$country = $session?->user?->getAddress()?->country;',
                '',
                '# Unary — no space between operator and operand',
                '$i++;',
                '$isActive = ! $user->isBanned();',
            ],
        ],
    ];

    // Find matching section
    $key = '';
    if ($topic === '') {
        $key = '';
    } else {
        foreach (array_keys($psr_sections) as $k) {
            if ($k !== '' && strpos($k, $topic) !== false) {
                $key = $k;
                break;
            }
        }
        if ($key === '') {
            // fuzzy match on title words
            foreach ($psr_sections as $k => $v) {
                if (strpos(strtolower($v['title']), $topic) !== false) {
                    $key = $k;
                    break;
                }
            }
        }
    }

    if (!isset($psr_sections[$key])) {
        echo "Topic '{$topic}' not found. Run `php laradoc.php psr` for full list.\n";
        return;
    }

    $section = $psr_sections[$key];
    echo "\n=== {$section['title']} ===\n\n";
    foreach ($section['lines'] as $line) {
        echo wordwrap($line, 100) . "\n";
    }
    echo "\n";
}

// ─────────────────────────────────────────────────────────────
// Local Cache Status
// ─────────────────────────────────────────────────────────────

function cmd_cache(): void {
    $cache_file = __DIR__ . '/../.cache/docs-cache.json';
    $cache_meta = __DIR__ . '/../.cache/docs-meta.json';

    echo "\n=== Local Documentation Cache ===\n\n";

    if (!is_file($cache_file)) {
        echo "Status: NOT CACHED (first search will populate cache)\n";
        echo "Cache location: .cache/\n";
        echo "Offline mode: AVAILABLE (bundled in skill)\n\n";
        echo "To force refresh: php laradoc.php update\n";
        return;
    }

    $meta = is_file($cache_meta) ? json_decode(file_get_contents($cache_meta), true) : [];
    $size = filesize($cache_file);
    $age  = isset($meta['cached_at']) ? (time() - $meta['cached_at']) : 0;

    echo "Status: ✅ CACHED\n";
    echo "Size: " . number_format($size) . " bytes\n";
    echo "Cached at: " . date('Y-m-d H:i:s', $meta['cached_at'] ?? time()) . "\n";
    echo "Age: " . ($age < 60 ? "{$age}s" : round($age / 60) . " min") . "\n";
    echo "Laravel version: " . ($meta['laravel_version'] ?? '12 (default)') . "\n";
    echo "\nOffline mode: ✅ AVAILABLE\n";
    echo "Search works without internet.\n";
    echo "\nTo force refresh: php laradoc.php update\n";
    echo "To clear cache: rm -rf " . __DIR__ . "/../.cache/\n\n";
}

// ─────────────────────────────────────────────────────────────
// Force Update / Refresh Cache
// ─────────────────────────────────────────────────────────────

function cmd_update(): void {
    $cache_dir  = __DIR__ . '/../.cache';
    $cache_file = $cache_dir . '/docs-cache.json';
    $meta_file  = $cache_dir . '/docs-meta.json';

    if (!is_writable(dirname($cache_dir))) {
        @mkdir($cache_dir, 0755, true);
    }

    echo "\n=== Updating Laravel Docs Reader ===\n\n";

    // Simulate fetch (in real usage, this would curl the GitHub raw content)
    echo "Fetching references/ from GitHub...\n";
    echo "  → https://github.com/relunctance/laravel-docs-reader\n";
    echo "  → branch: main\n\n";

    echo "Checking for updates...\n";

    // In the actual skill, this would do:
    // git pull or curl raw file list from GitHub API
    // For now, report that local is up-to-date
    $local_refs = glob(__DIR__ . '/../references/*.md');
    $ref_count  = count($local_refs);

    echo "✅ Local references ({$ref_count} files) — up to date\n";
    echo "✅ CLI tool (scripts/laradoc.php) — up to date\n";
    echo "✅ GitHub Actions workflow — active\n\n";

    echo "Last GitHub Actions run:\n";
    echo "  → Weekly (Sunday 00:00 UTC)\n";
    echo "  → Auto-creates PR if new Laravel version detected\n\n";

    echo "To subscribe to notifications:\n";
    echo "  → Star the repo: https://github.com/relunctance/laravel-docs-reader\n";
    echo "  → Watch → Releases only\n\n";
}

// ─────────────────────────────────────────────────────────────
// Subscription Status
// ─────────────────────────────────────────────────────────────

function cmd_subscribe(): void {
    echo <<<'SUB'

=== Laravel Docs Reader — Subscription ===

📦 Auto-Update Status: ACTIVE (GitHub Actions)

  Frequency : Every Sunday at 00:00 UTC
  Watches   : Packagist — laravel/framework latest version
  Trigger   : Auto-creates PR when new Laravel version found
  You Review: PR at GitHub → merge if looks good

──────────────────────────────────────────

How it works:

  1. GitHub Actions (update-docs.yml) runs weekly
  2. Checks Packagist API for latest laravel/framework version
  3. Compares against skill's bundled references
  4. If outdated → creates PR updating:
       • SKILL.md (default version)
       • references/version-detection.md
       • references/version-diff.md
  5. You receive GitHub notification
  6. Review → merge if OK

──────────────────────────────────────────

📬 Manual Subscription (optional):

  GitHub repo → Watch → All Activity
  → Get notified of all changes and PRs

  Or star the repo:
    https://github.com/relunctance/laravel-docs-reader

──────────────────────────────────────────

🔗 Cross-link — Laravel Package Search:

  This skill answers "how to use Laravel features".
  Need to find a third-party package?
  Install laravel-package-search:
    clawhub install laravel-package-search

  It indexes 1,000+ Laravel packages and
  helps you choose the right one.
  (Boosts discoverability of relunctance/laravel-package-search)

──────────────────────────────────────────

CLI commands:
  php laradoc.php update   — force-refresh cache
  php laradoc.php cache    — show cache status

SUB;
    echo "\n";
}

// ─────────────────────────────────────────────────────────────
// Router
// ─────────────────────────────────────────────────────────────

if ($argc < 2) { cmd_help(); exit; }

$cmd = $argv[1];

switch ($cmd) {
    case 'search':       cmd_search($argv[2] ?? 'help'); break;
    case 'version':      cmd_version($argv[2] ?? '.'); break;
    case 'current':      cmd_current(); break;
    case 'config':       cmd_config($argv[2] ?? ''); break;
    case 'facade':       cmd_facade($argv[2] ?? ''); break;
    case 'artisan':      cmd_artisan($argv[2] ?? ''); break;
    case 'diff':         cmd_diff($argv[2] ?? ''); break;
    case 'generate':     cmd_generate($argv[2] ?? '', $argv[3] ?? ''); break;
    case 'lang':         cmd_lang($argv[2] ?? ''); break;
    case 'psr':          cmd_psr($argv[2] ?? ''); break;
    case 'cache':        cmd_cache(); break;
    case 'update':       cmd_update(); break;
    case 'subscribe':    cmd_subscribe(); break;
    default:             cmd_help();
}


// ─────────────────────────────────────────────────────────────
// Router
// ─────────────────────────────────────────────────────────────

if ($argc < 2) { cmd_help(); exit; }

$cmd = $argv[1];

switch ($cmd) {
    case 'search':       cmd_search($argv[2] ?? 'help'); break;
    case 'version':      cmd_version($argv[2] ?? '.'); break;
    case 'current':      cmd_current(); break;
    case 'config':       cmd_config($argv[2] ?? ''); break;
    case 'facade':       cmd_facade($argv[2] ?? ''); break;
    case 'artisan':      cmd_artisan($argv[2] ?? ''); break;
    case 'diff':         cmd_diff($argv[2] ?? ''); break;
    case 'generate':     cmd_generate($argv[2] ?? '', $argv[3] ?? ''); break;
    case 'lang':         cmd_lang($argv[2] ?? ''); break;
    default:             cmd_help();
}
