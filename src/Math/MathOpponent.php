<?php


namespace HaruhikoZHT\Glicko2\Math;


/**
 * Class MathOpponent
 * @package HaruhikoZHT\Glicko2\Math
 */
class MathOpponent implements MathOpponentInterface
{
    /**
     * @var float opponent's μ
     */
    protected float $mu;

    /**
     * @var float opponent's φ
     */
    protected float $phi;

    /**
     * @var float s
     */
    protected float $s;

    /**
     * MathOpponent constructor.
     * @param float $mu opponent's μ
     * @param float $phi opponent's φ
     * @param float $s s
     */
    public function __construct(float $mu, float $phi, float $s)
    {
        $this->mu = $mu;
        $this->phi = $phi;
        $this->s = $s;
    }

    public function mu(): float
    {
        return $this->mu;
    }

    public function phi(): float
    {
        return $this->phi;
    }

    public function s(): float
    {
        return $this->s;
    }
}