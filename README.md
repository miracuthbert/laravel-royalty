# Laravel Royalty

A user points package for Laravel that can be used to give rewards, loyalty or experience points with real time support.

## How does it work?

Simply every point has an action file which when called will resolve the model for a given point.

The action file is used to assign points to a user.

The reason we use action (files) is:

- It makes it easy to track points.
- It's easier to switch out points eg. you want to bump up the points a user gets on completing a lesson from `50` to `100`, you just create a new action and you replace the old one.

## Installation

Use composer to install the package:

```
composer require miracuthbert/laravel-royalty
```

## Setup

The package takes advantage of Laravel Auto-Discovery, so it doesn't require you to manually add the ServiceProvider.

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`

```php
Miracuthbert\Royalty\RoyaltyServiceProvider::class
```

You must then publish the `config` and `migrations` file.

### Using the Setup command

You can do all the necessary setup using the `royalty:setup` in your command console

```
php artisan royalty:setup
```

> Update the keys in "config/royalty.php" before migrating your database 

If you want to reset either `config` or `migrations` use the commands below in your console

### Publish Config

```
php artisan vendor:publish --provider=Miracuthbert\Royalty\RoyaltyServiceProvider --tag=royalty-config
```

> Setup the user `model` key in config to indicate which User model to use

### Publish Migrations

```
php artisan vendor:publish --provider=Miracuthbert\Royalty\RoyaltyServiceProvider --tag=royalty-migrations
```

> Before migrating the database make sure you setup the user `model` key in config

### Publish Vue Components

A simple Vue component is included to display a user's points in real-time anytime they are given points. See [Real-time](#real-time) section under usage for more.

```
php artisan vendor:publish --provider=Miracuthbert\Royalty\RoyaltyServiceProvider --tag=royalty-components
```

> The package does not tie you to use a specific front-end framework to listen to the fired event, so feel free to experiment 

## Usage

### Setting the User model

First, setup the user `model` key in the config.

Then add `CollectsPoints` trait in the respective model.

```php
use CollectsPoints;
```

### Creating Points

Let's take an example of a user completing a Lesson in a course, we can create a `CompletedLesson`.

#### Using Console Command

You can use the command:

```
php artisan royalty:action CompletedLesson

// with specific namespace
php artisan royalty:action Course\\CompletedLesson
```

This will create an action file under the `Royalty\Actions` folder in the `app` directory 
or a corresponding one as specified in the `config/royalty.php` file. 

It will include:

- `key` method with the unique key which will be used to identify created from the slug of the `Action` name.

You can also use these options along with the command:

- `--key` to override the key generated from class name.
- `--name` to create the point in the database (use along with the `points` option).
- `--points` the points for the given action.
- `--description` the description of the point.

> If you just created the point file only you need to create a record with reference to the action in the database.

See [Adding Points in the Database](#adding-points-in-the-database) section on adding a point in the database.

#### Creating Manually

To create a point manually you need to create an `action` file and also a `record` referencing the action in the database.

##### Creating the Action File

To create a point action file, you need to create a class that extends `Miracuthbert\Royalty\Actions\ActionAbstract`.

```php
namespace App\Royalty\Actions;

use Miracuthbert\Royalty\Actions\ActionAbstract;

class CompletedLesson extends ActionAbstract
{
    /**
     * Set the action key.
     *
     * @return mixed
     */
    public function key()
    {
        return 'completed-lesson';
    }
}
```

##### Adding Points in the Database

```php
use Miracuthbert\Royalty\Models\Point;

$point = Point::create([
    'name' => 'Completed Lesson',
    'key' => 'completed-lesson',
    'description' => 'Reward for completing a lesson',
    'points' => 100,
]);
```

You can also create bulk points using, for example a seeder:

```php
$points = [
    [
        'name' => 'Completed Lesson',
        'key' => 'completed-lesson',
        'points' => 100,
    ],
    [
        'name' => 'Completed Course',
        'key' => 'completed-course',
        'points' => 500,
    ],
    
    // grouped
    [
        'name' => 'Grades',
        'key' => 'grades',
        'points' => 100,
        'children' => [
            [
                'name' => 'Excellent',
                'points' => 100,
            ],
            [
                'name' => 'Very Good',
                'points' => 90,
            ],
            [
                'name' => 'Good',
                'points' => 80,
            ],
        ],
    ],
];

foreach ($points as $point) {
    $exists = Point::where('key', $point['key'])->first();

    if (!$exists) {
        Miracuthbert\Royalty\Models\Point::create($point);
    }
} 
```

### Giving Points

To give points, just call the `givePoints` method on an instance of a user.

```php
// user instance
$user = User::find(1);
$user->givePoints(new CompletedLesson());

// using request user
$request->user()->givePoints(new CompletedLesson());

// using auth user
auth()->user()->givePoints(new CompletedLesson());
```

### Getting User's Points

To get user points, just call the `points` method on an instance of a user chaining one of the following methods:

- `number`: The raw points value
- `number`: For a formatted number value, i.e `1,000`, `1,000,000`
- `shorthand`: For a formatted string value, i.e `1k`, `10.5k`, `1m`

```php
// user instance
$user = User::find(1);
$user->points()->number();
$user->points()->shorthand();

// using request user
$request->user()->points()->number();
$request->user()->points()->shorthand();

// using auth user
auth()->user()->points()->number();
auth()->user()->points()->shorthand();
```

### Real-time

Whenever a user is given points a `PointsGiven` event is fired.

#### Broadcast Channel

It broadcasts to the `users` (private) channel as set in the `channel` key of `broadcast` in `config/royalty.php`.

> The channel should exist in `channels` file under routes or your respective user channel

An example of the channel route:

```php
Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

#### Listening to the Event

You can then listen to it using the `points-given` or the set value of the `name` key under `broadcast` in `config/royalty.php`.

Example using Laravel Echo with Vue.js:

```vue
    Echo.private(`users.${this.userId}`)
        .listen('.points-given', (e) => {
            this.point = e.point
            this.userPoints = e.user_points
        })
```

##### Vue Component

There is a Vue component included with the package. Use command below to publish it:

```
php artisan vendor:publish --provider=Miracuthbert\Royalty\RoyaltyServiceProvider --tag=royalty-components
```

The published component will be placed in your `resources/js/components` directory. Once the components have been published, you should register them in your `resources/js/app.js` file:

```
Vue.component('royalty-badge', require('./components/royalty/RoyaltyBadge.vue').default);
```

After registering the component, make sure to run `npm run dev` to recompile your assets. Once you have recompiled your assets, you may drop the components into one of your application's templates to get started:

```
<royalty-badge
  :user-id="{{ auth()->user()->id }}"
  initial-points="{{ auth()->user()->points()->shorthand() }}"
/>
```

## Console Commands

There are three commands within the package:

- `royalty:setup`: Used to setup the package files
- `royalty:action`: Used to create an action file and point
- `royalty:actions`: Used to list points and their related actions

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to Cuthbert Mirambo via [miracuthbert@gmail.com](mailto:miracuthbert@gmail.com). All security vulnerabilities will be promptly addressed.

## Credits

- [Cuthbert Mirambo](https://github.com/miracuthbert)

## License

The project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
