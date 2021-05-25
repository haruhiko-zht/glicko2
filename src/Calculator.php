<?php


namespace HaruhikoZHT\Glicko2;


use HaruhikoZHT\Glicko2\Math\Math;
use HaruhikoZHT\Glicko2\Math\MathInterface;
use HaruhikoZHT\Glicko2\Math\MathOpponent;

class Calculator
{
    /**
     * @var MatchesInterface
     */
    protected MatchesInterface $matches;

    /**
     * @var MathInterface
     */
    protected MathInterface $math;

    /**
     * @var PlayerDetailInterface[]
     */
    protected array $join_players_detail = [];

    /**
     * Calculator constructor.
     * @param MatchesInterface $matches
     * @param MathInterface|null $math
     */
    public function __construct(MatchesInterface $matches, ?MathInterface $math = null)
    {
        $this->matches = $matches;
        $this->math = $math ?? new Math();
    }

    public function updateRating(): array
    {
        $this->addResultsToPlayers();

        $new_players = [];
        foreach ($this->join_players_detail as $player_detail) {
            $new_players[] = $this->calcRating($player_detail);
        }

        $joins = array_map(static fn($player_detail) => $player_detail->getPlayer(), $this->join_players_detail);
        $not_joins = array_udiff($this->matches->getPlayers(), $joins, static fn($a, $b) => ($a === $b) ? 0 : -1);

        foreach ($not_joins as $not_join) {
            $player_detail = new PlayerDetail($not_join);
            $new_players[] = $this->calcRating($player_detail);
        }

        foreach ($new_players as $player) {
            $res = $player->getPrevious()?->update($player);
        }

        return $this->matches->getPlayers();
    }

    protected function addResultsToPlayers(): void
    {
        foreach ($this->matches->getResults() as $result) {
            $player_s = $this->math->s($result->getPlayerScore(), $result->getOpponentScore());
            $opponent_s = $this->math->s($result->getOpponentScore(), $result->getPlayerScore());

            $filtered = array_filter(
                $this->join_players_detail,
                static fn($player_detail) => $player_detail->getPlayer() === $result->getPlayer()
            );

            if (empty($filtered)) {
                $tmp = new PlayerDetail($result->getPlayer());
                $tmp->addResult($result->getOpponent(), $player_s);
                $this->join_players_detail[] = $tmp;
            } else {
                $filtered[0]->addResult($result->getOpponent(), $player_s);
            }

            $filtered = array_filter(
                $this->join_players_detail,
                static fn($player_detail) => $player_detail->getPlayer() === $result->getOpponent()
            );

            if (empty($filtered)) {
                $tmp = new PlayerDetail($result->getOpponent());
                $tmp->addResult($result->getPlayer(), $opponent_s);
                $this->join_players_detail[] = $tmp;
            } else {
                $filtered[0]->addResult($result->getPlayer(), $opponent_s);
            }
        }
    }

    protected function calcRating(PlayerDetailInterface $player_detail): PlayerInterface
    {
        $player = $player_detail->getPlayer();

        if (empty($player_detail->getResults())) {
            $phi = $this->math->phi($player->getRD());
            $new_RD = $this->math->new_RD_without_matching($phi, $player->getSigma());
            return (new Player($player->getRating(), $new_RD, $player->getSigma(), $player));
        }

        /** @var PlayerResult $result */
        $mathOpponents = array_map(
            fn($result) => new MathOpponent(
                $this->math->mu($result->getOpponent()->getRating()),
                $this->math->phi($result->getOpponent()->getRD()),
                $result->getS()
            ),
            $player_detail->getResults()
        );

        // Step 2
        $mu = $this->math->mu($player->getRating());
        $phi = $this->math->phi($player->getRD());

        // Step 3
        $v = $this->math->v($mu, $mathOpponents);

        // Step 4
        $delta = $this->math->delta($mu, $mathOpponents);

        // Step 5
        $new_sigma = $this->math->new_sigma($delta, $phi, $v, $player->getSigma(), $this->matches->getTau());

        // Step 6
        $pre_phi = $this->math->pre_phi($phi, $new_sigma);

        // Step 7
        $new_phi = $this->math->new_phi($phi, $new_sigma, $v);
        $new_mu = $this->math->new_mu($mu, $new_phi, $mathOpponents);

        // Step 8
        $new_r = $this->math->new_r($new_mu);
        $new_RD = $this->math->new_RD($new_phi);

        return (new Player($new_r, $new_RD, $new_sigma, $player));
    }
}