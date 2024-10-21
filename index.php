<?php
session_start();

function generateCards($numPairs)
{
    $emojis = ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔', '🐧', '🐦', '🦆', '🦉'];
    shuffle($emojis);
    $cards = array_slice($emojis, 0, $numPairs);
    $cards = array_merge($cards, $cards);
    shuffle($cards);
    return $cards;
}

if (!isset($_SESSION['level'])) {
    $_SESSION['level'] = 1;
}

if (isset($_POST['action']) && $_POST['action'] == 'nextLevel') {
    $_SESSION['level']++;
}

$level = $_SESSION['level'];
$numPairs = min(4 + $level, 10);
$cards = generateCards($numPairs);
$gridSize = ceil(sqrt(count($cards)));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLICKO - STAGE: <?php echo $level; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #2d2d2d;
            color: #f0f0f0;
            
        }

        .game-container {
            text-align: center;
            background-color: #3d3d3d;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        h1, h2 {
            margin: 0;
            font-weight: normal;
        }

        h1 {
            font-size: 2em;
            color: #f0f0f0;
        }
        a{
            color:#fdfdfd
        }
        h2 {
            font-size: 1em;
            font-weight: 100;
            color: #f5b227;
        }
        footer{
            display: flex;
            width: 100%;
            justify-content: center;
            align-items: center;
            padding-bottom:5vh;
          
        }
        nav{
            display:flex;
            width: 100%;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-top: 5vh;
          
        }

        .game-board {
            display: grid;
            grid-template-columns: repeat(<?php echo $gridSize; ?>, 1fr);
            gap: 10px;
            margin-top: 20px;
        }

        .card {
            width: 80px;
            height: 80px;
            background-color: #4b4b4b;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2em;
            color: #fff;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .card.flipped {
            background-color: #f5b227;
            color: #2d2d2d;
        }

        .card.matched {
            background-color: #5bb450;
            color: #2d2d2d;
        }

        .card.notmatch {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0% { transform: translate(1px, 1px) rotate(0deg); }
            10% { transform: translate(-1px, -2px) rotate(-1deg); }
            20% { transform: translate(-3px, 0px) rotate(1deg); }
            30% { transform: translate(3px, 2px) rotate(0deg); }
            40% { transform: translate(1px, -1px) rotate(1deg); }
            50% { transform: translate(-1px, 2px) rotate(-1deg); }
            60% { transform: translate(-3px, 1px) rotate(0deg); }
            70% { transform: translate(3px, 1px) rotate(-1deg); }
            80% { transform: translate(-1px, -1px) rotate(1deg); }
            90% { transform: translate(1px, 2px) rotate(0deg); }
            100% { transform: translate(1px, -2px) rotate(-1deg); }
        }

        #moves, #timer, #level {
            margin-top: 20px;
            font-size: 1.2em;
            color: #f0f0f0;
        }

        #nextLevel {
            display: none;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1em;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #nextLevel:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>
    <nav>
    <h1>Find the matching Pair</h1>
    <h2>Although you might not</h2>
    </nav>
    <div class="game-container">
        
        <div id="level">Level: <?php echo $level; ?></div>
        <div class="game-board">
            <?php foreach ($cards as $index => $card): ?>
                <div class="card" data-card="<?php echo $card; ?>"></div>
            <?php endforeach; ?>
        </div>
        <div id="moves">Moves: 0</div>
        <div id="timer">Time: 0s</div>
        <button id="nextLevel">Next Level</button>
    </div>
    <footer><a href="https://www.adithyakrishnan.com?ref=findthepair">©fal3n-4ngel</a></footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.card');
            let hasFlippedCard = false;
            let lockBoard = false;
            let firstCard, secondCard;
            let moves = 0;
            let matches = 0;
            let startTime;
            let timerInterval;

            function flipCard() {
                if (lockBoard) return;
                if (this === firstCard) return;

                this.classList.add('flipped');
                this.textContent = this.dataset.card;

                if (!hasFlippedCard) {
                    hasFlippedCard = true;
                    firstCard = this;
                    if (moves === 0) startTimer();
                    return;
                }

                secondCard = this;
                checkForMatch();
            }

            function checkForMatch() {
                let isMatch = firstCard.dataset.card === secondCard.dataset.card;
                isMatch ? disableCards() : unflipCards();
                moves++;
                document.getElementById('moves').textContent = `Moves: ${moves}`;
            }

            function disableCards() {
                firstCard.removeEventListener('click', flipCard);
                secondCard.removeEventListener('click', flipCard);
                firstCard.classList.add('matched');
                secondCard.classList.add('matched');
                matches++;
                if (matches === cards.length / 2) {
                    clearInterval(timerInterval);
                    setTimeout(() => {
                        alert(`Congratulations! You completed Level ${<?php echo $level; ?>} in ${moves} moves and ${Math.floor((Date.now() - startTime) / 1000)} seconds!`);
                        document.getElementById('nextLevel').style.display = 'inline-block';
                    }, 500);
                }
                resetBoard();
            }

            function unflipCards() {
                lockBoard = true;
                setTimeout(() => {
                    firstCard.classList.remove('flipped');
                    secondCard.classList.remove('flipped');
                    firstCard.classList.add('notmatch');
                    secondCard.classList.add('notmatch');
                    firstCard.textContent = '';
                    secondCard.textContent = '';
                    resetBoard();
                }, 1000);
            }

            function resetBoard() {
                [hasFlippedCard, lockBoard] = [false, false];
                [firstCard, secondCard] = [null, null];
            }

            function startTimer() {
                startTime = Date.now();
                timerInterval = setInterval(() => {
                    const elapsedTime = Math.floor((Date.now() - startTime) / 1000);
                    document.getElementById('timer').textContent = `Time: ${elapsedTime}s`;
                }, 1000);
            }

            cards.forEach(card => {
                card.addEventListener('click', flipCard);
                const randomPos = Math.floor(Math.random() * cards.length);
                card.style.order = randomPos;
            });

            document.getElementById('nextLevel').addEventListener('click', () => {
                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=nextLevel'
                }).then(() => {
                    window.location.reload();
                });
            });
        });
    </script>
</body>

</html>
