<?php


namespace HaruhikoZHT\Glicko2;


interface MatchesInterface
{
    public function getPlayers(): array;

    public function getResults(): array;

    public function getTau(): float;
}