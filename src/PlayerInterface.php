<?php


namespace HaruhikoZHT\Glicko2;


interface PlayerInterface
{
    public function getRating(): float;

    public function getRD(): float;

    public function getSigma(): float;

    public function getPrevious(): ?PlayerInterface;
}