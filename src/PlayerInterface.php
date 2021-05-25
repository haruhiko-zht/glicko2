<?php


namespace HaruhikoZHT\Glicko2;


/**
 * Interface PlayerInterface
 * @package HaruhikoZHT\Glicko2
 */
interface PlayerInterface
{
    /**
     * @return float player rating
     */
    public function getRating(): float;

    /**
     * @return float RD (= rating deviation)
     */
    public function getRD(): float;

    /**
     * @return float σ (= volatility)
     */
    public function getSigma(): float;

    /**
     * @return PlayerInterface|null previous
     */
    public function getPrevious(): ?PlayerInterface;

    /**
     * @param PlayerInterface $player
     * @return PlayerInterface
     */
    public function update(PlayerInterface $player): PlayerInterface;
}