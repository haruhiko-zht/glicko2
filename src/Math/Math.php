<?php


namespace HaruhikoZHT\Glicko2\Math;


/**
 * Class Math
 * @package HaruhikoZHT\Glicko2\Math
 */
class Math implements MathInterface
{
    /**
     * SCALING_FACTOR
     */
    public const SCALING_FACTOR = 173.7178;

    /**
     * @var float rating of unrated player
     */
    protected float $unrated;

    /**
     * Math constructor.
     * @param float $unrated rating of unrated player
     */
    public function __construct(float $unrated = 1500.0)
    {
        $this->unrated = $unrated;
    }

    public function mu(float $r): float
    {
        return (($r - $this->unrated) / static::SCALING_FACTOR);
    }

    public function phi(float $RD): float
    {
        return ($RD / static::SCALING_FACTOR);
    }

    public function s(float $player_score, float $opponent_score): float
    {
        return
            match ($player_score <=> $opponent_score) {
                -1 => 0.0,
                0 => 0.5,
                1 => 1.0
            };
    }

    public function g(float $phi): float
    {
        return (1 / sqrt(1 + ((3 * ($phi ** 2)) / M_PI ** 2)));
    }

    public function E(float $player_mu, float $opponent_mu, float $opponent_phi): float
    {
        return (1 / (1 + exp((-$this->g($opponent_phi)) * ($player_mu - $opponent_mu))));
    }


    public function v(float $player_mu, array $opponents): float
    {
        $v_reciprocal = 0;

        /**
         * @var MathOpponentInterface[] $opponents
         */
        foreach ($opponents as $opponent) {
            $g = $this->g($opponent->phi());
            $E = $this->E($player_mu, $opponent->mu(), $opponent->phi());
            $v_reciprocal += ($g ** 2) * $E * (1 - $E);
        }

        return (1 / $v_reciprocal);
    }

    public function delta(float $player_mu, array $opponents): float
    {
        $tmp = 0;

        /**
         * @var MathOpponentInterface[] $opponents
         */
        foreach ($opponents as $opponent) {
            $g = $this->g($opponent->phi());
            $s = $opponent->s();
            $E = $this->E($player_mu, $opponent->mu(), $opponent->phi());
            $tmp += $g * ($s - $E);
        }
        $v = $this->v($player_mu, $opponents);
        return ($v * $tmp);
    }

    public function a(float $sigma): float
    {
        return log($sigma ** 2);
    }

    public function equation(float $delta, float $phi, float $v, float $sigma, float $tau): callable
    {
        $a = $this->a($sigma);
        return static fn($x
        ): float => ((((M_E ** $x) * ($delta ** 2 - $phi ** 2 - $v - M_E ** $x)) / (2 * ($phi ** 2 + $v + M_E ** $x) ** 2)) - (($x - $a) / $tau ** 2));
    }

    public function B(float $delta, float $phi, float $v, float $sigma, float $tau): float
    {
        $b = log(($delta ** 2) - ($phi ** 2) - $v);
        $f = $this->equation($delta, $phi, $v, $sigma, $tau);

        if ($delta ** 2 <= ($phi ** 2 + $v)) {
            $k = 1;
            $a = $this->a($sigma);
            while ($f($a - ($k * $tau)) < 0) {
                $k++;
            }
            $b = $a - ($k * $tau);
        }

        return $b;
    }

    public function new_sigma(
        float $delta,
        float $phi,
        float $v,
        float $sigma,
        float $tau,
        float $epsilon = 0.000001
    ): float {
        $A = $this->a($sigma);  // A = a = ln(σ^2)
        $B = $this->B($delta, $phi, $v, $sigma, $tau);
        $f = $this->equation($delta, $phi, $v, $sigma, $tau);

        $f_A = $f($A);
        $f_B = $f($B);

        while (abs($B - $A) > $epsilon) {
            // (a)
            $C = $A + ((($A - $B) * $f_A) / ($f_B - $f_A));
            $f_C = $f($C);

            // (b)
            if (($f_C * $f_B) < 0) {
                $A = $B;
                $f_A = $f_B;
            } else {
                $f_A /= 2;
            }

            // (c)
            $B = $C;
            $f_B = $f_C;
        }

        return M_E ** ($A / 2);
    }

    public function pre_phi(float $phi, float $new_sigma): float
    {
        return sqrt(($phi ** 2) + ($new_sigma ** 2));
    }

    public function new_phi(float $phi, float $new_sigma, float $v): float
    {
        return (1 / sqrt((1 / ($this->pre_phi($phi, $new_sigma) ** 2)) + (1 / $v)));
    }

    public function new_mu(float $player_mu, float $new_phi, array $opponents): float
    {
        $tmp = 0;

        /**
         * @var MathOpponentInterface[] $opponents
         */
        foreach ($opponents as $opponent) {
            $g = $this->g($opponent->phi());
            $s = $opponent->s();
            $E = $this->E($player_mu, $opponent->mu(), $opponent->phi());
            $tmp += $g * ($s - $E);
        }

        return ($player_mu + (($new_phi ** 2) * $tmp));
    }

    public function new_r(float $new_mu): float
    {
        return ((static::SCALING_FACTOR * $new_mu) + $this->unrated);
    }

    public function new_RD(float $new_phi): float
    {
        return (static::SCALING_FACTOR * $new_phi);
    }

    public function new_RD_without_matching(float $phi, float $sigma): float
    {
        return (static::SCALING_FACTOR * $this->pre_phi($phi, $sigma));
    }
}