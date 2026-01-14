# Practical Refactoring Guide

This guide provides **copy-paste ready** fixes for the critical issues identified in your project.

---

## 🔴 Immediate Fixes (Critical)

### Fix 1: Remove Migrations from Request Lifecycle

**File:** `src/Application.php`

```php
<?php

namespace Elmasry;

use Elmasry\Http\Request;
use Elmasry\Http\Response;
use Elmasry\Http\Route;
use Elmasry\Support\Config;
use Elmasry\Support\Session;
use Elmasry\Database\Database;

class Application
{
    protected $route;
    protected $request;
    protected $response;
    protected $config;
    public $session;

    public function __construct()
    {
        $this->request = new Request;
        $this->response = new Response;
        $this->session = new Session;
        $this->route = new Route($this->request, $this->response);
        $this->config = new Config($this->loadConfigurations());
        
        // ✅ REMOVED: initializeDatabase() - migrations should be CLI only
        // Just establish connection
        Database::connection();
    }

    public function config()
    {
        return $this->config;
    }

    protected function loadConfigurations()
    {
        foreach (scandir(config_path()) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filename = explode('.', $file)[0];
            yield $filename => require config_path() . $file;
        }
    }

    public function run()
    {
        $this->route->resolve();
    }

    public function db(): \PDO
    {
        return Database::connection();
    }
}
```

### Fix 2: CLI Migration Command

**File:** `commands` (update existing)

```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Elmasry\Database\Migrator;
use Elmasry\Database\Database;
use App\Seeders\DatabaseSeeder;

// Establish connection
Database::connection();

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'migrate':
        $migrator = new Migrator();
        $result = $migrator->migrate();
        echo formatResult($result);
        break;
        
    case 'migrate:rollback':
        $steps = (int)($argv[2] ?? 1);
        $migrator = new Migrator();
        $result = $migrator->rollback($steps);
        echo formatResult($result);
        break;
        
    case 'migrate:refresh':
        $migrator = new Migrator();
        $result = $migrator->refresh();
        echo formatResult($result);
        break;
        
    case 'migrate:status':
        $migrator = new Migrator();
        $status = $migrator->status();
        foreach ($status as $migration) {
            $badge = $migration['status'] === 'Ran' ? '✓' : '○';
            echo "{$badge} {$migration['migration']} [{$migration['status']}]\n";
        }
        break;
        
    case 'db:seed':
        $seeder = new DatabaseSeeder();
        $seeder->run();
        echo "✓ Database seeded successfully!\n";
        break;
        
    case 'migrate:fresh':
        echo "⚠️  Dropping all tables...\n";
        $migrator = new Migrator();
        $migrator->reset();
        echo "Running migrations...\n";
        $result = $migrator->migrate();
        echo formatResult($result);
        echo "Seeding database...\n";
        $seeder = new DatabaseSeeder();
        $seeder->run();
        echo "✓ Fresh migration complete!\n";
        break;
        
    default:
        echo <<<HELP
Usage: php commands <command>

Available commands:
  migrate           Run pending migrations
  migrate:rollback  Rollback the last migration batch
  migrate:refresh   Rollback all and re-run migrations
  migrate:fresh     Drop all tables, migrate, and seed
  migrate:status    Show migration status
  db:seed           Run database seeders
  
HELP;
}

function formatResult(array $result): string
{
    $output = "[{$result['status']}] {$result['message']}\n";
    
    if (!empty($result['migrated'])) {
        foreach ($result['migrated'] as $m) {
            $output .= "  ✓ {$m}\n";
        }
    }
    
    if (!empty($result['rolled_back'])) {
        foreach ($result['rolled_back'] as $m) {
            $output .= "  ↩ {$m}\n";
        }
    }
    
    return $output;
}
```

### Fix 3: Remove Debug Code from Validator

**File:** `src/Validation/Validator.php`

```php
<?php

namespace Elmasry\Validation;

class Validator
{
    protected array $errors = [];
    protected array $rules = [];
    protected array $data = [];
    protected array $aliases = [];
    protected ErrorBag $errorBag;

    public function make($data)
    {
        $this->data = $data;
        $this->errorBag = new ErrorBag;
        $this->validate();
    }

    protected function validate()
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = is_string($ruleString) ? explode('|', $ruleString) : $ruleString;
            $value = $this->data[$field] ?? null;
            
            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
    }

    protected function applyRule(string $field, $value, string $rule): void
    {
        // Parse rule name and parameters
        $parameters = [];
        if (str_contains($rule, ':')) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $parameters = explode(',', $paramString);
        }

        $passed = match($rule) {
            'required' => !empty($value) || $value === '0',
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'min' => strlen($value) >= (int)($parameters[0] ?? 0),
            'max' => strlen($value) <= (int)($parameters[0] ?? PHP_INT_MAX),
            'numeric' => is_numeric($value),
            'confirmed' => $value === ($this->data[$field . '_confirmation'] ?? null),
            default => true,
        };

        if (!$passed) {
            $this->addError($field, $this->getMessage($rule, $field, $parameters));
        }
    }

    protected function addError(string $field, string $message): void
    {
        $this->errorBag->errors[$field][] = $message;
    }

    protected function getMessage(string $rule, string $field, array $params = []): string
    {
        $field = $this->alias($field);
        
        $messages = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least {$params[0]} characters.",
            'max' => "The {$field} must not exceed {$params[0]} characters.",
            'numeric' => "The {$field} must be a number.",
            'confirmed' => "The {$field} confirmation does not match.",
        ];

        return $messages[$rule] ?? "The {$field} field is invalid.";
    }

    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    public function passes(): bool
    {
        return empty($this->errors());
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors($key = null)
    {
        return $key 
            ? ($this->errorBag->errors[$key] ?? [])
            : $this->errorBag->errors;
    }

    public function alias($field)
    {
        return $this->aliases[$field] ?? str_replace('_', ' ', $field);
    }

    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }
}
```

