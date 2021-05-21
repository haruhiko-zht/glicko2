<?php


namespace HaruhikoZHT\Glicko2;


interface PlayerInterface
{
    public function getRating(): float;

    public function getRd(): float;

    public function getSigma(): float;

    public function getPrevious(): ?PlayerInterface;
}