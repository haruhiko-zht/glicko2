<?php


namespace HaruhikoZHT\Glicko2;


/**
 * Class MatchResult
 * @package HaruhikoZHT\Glicko2
 */
class MatchResult
{
    protected PlayerInterface $player;
    protected float $player_score;
    protected PlayerInterface $opponent;
    protected float $opponent_score;

    /**
     * MatchResult constructor.
     * @param PlayerInterface $player
     * @param float $player_score
     * @param PlayerInterface $opponent
     * @param float $opponent_score
     */
    public function __construct(
        PlayerInterface $player,
        float $player_score,
        PlayerInterface $opponent,
        float $opponent_score
    ) {
        $this->player = $player;
        $this->player_score = $player_score;
        $this->opponent = $opponent;
        $this->opponent_score = $opponent_score;
    }

    /**
     * @param PlayerInterface $player
     * @param float $player_score
     * @param PlayerInterface $opponent
     * @param float $opponent_score
     * @return MatchResult
     */
    public static function create(
        PlayerInterface $player,
        float $player_score,
        PlayerInterface $opponent,
        float $opponent_score
    ): MatchResult {
        return (new static($player, $player_score, $opponent, $opponent_score));
    }

    /**
     * @return PlayerInterface
     */
    public function getPlayer(): PlayerInterface
    {
        return $this->player;
    }

    /**
     * @return float
     */
    public function getPlayerScore(): float
    {
        return $this->player_score;
    }

    /**
     * @return PlayerInterface
     */
    public function getOpponent(): PlayerInterface
    {
        return $this->opponent;
    }

    /**
     * @return float
     */
    public function getOpponentScore(): float
    {
        return $this->opponent_score;
    }
}