### Fix 4: Fix the `old()` Helper Bug

**File:** `src/Support/helper.php`

Replace the buggy function:

```php
if (!function_exists('old')) {
    function old($key, $default = '')
    {
        $old = app()->session->getFlash('old');
        
        if ($old && isset($old[$key])) {
            return htmlspecialchars($old[$key], ENT_QUOTES, 'UTF-8');
        }
        
        return $default;
    }
}
```

### Fix 5: Add XSS Protection Helper

**File:** `src/Support/helper.php` (add these)

```php
if (!function_exists('e')) {
    /**
     * Escape HTML special characters in a string.
     */
    function e($value): string
    {
        if ($value === null) {
            return '';
        }
        
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get or generate CSRF token.
     */
    function csrf_token(): string
    {
        $session = app()->session;
        
        if (!$session->has('_token')) {
            $session->set('_token', bin2hex(random_bytes(32)));
        }
        
        return $session->get('_token');
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('method_field')) {
    /**
     * Generate a form method field for PUT/PATCH/DELETE.
     */
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response.
     */
    function redirect(string $url): never
    {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * Throw an HTTP exception.
     */
    function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        $view = match($code) {
            404 => 'errors/404',
            403 => 'errors/403',
            500 => 'errors/500',
            default => 'errors/error',
        };
        
        view($view, ['code' => $code, 'message' => $message]);
        exit;
    }
}

if (!function_exists('auth')) {
    /**
     * Get authenticated user data.
     */
    function auth(): ?array
    {
        $session = app()->session;
        
        if (!$session->get('is_authenticated', false)) {
            return null;
        }
        
        return [
            'id' => $session->get('user_id'),
            'name' => $session->get('user_name'),
            'email' => $session->get('user_email'),
        ];
    }
}

if (!function_exists('guest')) {
    /**
     * Check if user is a guest (not authenticated).
     */
    function guest(): bool
    {
        return auth() === null;
    }
}
```

### Fix 6: Move `redirect()` Function Out of Controller

**File:** `App/Controller/LoginController.php`

Remove these lines from the end of the file:
```php
// ❌ DELETE THESE LINES (127-131)
// Helper function for redirect
function redirect($url) {
    header("Location: $url");
    exit;
}
```

The `redirect()` function is now in `helper.php`.

---

## 🟡 Namespace Fixes

### Fix 7: Correct Validation Namespaces

**File:** `src/Validation/RulesMapper.php`

```php
<?php

namespace Elmasry\Validation;  // ✅ Fixed namespace

use Elmasry\Validation\Rules\EmailRule;
use Elmasry\Validation\Rules\UniqueRule;
use Elmasry\Validation\Rules\BetweenRule;
use Elmasry\Validation\Rules\AlphaNumRule;
use Elmasry\Validation\Rules\RequiredRule;
use Elmasry\Validation\Rules\ConfirmedRule;

trait RulesMapper
{
    protected static array $map = [
        'required' => RequiredRule::class,
        'alnum' => AlphaNumRule::class,
        'between' => BetweenRule::class,
        'email' => EmailRule::class,
        'confirmed' => ConfirmedRule::class,
        'unique' => UniqueRule::class,
    ];

    public static function resolve(string $rule, $options)
    {
        return new static::$map[$rule](...$options);
    }
}
```

**File:** `src/Validation/Rules/Contract/Rule.php`

```php
<?php

namespace Elmasry\Validation\Rules\Contract;  // ✅ Fixed namespace

interface Rule extends \Stringable
{
    public function apply($field, $value, $data = []);
}
```

**File:** `src/Validation/Rules/Required.php`

```php
<?php

namespace Elmasry\Validation\Rules;  // ✅ Fixed namespace

use Elmasry\Validation\Rules\Contract\Rule;

class RequiredRule implements Rule
{
    public function apply($field, $value, $data = [])
    {
        return !empty($value) || $value === '0' || $value === 0;
    }

    public function __toString(): string
    {
        return 'The %s field is required.';
    }
}
```

### Fix 8: Controller Naming Convention

Rename file: `App/Controller/productController.php` → `App/Controller/ProductController.php`

```php
<?php

namespace App\Controller;

// ✅ Class name matches file name with PascalCase
class ProductController
{
    // ... rest of the class
}
```

---

## 🟢 Add CSRF Middleware

