<?php
session_start();
include 'db.php';

// Admin Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === 'Admin' && $password === 'Admin') {
        $_SESSION['admin'] = true;
    } else {
        echo "<script>alert('Invalid credentials!');</script>";
    }
}

// Handle Quiz Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_quiz'])) {
    $quizTitle = $_POST['quiz_title'];
    
    // Insert the new quiz title
    $stmt = $conn->prepare("INSERT INTO quizzes (title) VALUES (?)");
    $stmt->bind_param("s", $quizTitle);
    $stmt->execute();
    $quizId = $stmt->insert_id; // Get the last inserted quiz ID
    $stmt->close();

    // Insert each question for the quiz
    for ($i = 1; $i <= 5; $i++) {
        $question = $_POST["question$i"];
        $optionA = $_POST["q${i}option1"];
        $optionB = $_POST["q${i}option2"];
        $optionC = $_POST["q${i}option3"];
        $optionD = $_POST["q${i}option4"]; 
        $correct = $_POST["q${i}correct"];
        
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $quizId, $question, $optionA, $optionB, $optionC, $optionD, $correct);
        $stmt->execute();
        $stmt->close();
    }

    // Set session for SweetAlert message
    $_SESSION['quiz_success'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <title>Admin Panel - SmartQuiz</title>
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .admin-container {
            background-color: white;
            color: #333;
            padding: 30px;
            margin: auto;
            margin-top: 50px;
            box-shadow: 0px 0px 10px black;
            max-width: 600px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- SweetAlert2 for Quiz Success -->
<?php if (isset($_SESSION['quiz_success'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Quiz Saved Successfully!',
            text: 'Redirecting to homepage...',
            timer: 3000, // 3 seconds delay before redirect
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'index.html'; // Redirect to homepage
        });
    </script>
    <?php unset($_SESSION['quiz_success']); // Unset session variable after alert ?>
<?php endif; ?>

<div class="container admin-container animate__animated animate__fadeInDown">
    <?php if (!isset($_SESSION['admin'])): ?>
        <h2 class="text-center">Admin Login</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
        </form>
    <?php else: ?>
        <h2 class="text-center">Create Quiz</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="quiz_title" class="form-label">Quiz Title</label>
                <input type="text" class="form-control" name="quiz_title" required>
            </div>
            <div id="questionsContainer">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="mb-3">
                        <label><strong>Question <?= $i ?> :</strong></label>
                        <input type="text" class="form-control" name="question<?= $i ?>" required>
                        <label><strong>Options:</strong></label>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="q<?= $i ?>correct" value="A" required>
                            <input type="text" class="form-control d-inline w-75" name="q<?= $i ?>option1" placeholder="Option A" required>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="q<?= $i ?>correct" value="B" required>
                            <input type="text" class="form-control d-inline w-75" name="q<?= $i ?>option2" placeholder="Option B" required>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="q<?= $i ?>correct" value="C" required>
                            <input type="text" class="form-control d-inline w-75" name="q<?= $i ?>option3" placeholder="Option C" required>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="q<?= $i ?>correct" value="D" required>
                            <input type="text" class="form-control d-inline w-75" name="q<?= $i ?>option4" placeholder="Option D" required>
                        </div>
                    </div>
                    <hr>
                <?php endfor; ?>
            </div>
            <button type="submit" name="create_quiz" class="btn btn-dark w-100 mt-3">Save Quiz</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
