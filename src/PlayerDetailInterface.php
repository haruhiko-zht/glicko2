<?php


namespace HaruhikoZHT\Glicko2;


/**
 * Interface PlayerDetailInterface
 * @package HaruhikoZHT\Glicko2
 *
 * PlayerDetailInterface is internal wrapper for PlayerInterface.
 */
interface PlayerDetailInterface
{
    /**
     * @return PlayerInterface
     */
    public function getPlayer(): PlayerInterface;

    /**
     * @param PlayerInterface $opponent
     * @param float $s s (= match result)
     */
    public function addResult(PlayerInterface $opponent, float $s): void;

    /**
     * @return array
     */
    public function getResults(): array;
}