### New File: `src/Http/Middleware/VerifyCsrfToken.php`

```php
<?php

namespace Elmasry\Http\Middleware;

use Elmasry\Http\Request;

class VerifyCsrfToken
{
    protected array $except = [
        // Add routes to exclude from CSRF verification
        '/webhook/*',
    ];

    public function handle(Request $request, \Closure $next)
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        if (!$this->tokensMatch($request)) {
            http_response_code(419);
            die('CSRF token mismatch. Please refresh and try again.');
        }

        return $next($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        // Skip GET, HEAD, OPTIONS
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        // Check exclusions
        $path = $request->path();
        foreach ($this->except as $pattern) {
            if ($this->matchesPattern($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    protected function tokensMatch(Request $request): bool
    {
        $sessionToken = app()->session->get('_token', '');
        $requestToken = $request->get('_token') 
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        return hash_equals($sessionToken, $requestToken);
    }

    protected function matchesPattern(string $pattern, string $path): bool
    {
        if (str_contains($pattern, '*')) {
            $pattern = str_replace('*', '.*', $pattern);
            return preg_match("#^{$pattern}$#", $path) === 1;
        }

        return $pattern === $path;
    }
}
```

---

## 🟢 Enhanced Response Class

**File:** `src/Http/Response.php` (replace entire file)

```php
<?php

namespace Elmasry\Http;

class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $content = '';

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function json($data, int $status = 200): self
    {
        $this->statusCode = $status;
        $this->headers['Content-Type'] = 'application/json';
        $this->content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function redirect(string $url, int $status = 302): never
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    public function download(string $path, string $name = null): never
    {
        $name ??= basename($path);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Length: ' . filesize($path));
        
        readfile($path);
        exit;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        echo $this->content;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
```

---

## 🟢 Simple Dependency Injection Container

**New File:** `src/Container/Container.php`

```php
<?php

namespace Elmasry\Container;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use Exception;

class Container
{
    private static ?Container $instance = null;
    
    protected array $bindings = [];
    protected array $instances = [];

    /**
     * Get the global container instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set the global container instance.
     */
    public static function setInstance(?Container $container): void
    {
        self::$instance = $container;
    }

    /**
     * Register a binding.
     */
    public function bind(string $abstract, $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?? $abstract;
    }

    /**
     * Register a shared binding (singleton).
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, function ($c) use ($abstract, $concrete) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $c->build($concrete ?? $abstract);
            }
            return $this->instances[$abstract];
        });
    }

    /**
     * Register an existing instance.
     */
    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve a binding.
     */
    public function make(string $abstract): mixed
    {
        // Return existing instance if we have one
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Get concrete implementation
        $concrete = $this->bindings[$abstract] ?? $abstract;

        // If it's a closure, execute it
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        // Build the concrete class
        return $this->build($concrete);
    }

    /**
     * Build a concrete instance with auto-wiring.
     */
    public function build(string $concrete): mixed
    {
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            // No type hint - try default value
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception(
                        "Cannot resolve parameter [{$parameter->getName()}] in class {$concrete}"
                    );
                }
                continue;
            }

            // Recursively resolve typed dependency
            $dependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Check if a binding exists.
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Forget a binding.
     */
    public function forget(string $abstract): void
    {
        unset($this->bindings[$abstract], $this->instances[$abstract]);
    }

    /**
     * Flush all bindings and instances.
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
    }
}
```

### Update `helper.php` to use Container:

```php
if (!function_exists('app')) {
    function app($abstract = null)
    {
        $container = \Elmasry\Container\Container::getInstance();
        
        if ($abstract === null) {
            // Return the application instance
            if (!$container->has('app')) {
                $container->instance('app', new \Elmasry\Application);
            }
            return $container->make('app');
        }
        
        return $container->make($abstract);
    }
}

if (!function_exists('resolve')) {
    function resolve(string $abstract): mixed
    {
        return app($abstract);
    }
}
```

---

## Quick Reference: File Changes Summary

| File | Action | Priority |
|------|--------|----------|
| `src/Application.php` | Remove `initializeDatabase()` from constructor | 🔴 Critical |
| `commands` | Add proper CLI migration commands | 🔴 Critical |
| `src/Validation/Validator.php` | Remove `var_dump`, implement actual validation | 🔴 Critical |
| `src/Support/helper.php` | Fix `old()` bug, add `e()`, `csrf_*()` helpers | 🔴 Critical |
| `App/Controller/LoginController.php` | Remove `redirect()` function definition | 🟡 High |
| `src/Validation/RulesMapper.php` | Fix namespace to `Elmasry\Validation` | 🟡 High |
| `src/Validation/Rules/*` | Fix all namespaces | 🟡 High |
| `App/Controller/productController.php` | Rename to `ProductController.php` | 🟡 High |
| `src/Http/Response.php` | Replace with enhanced version | 🟢 Medium |
| `src/Container/Container.php` | Create new file | 🟢 Medium |
| `src/Http/Middleware/VerifyCsrfToken.php` | Create new file | 🟢 Medium |

---

*Apply these fixes in order of priority (🔴 → 🟡 → 🟢) to progressively improve your framework.*
