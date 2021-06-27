<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# DataBase

### for handle 1 million record, I am used:

> 1 : eloquent transaction around your query(s).

> 2 : `brokenice/laravel-mysql-partition` composer package for create partition by range of status.
> ###### source: https://github.com/lucabecchetti/laravel-mysql-partition

# Accessor

#### use Accessor for create `full_name` attribute into provider model.

#### use Accessor for create `name` attribute into job model as `$job->name`.

#### use Accessor for create `providerList` attribute into job model as `$job->providerList`.

# Speed

#### for more speed I do these action

* singleton design pattern for JobFechted jobfethe class, test with `phpUnit`:
    ```php
    namespace Tests\Unit;
    
    use App\Helpers\jobFechted;
    use App\Models\Job;use PHPUnit\Framework\TestCase;
    
    class SingletonTest extends TestCase
    {
        public function testUniqueness()
        {
            $firstCallJob = jobFechted::getInstance('Fetched job id: ' . Job::first()->id);
            $secondCallJob = jobFechted::getInstance('Fetched job id: ' . Job::first()->id);
    
            $this->assertInstanceOf(jobFechted::class, $firstCallJob);
            $this->assertSame($firstCallJob, $secondCallJob);
        }
    }
    ```
  for more flexibility for `App\Helpers\jobFechted` :
    ```php
    final class jobFechted implements .......\Mail\Mailable
    ```
* lazy collection for `$jobs`
    ```php
    $jobs = LazyCollection::make(function () use ($jobs) {
            yield $jobs;
        })->chunk(100)->each(function ($job) use ($user) {
            Mail::to($user->email)
            ->send(jobFechted::getInstance('Fetched job id: ' . $job->id));
        });
    ```
  ###### sounrce: https://laravel.com/docs/8.x/collections#lazy-collection-methods
* To get rid of this memory usage we can use the `cursor()` method. This method will allow you to iterate through your
  dataset records and will only execute a single query, Then we can use `lazy collection`:

    ```php
    $query = $user->jobs()->where('is_completed', true)->cursor()->remember();
    ```

# My answer

### Q1) We need to verify a condition on every HTTP request . What approach would you suggest to do so?

> we can use `CSRF_TOKEN`, `Middleware group` or `laravel passport`

### Q2) We want to use authentication. Which package would you recommend for authentication flow? Please note that we don't need any OAuth2.

> Ok, we can use `sentinel` agnostic fully-featured authentication.
> ###### source: https://github.com/cartalyst/sentinel

### Q3)We want to trigger a function each time an email is sent inside the application (not just the following method). What is your recommended solution?

> we need create event & listener around the `\Mail` class, or fire the event into the `AppServiceProviders.php` 

### Q4> Please provide a time estimation on this task, and when you deliver the project.
> about 4h.

# Security Tips
* Create policies for authorized user just work for own jobs
* Create middleware for allow authorized user to access to it action.

#recommendations
* if you want just `is_completed` jobs, we can use `laravel Global Scopes`
    ```php
    namespace App\Scopes;
    
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Scope;
    
    class JobScope implements Scope
    {
        public function apply(Builder $builder, Model $model)
        {
            $builder->where('is_completed', true);
        }
    }
    ```
  in `App\Models\Job`
  ```php 
      namespace App\Models;
    
    use App\Scopes\JobScope;
    use Illuminate\Database\Eloquent\Model;
    
    class User extends Model
    {
        protected static function booted()
        {
            static::addGlobalScope(new JobScope);
        }
    }  
  ```

