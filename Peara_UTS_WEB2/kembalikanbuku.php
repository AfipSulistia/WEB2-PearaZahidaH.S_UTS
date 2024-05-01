<?php
require_once "liblary.php";
require_once "book.php";
require_once "references.php";
session_start();

if (!isset($_SESSION['liblary'])) {
    $_SESSION['liblary'] = new Liblary();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['returnBook'])) {
        $isbn = $_POST['isbn'];

        $book = $_SESSION['liblary']->findBookByISBN($isbn);

        if ($book) {
            $book->returnBook();
            $_SESSION['liblary']->saveToSession();
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Dea Meilani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body class="bg-gray-100">
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand text-light" href="#">Peara</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active text-light" aria-current="page" href="index.php">Home</a>
        </li>
    
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Menu
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="tambahbuku.php">Tambah Buku</a></li>
            <li><a class="dropdown-item" href="kembalikanbuku.php">Kembalikan Buku</a></li>
            
          </ul>
        </li>
       
      </ul>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" name="keyword" aria-label="Search">
        <button class="btn btn-outline-success" type="submit" id="button-addon2">Search</button>
      </form>
    </div>
  </div>
</nav>   

    <div class="container mx-auto my-5" id="main-container">
               <div class="card my-3">
            <div class="card-header">
                Kembalikan Buku
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <label for="kembalikanISBN">Buku</label>
                    <select class="form-select mb-3" aria-label="Default select example" name="isbn" id="kembalikanISBN" required>
                        <?php
                        foreach ($_SESSION['liblary']->getBooks() as $book) {
                            if ($book->isBorrowed()) {
                                echo "<option value='" . $book->getISBN() . "'>" . $book->getTitle() . "</option>";
                            }
                        } ?>
                    </select>
                    <button type="submit" name="returnBook" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
   </body>

</html>


