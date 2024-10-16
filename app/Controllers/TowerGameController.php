<?php

namespace App\Controllers;

use App\Models\TowerGameModel;
use CodeIgniter\RESTful\ResourceController;

class TowerGameController extends ResourceController
{
    protected $TowerGameModel;

    public function __construct()
    {
        $this->TowerGameModel = new TowerGameModel();
        helper('form');
    }

// Show the view to start a new game or continue playing if the game has started
public function showNewGameForm()
{
    $game_id = session()->get('game_id'); // Fetch game_id from session

    if ($game_id) {
        // Fetch game state if a game has started
        $game = $this->TowerGameModel->getGameState($game_id);
        $game['state'] = json_decode($game['state'], true); // Decode the game state

        // Pass messages along with the game state
        return view('new_game', [
            'game' => $game,
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
            'warning' => session()->getFlashdata('warning'),
        ]);
    }

    return view('new_game'); // No game started yet
}



    // POST: Start a new game
    public function newGame()
    {
        // Default game state: Rod A has all disks, B and C are empty
        $asc = [7, 6, 5, 4, 3, 2, 1];
        $dec = [1,2,3,4,5,6,7];
        $initialState = json_encode([
            'A' => $asc, // 7 is the largest, 1 is the smallest
            'B' => [],
            'C' => []
        ]);

        // Create a new game and save it to the database
        $game_id = $this->TowerGameModel->createNewGame($initialState);

        // Store the game ID in the session for persistent access
        session()->set('game_id', $game_id);

        // Redirect to the game UI with the new game
        return redirect()->to('/tower')->with('game_id', $game_id);
    }

    // POST: Move a disk from one rod to another
    // POST: Move a disk from one rod to another
public function moveDisk($game_id)
{
    $input = $this->request->getPost();

    // Validate input
    if (!isset($input['from']) || !isset($input['to'])) {
        return $this->reloadGameWithMessage($game_id, 'error', 'Invalid move. Both "from" and "to" must be selected.');
    }

    // Get the game state
    $game = $this->TowerGameModel->getGameState($game_id);
    if (!$game) {
        return $this->reloadGameWithMessage($game_id, 'error', 'Game not found.');
    }

    $tower = json_decode($game['state'], true);
    $from = $input['from'];
    $to = $input['to'];

    // Check if the 'from' rod has any disks
    if (empty($tower[$from])) {
        return $this->reloadGameWithMessage($game_id, 'warning', 'No disks to move from rod ' . $from . '.');
    }

    // Check if the move is valid
    $disk = end($tower[$from]);
    if (!empty($tower[$to]) && end($tower[$to]) < $disk) {
        return $this->reloadGameWithMessage($game_id, 'warning', 'Cannot move a larger disk onto a smaller disk.');
    }

    // Perform the move
    array_pop($tower[$from]);
    array_push($tower[$to], $disk);

    // Update the game state in the database
    $this->TowerGameModel->updateGameState($game_id, json_encode($tower));

    // Check for a win condition: All disks are on rod C (or another rod)
    if (empty($tower['A']) && (empty($tower['B']) || empty($tower['C']))) {
        session()->remove('game_id'); // Clear the game session on win
        return redirect()->to('/tower')->with('success', 'Congratulations! You have successfully completed the Tower of Hanoi!');
    }

    // Reload the game UI with the updated state
    return redirect()->to('/tower')->with('game_id', $game_id);
}

    // Helper function to reload game with error/warning message and retain game state
    private function reloadGameWithMessage($game_id, $type, $message)
    {
        // Fetch the current game state
        $game = $this->TowerGameModel->getGameState($game_id);
        if (!$game) {
            return redirect()->to('/tower')->with('error', 'Game not found.');
        }

        $game['state'] = json_decode($game['state'], true);

        // Reload the view with the message and game state
        return view('new_game', [
            'game' => $game,
            $type => $message // Pass either 'warning' or 'error' based on the type
        ]);
    }
}
