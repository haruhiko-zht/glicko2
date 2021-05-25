<?php


namespace HaruhikoZHT\Glicko2\Math;


/**
 * Interface MathOpponentInterface
 * @package HaruhikoZHT\Glicko2\Math
 *
 * This interface is support MathInterface.
 * When calculating the Glicko-2 rating, the parameters v, Δ, μ' are required,
 * but these are required for each opponent's μ, φ, s, and Σ must be calculated.
 * MathOpponentInterface will support when calculating these.
 */
interface MathOpponentInterface
{
    /**
     * @return float opponent's μ
     */
    public function mu(): float;

    /**
     * @return float opponent's φ
     */
    public function phi(): float;

    /**
     * @return float s
     */
    public function s(): float;
}