<?php

require_once __DIR__ . '/vendor/autoload.php';

use HaruhikoZHT\Glicko2\Player;
use HaruhikoZHT\Glicko2\Matches;
use HaruhikoZHT\Glicko2\MatchResult;
use HaruhikoZHT\Glicko2\Calculator;

// Create player list, key(ex. uuid) and value(Player instance) format.
$players = [
    'foo' => new Player(rd: 200),
    'bar' => new Player(rating: 1400, rd: 30, sigma: 0.06),
    'baz' => new Player(1550, 100, 0.06),
    'qux' => new Player(1700, 300, 0.06),
    'quux' => new Player(2000, 100, 0.06),  // no match player
];

echo '==========BEFORE==========', PHP_EOL;
foreach ($players as $name => $player) {
    $rating = $player->getRating();
    $rd = $player->getRD();
    $sigma = $player->getSigma();
    echo sprintf('[%s] rating:%f, RD:%f, σ:%f', $name, $rating, $rd, $sigma), PHP_EOL;
}

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

echo '==========AFTER==========', PHP_EOL;
foreach ($players as $name => $player) {
    $rating = $player->getRating();
    $rd = $player->getRD();
    $sigma = $player->getSigma();
    echo sprintf('[%s] rating:%f, RD:%f, σ:%f', $name, $rating, $rd, $sigma), PHP_EOL;
}
