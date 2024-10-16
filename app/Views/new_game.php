<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Tower of Hanoi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>
        .disk {
            display: block;
            height: 30px;
            border-width: 1px;
            border-style: solid;
            padding: 0;
            margin: 0 auto;
            border-radius: 20px;
            text-align: center;
            font-size: 20px;
            line-height: 30px;
            color: #fff;
            align-self: flex-end;
            z-index: 5;
        }

        .disk-1 {
            width: 40px;
            background-color: #50DDE0;
            border-color: #50DDE0;
        }

        .disk-2 {
            width: 55px;
            /* Width for disk size 2 */
            background-color: #45A25E;
            /* Color for disk size 2 */
            border-color: #45A25E;
        }

        .disk-3 {
            width: 80px;
            /* Width for disk size 3 */
            background-color: #BC4732;
            /* Color for disk size 3 */
            border-color: #BC4732;
        }

        .disk-4 {
            width: 105px;
            background-color: #41656D;
            border-color: #41656D;
        }

        .disk-5 {
            width: 130px;
            /* Width for disk size 2 */
            background-color: #3C8CAE;
            /* Color for disk size 2 */
            border-color: #3C8CAE;
        }

        .disk-6 {
            width: 155px;
            /* Width for disk size 3 */
            background-color: #6B4054;
            /* Color for disk size 3 */
            border-color: #6B4054;
        }

        .disk-7 {
            width: 180px;
            /* Width for disk size 3 */
            background-color: #C3BFAF;
            /* Color for disk size 3 */
            border-color: #C3BFAF;
        }

        .rod {
            height: 220px;
            width: 220px;
            position: relative;
            margin: 0 10px;
            border-bottom: 1px solid black;
        }

        .rod::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%; /* Position the line in the center */
            width: 1px; /* Line thickness */
            background-color: #000; /* Line color */
            transform: translateX(-50%); /* Ensure the line is perfectly centered */
        }

        .labels,
        .rod {
            display: flex;
            flex-direction: column;
            justify-content: end;
            align-items: center;
        }

        .labels {
            margin: 10px 0;
            width: 220px;
            text-align: center;
        }

    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Tower of Hanoi</h1>
        
        <!-- Success, Warning, and Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php elseif (isset($warning)): ?>
            <div class="alert alert-warning">
                <?= $warning ?>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (isset($game)): ?>
            <div class="d-flex flex-row justify-content-between align-items-center">
                <p>Click and drag the disks to complete game!.</p>
                <div class="btn-group pe-5">
                    <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Settings
                    </button>
                    <ul class="dropdown-menu">
                    <li class="fw-bold text-end pe-2">Restart Game!</li>
                    <form action="/tower/new" method="post">
                        <li class="float-end pe-2"><button type="submit" class="btn btn-success">Start Again!</button></li>
                        </form>
                    </ul>
                </div>
            </div>
            <div class="d-flex justify-content-around mt-4">
                <div class="rod" id="rodA" ondragover="allowDrop(event)" ondrop="drop(event, 'A')">
                    <?php
                    $a = $game['state']['A'];
                    $revA = array_reverse($a);
                    foreach (array_reverse($game['state']['A']) as $disk): ?>
                        <div class="disk disk-<?= $disk ?>" draggable="true" ondragstart="drag(event, '<?= $disk ?>', 'A')">
                            <?= $disk ?></div> <!-- Assign class based on disk size -->
                    <?php endforeach; ?>
                </div>
                <div class="rod" id="rodB" ondragover="allowDrop(event)" ondrop="drop(event, 'B')">
                    <?php foreach (array_reverse($game['state']['B']) as $disk): ?>
                        <div class="disk disk-<?= $disk ?>" draggable="true" ondragstart="drag(event, '<?= $disk ?>', 'B')">
                            <?= $disk ?></div> <!-- Assign class based on disk size -->
                    <?php endforeach; ?>
                </div>
                <div class="rod" id="rodC" ondragover="allowDrop(event)" ondrop="drop(event, 'C')">
                    <?php foreach (array_reverse($game['state']['C']) as $disk): ?>
                        <div class="disk disk-<?= $disk ?>" draggable="true" ondragstart="drag(event, '<?= $disk ?>', 'C')">
                            <?= $disk ?></div> <!-- Assign class based on disk size -->
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="d-flex justify-content-around mb-4 w-100">
                <?php
                foreach ($game['state'] as $key => $value): ?>
                    <div class="labels">
                        <h4>Rod <?= $key ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Form to move disks -->
             <p>Manually move disks from Rod to Rod!</p>
            <form action="/tower/move/<?= $game['game_id'] ?>" method="post" id="moveForm" class="mt-4">
                <div class="form-group">
                    <label for="from">Move Disk From:</label>
                    <select name="from" id="from" class="form-control">
                        <option value="A">Rod A</option>
                        <option value="B">Rod B</option>
                        <option value="C">Rod C</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="to">Move Disk To:</label>
                    <select name="to" id="to" class="form-control">
                        <option value="A">Rod A</option>
                        <option value="B">Rod B</option>
                        <option value="C">Rod C</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-5">Move Disk</button>
            </form>
        <?php else: ?>
            <!-- Start New Game Button -->
            <p>Click the button below to start a new Tower of Hanoi game.</p>
            <form action="/tower/new" method="post">
                <button type="submit" class="btn btn-primary">Start New Game</button>
            </form>
        <?php endif; ?>
    </div>
    <script>

        let currentDisk = null; // Variable to store the currently dragged disk

        function allowDrop(event) {
            event.preventDefault(); // Prevent default behavior (Prevent it from opening as a link for some elements)
        }

        function drag(event, disk, fromRod) {
            currentDisk = { disk, fromRod }; // Store the disk and the rod it comes from
            event.dataTransfer.setData("text", disk); // Set data to be dragged
        }

        function drop(event, toRod) {
            event.preventDefault(); // Prevent default behavior
            const topDisk = parseInt(currentDisk.disk);
            const fromRod = currentDisk.fromRod;

            // Get the current state of the rods
            const currentState = getCurrentState();
            const fromState = currentState[fromRod]; // Get the current state from the source rod
            const toState = currentState[toRod]; // Get the current state from the target rod

            // Validate that we are moving the top disk
            if (fromState.length === 0 || fromState[fromState.length - 1] !== topDisk ) {
                alert("Invalid move: You can only move the top disk.");
                return;
            }

            // Validate that we are not placing a larger disk on a smaller one
            if (toState.length > 0 && toState[toState.length - 1] < topDisk ) {
                alert("Invalid move: Cannot place larger disk on smaller disk.");
                return;
            }

            // Move the disk
            moveDisk(fromRod, toRod, topDisk);

            // Optionally submit the form to update the server state
            document.getElementById('from').value = fromRod;
            document.getElementById('to').value = toRod;
            document.getElementById('moveForm').submit();
        }

        function moveDisk(fromRod, toRod, disk) {
            const fromElement = document.getElementById('rod' + fromRod);
            const toElement = document.getElementById('rod' + toRod);
            const diskElement = fromElement.querySelector(`.disk.disk-${disk}`);
            const toElements = document.querySelectorAll( `#rod${toRod} .disk` );
            console.log( 'toElements', toElements );
            if( toElements.length > 0 ){
                toElement.insertBefore( diskElement, toElements[0] );
            }else{
                toElement.appendChild( diskElement );
            }
            // Move the disk element to the new rod
            // fromElement.removeChild(diskElement);
            // toElement.appendChild(diskElement);
        }


        function getCurrentState() {
            const rods = {
                A: [],
                B: [],
                C: []
            };

            document.querySelectorAll('.rod').forEach(rod => {
                const rodId = rod.id.replace('rod', '');
                const disks = rod.querySelectorAll('.disk');

                // Collect disks from bottom to top
                disks.forEach(disk => {
                    const diskClass = disk.className;
                    const diskNum = diskClass.match(/disk-(\d+)/)[1];
                    rods[rodId].unshift(parseInt(diskNum)); // Unshift to keep the visual order
                });
            });

            return rods; // Return the current state of the rods
        }

        
    </script>
</body>

</html>