haruhiko-zht/glicko2
===

[Glicko-2 rating system](http://glicko.net/glicko.html) implements by PHP

**This library is making and not stable, so still don't use.**

## Description

This is Glicko-2 rating system calculator.

There are several libraries that calculate Glicko-2 rating (even in the PHP, of course), but I often find it
inconvenient to use, so I decided to create one myself by modern PHP.

I have little knowledge of PHP and programming, but I believe it is possible to go from 0 to 1.

## Requirement

- PHP 8.x

## Usage

```php
use HaruhikoZHT\Glicko2\Player;
use HaruhikoZHT\Glicko2\Matches;
use HaruhikoZHT\Glicko2\MatchResult;
use HaruhikoZHT\Glicko2\Calculator;

// Create player list, key (something you can distinguish, such as uuid) and value (Player instance) format.
$players = [
    'foo' => new Player(rd: 200),
    'bar' => new Player(rating: 1400, rd: 30, sigma: 0.06),
    'baz' => new Player(1550, 100, 0.06),
    'qux' => new Player(1700, 300, 0.06),
    'quux' => new Player(2000, 100, 0.06),  // no matching player
];

// Create match results.
$results = [
    new MatchResult(player: $players['foo'], player_score: 80, opponent: $players['bar'], opponent_score: 20),
    new MatchResult($players['foo'], 60, $players['baz'], 90),
    new MatchResult($players['foo'], 30, $players['qux'], 100),
];

// Create Matches instance and add information.
$matches = new Matches();
$matches->addPlayers($players);
$matches->addResults($results);

// Prepare Calculator. Calculate rate and update.
$calculator = new Calculator($matches);
$calculator->updateRating();

// After update rating
// [foo] rating:1464.050671, RD:151.516524, σ:0.059996
// [bar] rating:1398.143558, RD:31.670215, σ:0.059999
// [baz] rating:1570.394740, RD:97.709169, σ:0.059999
// [qux] rating:1784.421790, RD:251.565565, σ:0.059999
// [quux] rating:2000.000000, RD:100.541734, σ:0.060000
```

## Feature

I want to be able to calculate Glicko-2 rates from results that don't involve direct confrontation (such as ranking by
score).

## LICENSE

This library is released under the MIT License, see LICENSE.

## Author

[haruhiko-zht](https://github.com/haruhiko-zht)
