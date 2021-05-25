<?php


namespace HaruhikoZHT\Glicko2\Math;


/**
 * Interface MathInterface
 * @package HaruhikoZHT\Glicko2\Math
 */
interface MathInterface
{
    /**
     * Step 2
     * @param float $r rating
     * @return float μ
     */
    public function mu(float $r): float;

    /**
     * Step 2
     * @param float $RD
     * @return float φ
     */
    public function phi(float $RD): float;

    /**
     * Step 2
     * @param float $player_score
     * @param float $opponent_score
     * @return float s
     */
    public function s(float $player_score, float $opponent_score): float;

    /**
     * Step 3
     * @param float $phi φ
     * @return float g
     */
    public function g(float $phi): float;

    /**
     * Step 3
     * @param float $player_mu player's μ
     * @param float $opponent_mu opponent's μ
     * @param float $opponent_phi opponent's φ
     * @return float E
     */
    public function E(float $player_mu, float $opponent_mu, float $opponent_phi): float;

    /**
     * Step 3
     * @param float $player_mu player's μ
     * @param MathOpponentInterface[] $opponents
     * @return float v
     */
    public function v(float $player_mu, array $opponents): float;

    /**
     * Step 4
     * @param float $player_mu player's μ
     * @param MathOpponentInterface[] $opponents
     * @return float Δ
     */
    public function delta(float $player_mu, array $opponents): float;

    /**
     * Step 5-1
     * @param float $sigma σ
     * @return float A = a = ln(σ^2)
     */
    public function a(float $sigma): float;

    /**
     * Step 5-1
     * @param float $delta Δ
     * @param float $phi φ
     * @param float $v v
     * @param float $sigma σ
     * @param float $tau τ
     * @return callable f(x), you can use `$f = $this->equation(...)` and $f($x)
     */
    public function equation(float $delta, float $phi, float $v, float $sigma, float $tau): callable;

    /**
     * Step 5-2
     * @param float $delta Δ
     * @param float $phi φ
     * @param float $v v
     * @param float $sigma σ
     * @param float $tau τ
     * @return float B
     */
    public function B(float $delta, float $phi, float $v, float $sigma, float $tau): float;

    /**
     * Step 5-5
     * @param float $delta Δ
     * @param float $phi φ
     * @param float $v v
     * @param float $sigma σ
     * @param float $tau τ
     * @param float $epsilon ε
     * @return float σ' (= new σ)
     */
    public function new_sigma(
        float $delta,
        float $phi,
        float $v,
        float $sigma,
        float $tau,
        float $epsilon = 0.000001
    ): float;

    /**
     * Step 6
     * @param float $phi φ
     * @param float $new_sigma σ'
     * @return float φ*
     */
    public function pre_phi(float $phi, float $new_sigma): float;

    /**
     * Step 7
     * @param float $phi φ
     * @param float $new_sigma σ'
     * @param float $v v
     * @return float φ'
     */
    public function new_phi(float $phi, float $new_sigma, float $v): float;

    /**
     * Step 7
     * @param float $player_mu player's μ
     * @param float $new_phi φ'
     * @param MathOpponentInterface[] $opponents
     * @return float μ'
     */
    public function new_mu(float $player_mu, float $new_phi, array $opponents): float;

    /**
     * Step 8
     * @param float $new_mu μ'
     * @return float r' (= new rating)
     */
    public function new_r(float $new_mu): float;

    /**
     * Step 8
     * @param float $new_phi φ'
     * @return float RD' (= new RD)
     */
    public function new_RD(float $new_phi): float;

    /**
     * @param float $phi φ
     * @param float $sigma σ
     * @return float RD' (= new RD when no matching)
     */
    public function new_RD_without_matching(float $phi, float $sigma): float;
}