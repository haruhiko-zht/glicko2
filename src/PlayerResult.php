<?php


namespace HaruhikoZHT\Glicko2;


/**
 * Class PlayerResult
 * @package HaruhikoZHT\Glicko2
 */
class PlayerResult
{
    protected PlayerInterface $opponent;
    protected float $s;

    /**
     * PlayerResult constructor.
     * @param PlayerInterface $opponent
     * @param float $s s (= match result)
     */
    public function __construct(PlayerInterface $opponent, float $s)
    {
        $this->opponent = $opponent;
        $this->s = $s;
    }

    /**
     * @return PlayerInterface
     */
    public function getOpponent(): PlayerInterface
    {
        return $this->opponent;
    }

    /**
     * @return float s
     */
    public function getS(): float
    {
        return $this->s;
    }
}