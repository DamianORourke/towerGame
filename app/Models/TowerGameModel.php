<?php

namespace App\Models;

use CodeIgniter\Model;

class TowerGameModel extends Model
{
    protected $table      = 'games';          // The table to use
    protected $primaryKey = 'game_id';         // The primary key of the table
    protected $returnType = 'array';           // Return the results as an array
    protected $allowedFields = ['state'];      // Allowed fields to be inserted/updated

    // Method to get the game state by game ID
    public function getGameState($game_id)
    {
        return $this->where('game_id', $game_id)->first();
    }

    // Method to create a new game
    public function createNewGame($state)
    {
        return $this->insert(['state' => $state]);
    }

    // Method to update the game state
    public function updateGameState($game_id, $state)
    {
        return $this->update($game_id, ['state' => $state]);
    }
}
